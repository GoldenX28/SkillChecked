import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
// Include credentials for cookie-based auth (Sanctum SPA)
window.axios.defaults.withCredentials = true;
// Axios will automatically read XSRF token from the `XSRF-TOKEN` cookie and set the
// `X-XSRF-TOKEN` header on requests. Laravel sets the cookie via /sanctum/csrf-cookie.
