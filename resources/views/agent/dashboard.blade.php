@extends('layouts.agent')

@section('title', 'Dashboard del Agente')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">

        <!-- Información del Agente -->
        <div class="bg-white p-4 rounded-lg shadow-md">
            <h3 class="text-lg font-semibold">👤 Información Personal</h3>
            <p><strong>Nombre:</strong> {{ $agent->name }}</p>
            <p><strong>Cédula:</strong> {{ $agent->id_number }}</p>
            <p><strong>Teléfono:</strong> {{ $agent->phone }}</p>
            <p><strong>Banco:</strong> {{ $agent->bank_name }}</p>
        </div>

        <!-- Ganancias -->
        <div class="bg-white p-4 rounded-lg shadow-md">
            <h3 class="text-lg font-semibold">💰 Resumen Financiero</h3>
            <p><strong>Ganancias Totales:</strong> 0 Bs</p>
            <p><strong>Pagos Recibidos:</strong> 0 Bs</p>
            <p><strong>Premios Pagados:</strong> 0 Bs</p>
        </div>

        <!-- Acciones Rápidas -->
        <div class="bg-white p-4 rounded-lg shadow-md">
            <h3 class="text-lg font-semibold">⚡ Acciones Rápidas</h3>
            <a href="{{ route('agent.raffles.index') }}" class="block bg-green-500 text-white p-3 rounded-lg text-center hover:bg-green-600 transition">
                🎟️ Gestionar Sorteos
            </a>
            <a href="#" class="block bg-blue-500 text-white p-3 rounded-lg text-center hover:bg-blue-600 transition mt-2">
                💰 Ver Pagos
            </a>
        </div>

    </div>
@endsection
