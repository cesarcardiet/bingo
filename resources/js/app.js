import Echo from "laravel-echo";
import Pusher from "pusher-js";

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: "pusher",
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    wsHost: import.meta.env.VITE_PUSHER_HOST || "127.0.0.1",
    wsPort: import.meta.env.VITE_PUSHER_PORT || 6001,
    forceTLS: false,
    disableStats: true
});

// ✅ Escuchar eventos en la sala del bingo
let roomId = 3; // ⚠️ ESTE ID DEBE SER DINÁMICO

window.Echo.channel("bingo-room-" + roomId)
    .listen(".new-number", (data) => {
        console.log("Nuevo número generado:", data.number);
        alert("Se generó el número: " + data.number);
    });
    window.Echo.channel("raffle-winner")
    .listen(".winner.announced", (data) => {
        Swal.fire({
            title: "🎉 ¡Tenemos un ganador!",
            html: `<strong>Jugador ${data.player_id}</strong> ha ganado <strong>${data.prize} Bs</strong>`,
            icon: "success",
            confirmButtonText: "Aceptar"
        }).then(() => {
            if (data.next_raffle_id) {
                window.location.href = `/agent/raffles/${data.next_raffle_id}/play`;
            } else {
                window.location.href = "/agent/dashboard"; // Si no hay más sorteos, redirige al dashboard
            }
        });
    });
