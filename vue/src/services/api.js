import axios from 'axios';

const apiClient = axios.create({
    baseURL: 'http://tsszadanie.test/api',
    headers: {
        'Content-Type': 'application/json',
    },
});

export default apiClient;