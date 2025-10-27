import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
// Include credentials for cookie-based auth (Sanctum SPA)
window.axios.defaults.withCredentials = true;
// Axios will automatically read XSRF token from the `XSRF-TOKEN` cookie and set the
// `X-XSRF-TOKEN` header on requests. Laravel sets the cookie via /sanctum/csrf-cookie.

// Global response interceptor: on 401/419, try fetching a fresh CSRF cookie and retry once.
window.axios.interceptors.response.use(
	response => response,
	async (error) => {
		const status = error.response?.status;
		const config = error.config || {};

		// Only retry once per request
		if ((status === 401 || status === 419) && !config.__isRetry) {
			config.__isRetry = true;
			try {
				await window.axios.get('/sanctum/csrf-cookie');
				return window.axios(config);
			} catch (err) {
				// If retry fails, fall through to rejection so caller can handle/redirect
				return Promise.reject(err);
			}
		}

		return Promise.reject(error);
	}
);
