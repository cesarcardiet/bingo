@extends('layouts.agent')

@section('title', 'Editar Sala')

@section('content')
<div class="max-w-lg mx-auto bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-2xl font-bold text-center text-blue-500 mb-6">✏️ Editar Sala</h2>

    <form action="{{ route('rooms.update', $room->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-gray-700 font-semibold">Nombre de la Sala:</label>
            <input type="text" name="name" value="{{ $room->name }}" class="w-full px-3 py-2 border rounded-md focus:ring focus:ring-blue-300" required>
        </div>

        <div>
            <label class="block text-gray-700 font-semibold">Descripción:</label>
            <textarea name="description" class="w-full px-3 py-2 border rounded-md focus:ring focus:ring-blue-300">{{ $room->description }}</textarea>
        </div>

        <div>
            <label class="block text-gray-700 font-semibold">Fecha y Hora de Inicio:</label>
            <input type="datetime-local" name="start_time" value="{{ \Carbon\Carbon::parse($room->start_time)->format('Y-m-d\TH:i') }}" class="w-full px-3 py-2 border rounded-md focus:ring focus:ring-blue-300" required>
        </div>

        <div>
            <label class="block text-gray-700 font-semibold">Premios Totales (Bs):</label>
            <input type="number" name="total_prizes" value="{{ $room->total_prizes }}" class="w-full px-3 py-2 border rounded-md focus:ring focus:ring-blue-300" required>
        </div>

        <div>
            <label class="block text-gray-700 font-semibold">Precio por Cartón (Bs):</label>
            <input type="number" name="card_price" value="{{ $room->card_price }}" class="w-full px-3 py-2 border rounded-md focus:ring focus:ring-blue-300" required>
        </div>

        <div>
            <label class="block text-gray-700 font-semibold">Cantidad Total de Cartones:</label>
            <input type="number" name="total_cards" value="{{ $room->total_cards }}" class="w-full px-3 py-2 border rounded-md focus:ring focus:ring-blue-300" required>
        </div>

        <div>
            <label class="block text-gray-700 font-semibold">Máximo de Jugadores:</label>
            <input type="number" name="max_players" value="{{ $room->max_players }}" class="w-full px-3 py-2 border rounded-md focus:ring focus:ring-blue-300">
        </div>

        <div>
            <label class="block text-gray-700 font-semibold">Estado de la Sala:</label>
            <select name="status" class="w-full px-3 py-2 border rounded-md focus:ring focus:ring-blue-300">
                <option value="active" {{ $room->status == 'active' ? 'selected' : '' }}>Activa</option>
                <option value="inactive" {{ $room->status == 'inactive' ? 'selected' : '' }}>Inactiva</option>
            </select>
        </div>

        <button type="submit" class="w-full bg-blue-500 text-white py-2 rounded-md hover:bg-blue-600 transition mt-4">
            💾 Guardar Cambios
        </button>
    </form>

    <a href="{{ route('rooms.index') }}" class="block text-center text-gray-500 mt-4 hover:underline">⬅️ Volver a la Lista de Salas</a>
</div>
@endsection
