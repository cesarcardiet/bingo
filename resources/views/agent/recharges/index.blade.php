@extends('layouts.agent')

@section('title', 'Recargas Pendientes')

@section('content')
    <h2 class="text-2xl font-bold text-gray-700 mb-4">💰 Recargas Pendientes</h2>

    @if(session('success'))
        <div class="bg-green-200 text-green-800 p-3 rounded-md">
            {{ session('success') }}
        </div>
    @endif

    @if(session('errors'))
        <div class="bg-red-200 text-red-800 p-3 rounded-md">
            {{ session('errors')->first() }}
        </div>
    @endif

    @if(!$recharges || $recharges->isEmpty())
        <p class="text-gray-600">No hay solicitudes de recarga pendientes.</p>
    @else
        <table class="w-full bg-white shadow-md rounded-lg overflow-hidden">
            <thead class="bg-green-500 text-white">
                <tr>
                    <th class="p-3">Jugador</th>
                    <th class="p-3">Monto</th>
                    <th class="p-3">Referencia</th>
                    <th class="p-3">Comprobante</th>
                    <th class="p-3">Acción</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recharges as $recharge)
                    <tr class="border-b">
                        <td class="p-3">{{ $recharge->player->name ?? 'Desconocido' }}</td>
                        <td class="p-3">{{ $recharge->amount }} Bs</td>
                        <td class="p-3">{{ $recharge->reference_number }}</td>
                        <td class="p-3">
                            @if($recharge->receipt_image)
                                <a href="{{ asset('storage/' . $recharge->receipt_image) }}" target="_blank" class="text-blue-500 hover:underline">📄 Ver</a>
                            @else
                                <span class="text-gray-500">Sin comprobante</span>
                            @endif
                        </td>
                        <td class="p-3 flex gap-2">
                            <form action="{{ route('agent.recharge.update', $recharge->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="status" value="Aprobado">
                                <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600 transition">✅ Aprobar</button>
                            </form>
                            @if(!$recharges || $recharges->isEmpty())
    <p class="text-gray-600">No hay solicitudes de recarga pendientes.</p>
@else
    <p class="text-green-600">Se encontraron {{ count($recharges) }} recargas pendientes.</p>
@endif

                            <form action="{{ route('agent.recharge.update', $recharge->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="status" value="Rechazado">
                                <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600 transition">❌ Rechazar</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
@endsection
