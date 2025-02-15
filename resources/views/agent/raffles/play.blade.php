@extends('layouts.agent')

@section('title', 'Jugar Sorteo')

@section('content')
    <h2 class="text-2xl font-bold text-center text-green-500 mb-6">🎲 Sorteo en Curso: {{ $raffle->name }}</h2>

    <!-- ✅ Último Número Generado (Grande en la Parte Superior) -->
    <div class="text-center mb-6">
        <h3 class="text-lg font-bold text-red-500">Último Número Generado</h3>
        <div id="last-number" class="text-6xl font-extrabold text-white bg-red-500 p-6 rounded-lg inline-block shadow-lg">
            --
        </div>
    </div>

    <!-- ✅ Lista de Números Sorteados -->
    <div class="bg-white p-4 rounded-md shadow-md text-center mt-4">
        <h3 class="text-lg font-bold text-orange-500">🔢 Números Sorteados</h3>
        <div id="numbers-list" class="flex flex-wrap justify-center gap-2 mt-2">
            @foreach($generatedNumbers as $number)
                <span class="bg-green-500 text-white px-3 py-1 rounded-full" data-number="{{ $number }}">{{ $number }}</span>
            @endforeach
        </div>
    </div>

    <!-- ✅ Botón para Generar Número -->
    <div class="text-center my-6">
        <button id="generate-btn" class="bg-blue-500 text-white px-4 py-2 rounded-md">
            🎲 Generar Número
        </button>
    </div>

    <!-- ✅ Tabla de Números del 1 al 75 -->
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

    <!-- ✅ Pusher y Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/laravel-echo/1.11.1/echo.js"></script>
    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>

    <script>
        Pusher.logToConsole = true;

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
            console.log("Número recibido en tiempo real:", number);

            // ✅ 1. Actualizar el número en la parte superior
            document.getElementById("last-number").innerText = number;

            // ✅ 2. Agregar a la lista de números sorteados si no existe
            let numbersList = document.getElementById("numbers-list");
            if (!document.querySelector(`#numbers-list span[data-number="${number}"]`)) {
                let span = document.createElement("span");
                span.className = "bg-green-500 text-white px-3 py-1 rounded-full";
                span.innerText = number;
                span.setAttribute("data-number", number);
                numbersList.appendChild(span);
            }

            // ✅ 3. Resaltar el número en la cuadrícula
            let cell = document.getElementById(`num-${number}`);
            if (cell) {
                cell.classList.add("bg-green-500", "text-white", "font-bold");
            }
        });

        // ✅ Generar número al hacer clic y actualizar la interfaz en tiempo real
        document.getElementById("generate-btn").addEventListener("click", function () {
            fetch("{{ url('/api/raffles/' . $raffle->id . '/generate-number') }}", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Content-Type": "application/json",
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.number) {
                    console.log("Número generado:", data.number);

                    // ✅ Mostrar el número generado en la parte superior
                    document.getElementById("last-number").innerText = data.number;

                    // ✅ Agregarlo a la lista si no está
                    let numbersList = document.getElementById("numbers-list");
                    if (!document.querySelector(`#numbers-list span[data-number="${data.number}"]`)) {
                        let span = document.createElement("span");
                        span.className = "bg-green-500 text-white px-3 py-1 rounded-full";
                        span.innerText = data.number;
                        span.setAttribute("data-number", data.number);
                        numbersList.appendChild(span);
                    }

                    // ✅ Resaltar en la cuadrícula
                    let cell = document.getElementById(`num-${data.number}`);
                    if (cell) {
                        cell.classList.add("bg-green-500", "text-white", "font-bold");
                    }
                }
            })
            .catch(error => console.error("Error al generar número:", error));
        });

        function fetchGeneratedNumbers() {
            fetch("{{ url('/api/raffles/' . $raffle->id . '/numbers') }}")
                .then(response => response.json())
                .then(data => {
                    if (!data.numbers) return;

                    document.getElementById('numbers-list').innerHTML = data.numbers.map(num =>
                        `<span class="bg-green-500 text-white px-3 py-1 rounded-full">${num}</span>`
                    ).join(" ");

                    data.numbers.forEach(num => {
                        let cell = document.getElementById(`num-${num}`);
                        if (cell) {
                            cell.classList.add('bg-green-500', 'text-white', 'font-bold');
                        }
                    });

                    // ✅ Mostrar el último número generado en grande
                    if (data.numbers.length > 0) {
                        document.getElementById("last-number").innerText = data.numbers[data.numbers.length - 1];
                    }
                })
                .catch(error => console.error("Error obteniendo números:", error));
        }

        setInterval(fetchGeneratedNumbers, 5000);
        fetchGeneratedNumbers();
    </script>
@endsection
