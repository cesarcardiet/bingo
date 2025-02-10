@extends('layouts.player')

@section('title', 'Sorteo en ' . $room->name)

@section('content')

<h2 class="text-2xl font-bold text-green-500 text-center">🎟️ Sorteo en {{ $room->name }}</h2>
<p class="text-center text-gray-600">⏳ Empieza: {{ \Carbon\Carbon::parse($room->start_time)->format('d/m/Y H:i') }}</p>

<!-- Números Sorteados -->
<div id="drawn-numbers" class="bg-white p-4 rounded-md shadow-md text-center mt-4">
    <h3 class="text-lg font-bold text-orange-500">🔢 Números Sorteados</h3>
    <div id="numbers-list" class="flex flex-wrap justify-center gap-2 mt-2">
        @foreach($generatedNumbers as $num)
            <span class="bg-green-500 text-white px-3 py-1 rounded-full">{{ $num }}</span>
        @endforeach
    </div>
</div>

<!-- Cartones del Jugador -->
<h3 class="text-lg font-bold mt-6 text-center">📜 Mis Cartones</h3>

@if(isset($playerCards) && $playerCards->count() > 0)
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mt-4">
    @foreach($playerCards as $card)
        <div class="bg-white p-4 rounded-md shadow-md">
            <h4 class="text-lg font-bold text-green-500 text-center">#{{ $card->card_number }}</h4>
            <table class="w-full border">
                <thead>
                    <tr>
                        <th class="p-2 border bg-orange-500 text-white">B</th>
                        <th class="p-2 border bg-orange-500 text-white">I</th>
                        <th class="p-2 border bg-orange-500 text-white">N</th>
                        <th class="p-2 border bg-orange-500 text-white">G</th>
                        <th class="p-2 border bg-orange-500 text-white">O</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $cardData = json_decode($card->card_data, true);
                    @endphp
                    @for ($i = 0; $i < 5; $i++)
                        <tr>
                            @foreach(['B', 'I', 'N', 'G', 'O'] as $letter)
                                <td class="p-2 border text-center text-lg number-cell"
                                    id="cell-{{ $card->id }}-{{ $cardData[$letter][$i] }}">
                                    @if ($letter === 'N' && $i === 2)
                                        <span class="text-red-500 font-bold">X</span> <!-- Espacio Libre -->
                                    @else
                                        {{ $cardData[$letter][$i] }}
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endfor
                </tbody>
            </table>
        </div>
    @endforeach
</div>
@else
<p class="text-center text-gray-500">⚠️ No tienes cartones para este sorteo.</p>
@endif

<!-- Script para actualizar los números en tiempo real -->
<script>
function fetchGeneratedNumbers() {
    fetch("{{ route('player.raffle.numbers', ['room' => $room->id]) }}")
        .then(response => response.json())
        .then(data => {
            if (data.numbers.length > 0) {
                document.getElementById('drawn-numbers').classList.remove('hidden');
            }

            let numbersList = document.getElementById('numbers-list');
            numbersList.innerHTML = "";
            data.numbers.forEach(num => {
                let span = document.createElement("span");
                span.className = "bg-green-500 text-white px-3 py-1 rounded-full";
                span.innerText = num;
                numbersList.appendChild(span);
            });

            // Resaltar los números en los cartones del usuario
            data.numbers.forEach(num => {
                let cells = document.querySelectorAll(`td[id*='cell-'][id$='-${num}']`);
                cells.forEach(cell => {
                    cell.classList.add('bg-green-500', 'text-white');
                });
            });

            // 🔥 Mostrar alerta si hay ganador
            if (data.winner) {
                alert(data.message);
            }
        })
        .catch(error => console.error("Error obteniendo números:", error));
}

// Llamar la función cada 3 segundos
setInterval(fetchGeneratedNumbers, 3000);
fetchGeneratedNumbers(); // Llamada inicial
</script>

<!-- WebSockets con Pusher -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/laravel-echo/1.11.1/echo.js"></script>
<script src="https://js.pusher.com/7.0/pusher.min.js"></script>

<script>
    Pusher.logToConsole = true; // Para depuración en la consola

    var pusher = new Pusher("{{ env('PUSHER_APP_KEY') }}", {
        cluster: "{{ env('PUSHER_APP_CLUSTER') }}",
        wsHost: "{{ env('PUSHER_HOST', '127.0.0.1') }}",
        wsPort: "{{ env('PUSHER_PORT', 6001) }}",
        wssPort: "{{ env('PUSHER_PORT', 6001) }}",
        forceTLS: false,
        disableStats: true,
        enabledTransports: ["ws", "wss"]
    });

    var channel = pusher.subscribe("raffle.{{ $room->id }}");

    channel.bind("number.generated", function(data) {
        let number = data.number;

        console.log("Número recibido:", number); // 🔍 Depuración

        let numbersList = document.getElementById("numbers-list");
        let span = document.createElement("span");
        span.className = "bg-green-500 text-white px-3 py-1 rounded-full";
        span.innerText = number;
        numbersList.appendChild(span);

        // Resaltar número en los cartones del jugador
        let cells = document.querySelectorAll(`td[id*='cell-'][id$='-${number}']`);
        cells.forEach(cell => {
            cell.classList.add('bg-green-500', 'text-white');
        });
    });
</script>

@endsection
