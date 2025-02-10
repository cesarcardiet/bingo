@extends('layouts.agent')

@section('title', 'Jugar Sorteo')
<script src="https://js.pusher.com/7.0/pusher.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/laravel-echo/1.11.1/echo.js"></script>

@section('content')
    <h2 class="text-2xl font-bold text-center text-green-500 mb-6">🎲 Sorteo en Curso: {{ $raffle->name }}</h2>

    <div class="bg-white p-4 rounded-md shadow-md text-center mt-4">
        <h3 class="text-lg font-bold text-orange-500">🔢 Números Sorteados</h3>
        <div id="numbers-list" class="flex flex-wrap justify-center gap-2 mt-2">
            @foreach($generatedNumbers as $number)
                <span class="bg-green-500 text-white px-3 py-1 rounded-full">{{ $number }}</span>
            @endforeach
        </div>
    </div>

    <div class="text-center my-6">
        <button id="generate-btn" class="bg-blue-500 text-white px-4 py-2 rounded-md">
            🎲 Generar Número
        </button>
    </div>

    <div class="grid grid-cols-10 gap-2 text-center text-white">
        @foreach(range(1, 75) as $number)
            @php
                $isDrawn = in_array($number, $generatedNumbers);
            @endphp
            <div class="p-3 text-lg font-bold rounded-md {{ $isDrawn ? 'bg-green-500' : 'bg-gray-300' }}" id="num-{{ $number }}">
                {{ $number }}
            </div>
        @endforeach
    </div>

    <script>
        document.getElementById("generate-btn").addEventListener("click", function() {
            fetch("{{ route('agent.raffles.generate', $raffle->id) }}", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Content-Type": "application/json"
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.number) {
                    let numbersList = document.getElementById("numbers-list");
                    let span = document.createElement("span");
                    span.className = "bg-green-500 text-white px-3 py-1 rounded-full";
                    span.innerText = data.number;
                    numbersList.appendChild(span);

                    let cell = document.getElementById(`num-${data.number}`);
                    if (cell) {
                        cell.classList.add('bg-green-500', 'text-white');
                    }
                }
            })
            .catch(error => console.error("Error:", error));
        });

        // 🔥 Escuchar eventos de números en vivo (WebSocket con Pusher)
        var pusher = new Pusher("{{ env('PUSHER_APP_KEY') }}", {
            cluster: "{{ env('PUSHER_APP_CLUSTER') }}",
            wsHost: "{{ env('PUSHER_HOST', '127.0.0.1') }}",
            wsPort: "{{ env('PUSHER_PORT', 6001) }}",
            wssPort: "{{ env('PUSHER_PORT', 6001) }}",
            forceTLS: false,
            disableStats: true,
            enabledTransports: ["ws", "wss"]
        });

        var channel = pusher.subscribe("raffle.{{ $raffle->id }}");

        channel.bind("number.generated", function(data) {
            let number = data.number;

            let numbersList = document.getElementById("numbers-list");
            let span = document.createElement("span");
            span.className = "bg-green-500 text-white px-3 py-1 rounded-full";
            span.innerText = number;
            numbersList.appendChild(span);

            let cell = document.getElementById(`num-${number}`);
            if (cell) {
                cell.classList.add('bg-green-500', 'text-white');
            }
        });
    </script>
@endsection
