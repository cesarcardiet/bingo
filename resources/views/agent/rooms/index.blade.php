@extends('layouts.agent')

@section('title', 'Mis Salas')

@section('content')
    <h2 class="text-2xl font-bold text-gray-700 mb-4">🏠 Mis Salas</h2>

    <a href="{{ route('rooms.create') }}" class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600 transition">
        ➕ Crear Nueva Sala
    </a>

    @if (session('success'))
        <div class="bg-green-200 text-green-800 p-3 rounded-md mt-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="mt-5">
        <table class="w-full bg-white shadow-md rounded-lg overflow-hidden">
            <thead class="bg-green-500 text-white">
                <tr>
                    <th class="p-3">Nombre</th>
                    <th class="p-3">Fecha de Inicio</th>
                    <th class="p-3">Premios Totales</th>
                    <th class="p-3">Cartón (Bs)</th>
                    <th class="p-3">Cartones Disponibles</th>
                    <th class="p-3">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rooms as $room)
                    <tr class="border-b">
                        <td class="p-3">{{ $room->name }}</td>
                        <td class="p-3">{{ $room->start_time }}</td>
                        <td class="p-3">{{ $room->total_prizes }} Bs</td>
                        <td class="p-3">{{ $room->card_price }} Bs</td>
                        <td class="p-3">{{ $room->total_cards }}</td>
                        <td class="p-3 flex items-center space-x-2">
                            <a href="{{ route('agent.raffles.create', ['room_id' => $room->id]) }}" class="bg-blue-500 text-white px-3 py-1 rounded-md hover:bg-blue-600">
                                ➕ Añadir Sorteo
                            </a>
                            <a href="{{ route('rooms.edit', $room->id) }}" class="text-blue-500 hover:underline">
                                ✏️ Editar
                            </a>

                            <!-- Botón de Eliminar -->
                            <form action="{{ route('rooms.destroy', $room->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar esta sala? Se eliminarán todos los sorteos asociados.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded-md hover:bg-red-600 transition">
                                    ❌ Eliminar
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-3 text-center text-gray-500">No tienes salas creadas aún.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
