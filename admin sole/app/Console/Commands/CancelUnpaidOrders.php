<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use Illuminate\Support\Facades\Log;

class CancelUnpaidOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    // Ini adalah nama command yang akan dipanggil di terminal (misal: php artisan orders:cancel-unpaid)
    protected $signature = 'orders:cancel-unpaid';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cancel online orders that are unpaid for more than 2 hours';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Waktu sekarang dikurangi 2 jam
        $twoHoursAgo = now()->subHours(2);

        // Cari order yang memenuhi kriteria untuk dibatalkan
        $ordersToCancel = Order::where('type', 'online')
            ->where('payment_proof', null)
            ->where('created_at', '<=', $twoHoursAgo)
            ->get();

        if ($ordersToCancel->isEmpty()) {
            $this->info('No unpaid orders to cancel.');
            return;
        }

        foreach ($ordersToCancel as $order) {
            $order->status = 'cancelled';
            $order->save();

            // Log untuk debugging (opsional, tapi sangat membantu)
            Log::info("Order #{$order->id} has been cancelled due to no payment.");
        }

        $this->info("Successfully cancelled {$ordersToCancel->count()} unpaid orders.");
    }
}
