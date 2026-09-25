import axios from 'axios';

const reportsClient = axios.create({
  baseURL: '/reporting/api/reports',
  headers: {
    'Content-Type': 'application/json',
  },
});

export default reportsClient;
