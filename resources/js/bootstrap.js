import axios from 'axios';
window.axios = axios;

// Configura la base URL per le API
window.axios.defaults.baseURL = 'http://127.0.0.1:8000/api';
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Interceptor per aggiungere automaticamente il token di autenticazione
window.axios.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('auth_token');
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

// Interceptor per gestire risposte non autorizzate
window.axios.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      // Token scaduto o non valido
      localStorage.removeItem('auth_token');
      localStorage.removeItem('user');
      window.location.href = '/login';
    }
    return Promise.reject(error);
  }
);
