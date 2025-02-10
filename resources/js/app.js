import './bootstrap';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: process.env.MIX_PUSHER_APP_KEY,
    cluster: process.env.MIX_PUSHER_APP_CLUSTER,
    wsHost: process.env.MIX_PUSHER_HOST,
    wsPort: process.env.MIX_PUSHER_PORT,
    forceTLS: false,
    disableStats: true
});

// Escuchar eventos en la sala del bingo
let roomId = 3; // Debes reemplazarlo dinámicamente con la ID de la sala en la que esté el jugador

window.Echo.channel('bingo-room-' + roomId)
    .listen('.new-number', (data) => {
        console.log('Nuevo número generado:', data.number);
        alert('Se generó el número: ' + data.number);
    });
