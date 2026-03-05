@extends('layouts.admin')

@section('page')
    Admin
@endsection

@section('content')
    <h1 class="text-2xl mb-3">Selamat datang di {{ Auth::user()->role === 'admin' ? 'Admin' : 'Owner' }} Dashboard</h1>

    <div class="grid grid-cols-4 gap-8">

    </div>
@endsection
