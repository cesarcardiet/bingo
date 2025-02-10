@extends('layouts.agent')

@section('title', 'Mis Sorteos')

@section('content')
    <h2 class="text-2xl font-bold text-gray-700 mb-4">🎟️ Mis Sorteos</h2>

    @if ($rooms->isEmpty())
        <p class="text-red-500">⚠️ Debes crear una sala antes de crear un sorteo.</p>
        <a href="{{ route('rooms.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 transition">
            ➕ Crear Sala
        </a>
    @else
        <a href="{{ route('agent.raffles.create', ['room_id' => $rooms->first()->id]) }}"
           class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600 transition">
            ➕ Crear Nuevo Sorteo
        </a>
    @endif

    @if (session('success'))
        <div class="bg-green-200 text-green-800 p-3 rounded-md mt-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="mt-5">
        <table class="w-full bg-white shadow-md rounded-lg overflow-hidden">
            <thead class="bg-green-500 text-white">
                <tr>
                    <th class="p-3">Sala</th>
                    <th class="p-3">Nombre</th>
                    <th class="p-3">Inicio</th>
                    <th class="p-3">Tipo</th>
                    <th class="p-3">Cartones</th>
                    <th class="p-3">Premio</th>
                    <th class="p-3">Estado</th>
                    <th class="p-3">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($raffles as $raffle)
                    <tr class="border-b">
                        <td class="p-3">{{ $raffle->room->name }}</td>
                        <td class="p-3">{{ $raffle->name }}</td>
                        <td class="p-3">{{ $raffle->start_time }}</td>
                        <td class="p-3">{{ $raffle->game_type }}</td>
                        <td class="p-3">{{ $raffle->total_cards }}</td>
                        <td class="p-3">{{ $raffle->prize }} Bs</td>
                        <td class="p-3">{{ $raffle->status }}</td>
                        <td class="p-3 flex items-center space-x-2">
                            <a href="{{ route('agent.raffles.edit', $raffle->id) }}"
                               class="text-blue-500 hover:underline">✏️ Editar</a>
                            <a href="{{ route('agent.raffles.play', $raffle->id) }}"
                               class="text-green-500 hover:underline">▶️ Iniciar</a>
                            <a href="{{ route('agent.raffles.finish', $raffle->id) }}" 
                               class="bg-red-500 text-white px-3 py-1 rounded-md hover:bg-red-600 transition">
                                ⏹ Finalizar
                            </a>

                            <!-- 🗑️ Botón de eliminar -->
                            <form action="{{ route('agent.raffles.destroy', $raffle->id) }}" method="POST"
                                  onsubmit="return confirm('¿Estás seguro de eliminar este sorteo? Esta acción no se puede deshacer.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-700 text-white px-3 py-1 rounded-md hover:bg-red-800 transition">
                                    🗑️ Eliminar
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="p-3 text-center text-gray-500">No hay sorteos creados aún.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
