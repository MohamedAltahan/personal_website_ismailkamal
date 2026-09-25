import axios from 'axios';

const http = axios.create({
    headers: {
        'X-Requested-With': 'XMLHttpRequest',
        Accept: 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
    },
});

/** First validation / server message from an axios error. */
export function errorMessage(error) {
    const data = error?.response?.data;
    if (data?.errors) {
        return Object.values(data.errors).flat()[0];
    }
    return data?.message || error?.message || 'Error';
}

export default http;
