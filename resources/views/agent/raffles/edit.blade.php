@extends('layouts.agent')

@section('title', 'Editar Sorteo')

@section('content')
<div class="max-w-lg mx-auto bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-2xl font-bold text-center text-green-500 mb-6">✏️ Editar Sorteo</h2>

    <form action="{{ route('agent.raffles.update', $raffle->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-gray-700 font-semibold">Nombre del Sorteo:</label>
            <input type="text" name="name" value="{{ $raffle->name }}" class="w-full px-3 py-2 border rounded-md focus:ring focus:ring-green-300" required>
        </div>

        <div>
            <label class="block text-gray-700 font-semibold">Modalidad de Juego:</label>
            <select name="game_type" class="w-full px-3 py-2 border rounded-md focus:ring focus:ring-green-300" required>
                <option value="Cartón lleno" {{ $raffle->game_type == 'Cartón lleno' ? 'selected' : '' }}>Cartón lleno</option>
                <option value="Línea" {{ $raffle->game_type == 'Línea' ? 'selected' : '' }}>Línea</option>
                <option value="Cruz" {{ $raffle->game_type == 'Cruz' ? 'selected' : '' }}>Cruz</option>
                <option value="Cuatro esquinas" {{ $raffle->game_type == 'Cuatro esquinas' ? 'selected' : '' }}>Cuatro esquinas</option>
                <option value="Esquinas dobles" {{ $raffle->game_type == 'Esquinas dobles' ? 'selected' : '' }}>Esquinas dobles</option>
                <option value="X" {{ $raffle->game_type == 'X' ? 'selected' : '' }}>X</option>
                <option value="Patrón personalizado" {{ $raffle->game_type == 'Patrón personalizado' ? 'selected' : '' }}>Patrón personalizado</option>
            </select>
        </div>

        <div>
            <label class="block text-gray-700 font-semibold">Premio del Sorteo (Bs):</label>
            <input type="number" name="prize" value="{{ $raffle->prize }}" step="0.01" class="w-full px-3 py-2 border rounded-md focus:ring focus:ring-green-300" required>
        </div>

        <div>
            <label class="block text-gray-700 font-semibold">Orden del Sorteo:</label>
            <input type="number" name="order" value="{{ $raffle->order }}" min="1" class="w-full px-3 py-2 border rounded-md focus:ring focus:ring-green-300" required>
        </div>

        <div>
            <label class="block text-gray-700 font-semibold">Estado del Sorteo:</label>
            <select name="status" class="w-full px-3 py-2 border rounded-md focus:ring focus:ring-green-300">
                <option value="Pendiente" {{ $raffle->status == 'Pendiente' ? 'selected' : '' }}>Pendiente</option>
                <option value="En curso" {{ $raffle->status == 'En curso' ? 'selected' : '' }}>En Curso</option>
                <option value="Finalizado" {{ $raffle->status == 'Finalizado' ? 'selected' : '' }}>Finalizado</option>
            </select>
        </div>

        <div class="flex gap-3 mt-4">
            <button type="submit" class="w-full bg-green-500 text-white py-2 rounded-md hover:bg-green-600 transition">
                💾 Guardar Cambios
            </button>
        </div>
    </form>

    <a href="{{ route('agent.raffles.index') }}" class="block text-center text-gray-500 mt-4 hover:underline">⬅️ Volver a la lista de Sorteos</a>
</div>
@endsection
