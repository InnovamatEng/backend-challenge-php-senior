import axios from 'axios';

const apiClient = axios.create({
  baseURL: '/platform/api',
  headers: {
    'Content-Type': 'application/json',
  },
});

apiClient.interceptors.request.use((config) => {
  const token = localStorage.getItem('jwt_token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

apiClient.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401 && window.location.pathname !== '/') {
      localStorage.removeItem('jwt_token');
      localStorage.removeItem('current_student');
      window.location.href = '/';
    }
    return Promise.reject(error);
  }
);

export default apiClient;
