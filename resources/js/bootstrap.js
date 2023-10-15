import 'bootstrap';

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */
import Echo from "laravel-echo";
import Pusher from 'pusher-js';


window.Echo = new Echo({
    broadcaster: 'pusher',
    key: '200a2bcde647700b50b3',
    wsHost: window.location.hostname,

    forceTLS: false,
    encrypted: true,
    cluster: 'eu',
});
console.log('test');
// window.Echo.channel("game." + gameData.id)
//     .listen('GameUpdate', (e) => {
//         // Handle the received event data (e.message)
//         console.log(e.game);
//         // Update your UI with the new message
//     });