window._ = require("lodash");

try {
    // jQuery remains available for AdminLTE and other plugins
    window.$ = window.jQuery = require("jquery");

    // Bootstrap 5 bundle (includes @popperjs/core v2)
    window.bootstrap = require("bootstrap/dist/js/bootstrap.bundle");

    // AdminLTE still loads (layout helpers); avoid BS4-specific plugins
    require("admin-lte");

    // DataTables styling for Bootstrap 5 (if installed)
    try { require("datatables.net-bs5")(window, $); } catch (_) {}
} catch (e) {}

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = require("axios");

window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";
window.axios.defaults.baseUrl = "http://localhost:8000";

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo';

// window.Pusher = require('pusher-js');

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: process.env.MIX_PUSHER_APP_KEY,
//     cluster: process.env.MIX_PUSHER_APP_CLUSTER,
//     forceTLS: true
// });
