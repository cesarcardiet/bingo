@extends('layouts.agent')

@section('title', 'Crear Sala')

@section('content')
<div class="max-w-md mx-auto p-6 bg-white shadow-md rounded-md">
    <h2 class="text-2xl font-bold text-center text-green-500 mb-4">🆕 Crear Sala</h2>

    <form action="{{ route('rooms.store') }}" method="POST">
        @csrf

        <div>
            <label class="block">Nombre de la Sala:</label>
            <input type="text" name="name" class="w-full px-3 py-2 border rounded-md" required>
        </div>

        <div>
            <label class="block">Descripción:</label>
            <textarea name="description" class="w-full px-3 py-2 border rounded-md"></textarea>
        </div>

        <div>
            <label class="block">Fecha y Hora de Inicio:</label>
            <input type="datetime-local" name="start_time" class="w-full px-3 py-2 border rounded-md" required>
        </div>

        <div>
            <label class="block">Premios Totales (Bs):</label>
            <input type="number" name="total_prizes" step="0.01" class="w-full px-3 py-2 border rounded-md" required>
        </div>

        <div>
            <label class="block">Precio por Cartón (Bs):</label>
            <input type="number" name="card_price" step="0.01" class="w-full px-3 py-2 border rounded-md" required>
        </div>

        <div>
            <label class="block">Total de Cartones Disponibles:</label>
            <input type="number" name="total_cards" min="1" class="w-full px-3 py-2 border rounded-md" required>
        </div>

        <div>
            <label class="block">Máximo de Jugadores (Opcional):</label>
            <input type="number" name="max_players" min="1" class="w-full px-3 py-2 border rounded-md">
        </div>

        <div>
            <label class="block">Estado de la Sala:</label>
            <select name="status" class="w-full px-3 py-2 border rounded-md">
                <option value="active">Activa</option>
                <option value="inactive">Inactiva</option>
            </select>
        </div>

        <button type="submit" class="w-full bg-green-500 text-white py-2 rounded-md mt-4">
            ✅ Crear Sala
        </button>
    </form>
</div>
@endsection
