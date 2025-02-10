@extends('layouts.agent')

@section('title', 'Crear Sorteo')

@section('content')
<div class="max-w-lg mx-auto bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-2xl font-bold text-center text-green-500 mb-6">🎟️ Añadir Sorteo en Sala: {{ $room->name }}</h2>

    <p class="text-gray-600 text-center mb-4">Esta sala ya tiene **{{ $room->raffles->count() }}** sorteos configurados.</p>

    <form action="{{ route('agent.raffles.store') }}" method="POST">
        @csrf
        <input type="hidden" name="room_id" value="{{ $room->id }}">

        <div>
            <label class="block text-gray-700 font-semibold">Nombre del Sorteo:</label>
            <input type="text" name="name" class="w-full px-3 py-2 border rounded-md focus:ring focus:ring-green-300" placeholder="Ejemplo: Cruz, Línea, Súper Cartón" required>
        </div>

        <div>
            <label class="block text-gray-700 font-semibold">Modalidad de Juego:</label>
            <select name="game_type" class="w-full px-3 py-2 border rounded-md focus:ring focus:ring-green-300" required>
                <option value="Cartón lleno">Cartón lleno</option>
                <option value="Línea">Línea</option>
                <option value="Cruz">Cruz</option>
                <option value="Cuatro esquinas">Cuatro esquinas</option>
                <option value="Esquinas dobles">Esquinas dobles</option>
                <option value="X">X</option>
                <option value="Patrón personalizado">Patrón personalizado</option>
            </select>
        </div>

        <div>
            <label class="block text-gray-700 font-semibold">Premio del Sorteo (Bs):</label>
            <input type="number" name="prize" step="0.01" class="w-full px-3 py-2 border rounded-md focus:ring focus:ring-green-300" required>
        </div>

        <div>
            <label class="block text-gray-700 font-semibold">Orden del Sorteo:</label>
            <input type="number" name="order" min="1" class="w-full px-3 py-2 border rounded-md focus:ring focus:ring-green-300" placeholder="Ejemplo: 1, 2, 3..." required>
        </div>

        <div>
            <label class="block text-gray-700 font-semibold">Estado del Sorteo:</label>
            <select name="status" class="w-full px-3 py-2 border rounded-md focus:ring focus:ring-green-300">
                <option value="Pendiente">Pendiente</option>
                <option value="En curso">En Curso</option>
                <option value="Finalizado">Finalizado</option>
            </select>
        </div>

        <div class="flex gap-3 mt-4">
            <button type="submit" name="action" value="save" class="w-1/2 bg-green-500 text-white py-2 rounded-md hover:bg-green-600 transition">
                ✅ Guardar Sorteo
            </button>
            <button type="submit" name="action" value="add_more" class="w-1/2 bg-blue-500 text-white py-2 rounded-md hover:bg-blue-600 transition">
                ➕ Guardar y Añadir Otro
            </button>
        </div>
    </form>

    <a href="{{ route('rooms.index') }}" class="block text-center text-gray-500 mt-4 hover:underline">⬅️ Volver a las Salas</a>
</div>
@endsection
