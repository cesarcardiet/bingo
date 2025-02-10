@extends('layouts.player')

@section('title', 'Dashboard del Jugador')

@section('content')

    <!-- Mostrar Mensajes de Éxito o Error -->
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

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">

        <!-- Información del Jugador -->
        <div class="bg-white p-4 rounded-lg shadow-md">
            <h3 class="text-lg font-semibold">👤 Mi Perfil</h3>
            <p><strong>Nombre:</strong> {{ $player->name }}</p>
            <p><strong>Correo:</strong> {{ $player->email }}</p>
            <p><strong>Agente Asignado:</strong> {{ $player->agent->name ?? 'Sin asignar' }}</p>
        </div>

        <!-- Saldo -->
        <div class="bg-white p-4 rounded-lg shadow-md">
    <h3 class="text-lg font-semibold">💰 Mi Saldo</h3>
    <p><strong>Disponible:</strong> {{ number_format($player->balance, 2) }} Bs</p>
    <a href="{{ route('player.recharges.create') }}" 
       class="block bg-green-500 text-white p-3 rounded-lg text-center hover:bg-green-600 transition">
        💳 Recargar Saldo
    </a>
</div>


        <!-- Salas del Agente -->
        <div class="bg-white p-4 rounded-lg shadow-md col-span-2">
            <h3 class="text-lg font-semibold">🏠 Salas Disponibles</h3>
            @if($rooms->isEmpty())
                <p class="text-gray-600">No hay salas disponibles aún.</p>
            @else
                <ul class="mt-2">
                    @foreach($rooms as $room)
                        <li class="p-3 border-b flex justify-between">
                            <span>🎰 {{ $room->name }} - {{ $room->total_prize }} Bs</span>
                            <a href="{{ route('player.room.show', $room->id) }}" 
                               class="text-blue-500 hover:underline">🎟️ Ver Sala</a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

    </div>
@endsection
