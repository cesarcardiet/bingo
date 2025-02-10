@extends('layouts.player')

@section('title', 'Sala de Bingo')

@section('content')
    <h2 class="text-2xl font-bold text-green-500 text-center">🎲 {{ $room->name }} 🎲</h2>
    <p class="text-center text-gray-600">{{ $room->description }}</p>

    <div class="text-center mt-4">
        <p><strong>💰 Premios Totales:</strong> Bs {{ $room->total_prizes }}</p>
        <p><strong>🎟️ Costo por Cartón:</strong> Bs {{ $room->card_price }}</p>
    </div>

    @if(session('success'))
        <div class="bg-green-500 text-white text-center p-2 rounded-md mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-500 text-white text-center p-2 rounded-md mb-4">
            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <!-- 📌 Números sorteados en la parte superior -->
    <div id="drawn-numbers" class="bg-white p-4 rounded-md shadow-md text-center mt-4">
        <h3 class="text-lg font-bold text-orange-500">🔢 Números Sorteados</h3>
        <div id="numbers-list" class="flex flex-wrap justify-center gap-2 mt-2">
            <!-- Aquí aparecerán los números sorteados -->
        </div>
    </div>

    <h3 class="text-lg font-bold mt-6 text-center">📜 Mis Cartones</h3>

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

    <!-- 📌 Script para actualizar los números y marcar en los cartones -->
    <script>
function fetchGeneratedNumbers() {
    fetch("{{ url('/api/raffles/' . $room->id . '/numbers') }}")
        .then(response => {
            if (!response.ok) {
                throw new Error(`Error en la API: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            if (!data || !data.numbers) {
                throw new Error("Datos inválidos en la respuesta de la API");
            }

            if (data.numbers.length > 0) {
                document.getElementById('drawn-numbers').classList.remove('hidden');
            }

            document.getElementById('numbers-list').innerHTML = data.numbers.map(num =>
                `<span class="bg-green-500 text-white px-3 py-1 rounded-full">${num}</span>`
            ).join(" ");

            // Resaltar los números en los cartones del usuario
            data.numbers.forEach(num => {
                let cells = document.querySelectorAll(`td[id*='cell-'][id$='-${num}']`);
                cells.forEach(cell => {
                    cell.classList.add('bg-green-500', 'text-white');
                });
            });
        })
        .catch(error => console.error("Error al obtener números:", error));
}

// Ejecutar la función cada 3 segundos
setInterval(fetchGeneratedNumbers, 3000);
fetchGeneratedNumbers();

        // 🔄 Actualiza cada 3 segundos
        setInterval(fetchGeneratedNumbers, 3000);
        fetchGeneratedNumbers();
    </script>

    <!-- 📡 WebSockets con Pusher para actualización instantánea -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/laravel-echo/1.11.1/echo.js"></script>
    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>

    <script>
        Pusher.logToConsole = true;

        var pusher = new Pusher("{{ env('PUSHER_APP_KEY') }}", {
            cluster: "{{ env('PUSHER_APP_CLUSTER') }}",
            wsHost: "{{ env('PUSHER_HOST', '127.0.0.1') }}",
            wsPort: "{{ env('PUSHER_PORT', 6001) }}",
            forceTLS: false,
            disableStats: true,
            enabledTransports: ["ws", "wss"]
        });

        var channel = pusher.subscribe("raffle.{{ $room->id }}");

        channel.bind("number.generated", function(data) {
            let number = data.number;
            console.log("Número recibido en tiempo real:", number);

            // 📌 Agregar número a la lista de números sorteados en la parte superior
            let numbersList = document.getElementById("numbers-list");
            let newNumber = document.createElement("span");
            newNumber.className = "bg-green-500 text-white px-3 py-1 rounded-full";
            newNumber.innerText = number;
            numbersList.appendChild(newNumber);

            // 📌 Marcar número en los cartones del jugador
            let cells = document.querySelectorAll(`td[id*='cell-'][id$='-${number}']`);
            cells.forEach(cell => {
                cell.classList.add('bg-green-500', 'text-white', 'font-bold');
            });
        });
    </script>

@endsection
