/**
 * =============================================================================
 * FILE    : api.js
 * LAYER   : Frontend (axios instance dengan konfigurasi otomatis)
 * FUNGSI  : Setup axios instance dengan baseURL dan Authorization header otomatis
 * =============================================================================
 */

import axios from 'axios';

// Base URL untuk API (sesuaikan dengan environment)
const API_BASE_URL = window.location.origin + '/api';

// Buat instance axios dengan konfigurasi default
const api = axios.create({
  baseURL: API_BASE_URL,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
});

// Interceptor untuk menambahkan token JWT ke setiap request
api.interceptors.request.use(
  (config) => {
    // Ambil token dari localStorage
    const token = localStorage.getItem('access_token') || localStorage.getItem('token');
    
    // Jika token ada, tambahkan ke header Authorization
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    
    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

// Interceptor untuk menangani response error (401 Unauthorized)
api.interceptors.response.use(
  (response) => {
    return response;
  },
  (error) => {
    // Jika mendapat 401 Unauthorized, mungkin token expired atau tidak valid
    if (error.response && error.response.status === 401) {
      // Hapus token yang tidak valid
      localStorage.removeItem('access_token');
      localStorage.removeItem('token');
      
      // Redirect ke halaman login jika belum di halaman login
      if (!window.location.pathname.includes('/login')) {
        // Opsional: bisa redirect ke login atau tampilkan pesan
        console.warn('Session expired. Please login again.');
      }
    }
    
    return Promise.reject(error);
  }
);

// Export instance axios yang sudah dikonfigurasi
export default api;

