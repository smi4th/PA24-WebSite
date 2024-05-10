import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

import Echo from 'laravel-echo';
window.Pusher = require('pusher-js');

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: 'c0e18e805b9f688e32bf',
    cluster: 'eu',
    forceTLS: true,
    csrfToken: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
  });
  
  var channel = Echo.channel('messages');
  channel.listen('.message.sent', function(data) {
    alert(JSON.stringify(data));
  });