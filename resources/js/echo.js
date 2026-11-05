import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

const appKey = import.meta.env.VITE_REVERB_APP_KEY;

if (!appKey) {
    console.error('Chave VITE_REVERB_APP_KEY não encontrada.');
} else {
    window.Echo = new Echo({
        broadcaster: 'reverb',
        key: appKey,

        wsHost: import.meta.env.VITE_REVERB_HOST || '127.0.0.1',
        wsPort: Number(import.meta.env.VITE_REVERB_PORT || 8080),
        wssPort: Number(import.meta.env.VITE_REVERB_PORT || 8080),

        forceTLS: (import.meta.env.VITE_REVERB_SCHEME || 'http') === 'https',

        enabledTransports: ['ws', 'wss'],
    });
}