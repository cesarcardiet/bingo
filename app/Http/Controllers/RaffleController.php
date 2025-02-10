<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Raffle;
use App\Models\RaffleNumber;
use App\Models\Room;
use App\Models\BingoCard; // 🔹 Agregado para solucionar el error
use App\Events\NumberGenerated;
class RaffleController extends Controller
{
    /**
     * Muestra la lista de sorteos para el agente autenticado.
     */
    public function index()
    {
        $agent = Auth::guard('agent')->user();
        if (!$agent) {
            return redirect()->route('agent.login.form')->withErrors('Debes iniciar sesión.');
        }

        $rooms = Room::where('agent_id', $agent->id)->get();
        $raffles = Raffle::whereHas('room', function ($query) use ($agent) {
            $query->where('agent_id', $agent->id);
        })->get();

        return view('agent.raffles.index', compact('raffles', 'rooms'));
    }

    /**
     * Muestra el formulario para crear un sorteo en una sala específica.
     */
    public function create($room_id)
    {
        $room = Room::findOrFail($room_id);
        if ($room->agent_id !== Auth::guard('agent')->id()) {
            return redirect()->route('rooms.index')->withErrors('No tienes permiso para añadir sorteos en esta sala.');
        }
        return view('agent.raffles.create', compact('room'));
    }

    /**
     * Almacena un nuevo sorteo en la base de datos.
     */
    public function store(Request $request)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'name' => 'required|string|max:255',
            'game_type' => 'required|string',
            'prize' => 'required|numeric|min:0',
            'order' => 'required|integer|min:1',
            'status' => 'required|in:Pendiente,En curso,Finalizado',
            'total_cards' => 'nullable|integer|min:0', // ✅ Agregado para evitar errores
        ]);
    
        $room = Room::findOrFail($request->room_id);
        if ($room->agent_id !== Auth::guard('agent')->id()) {
            return redirect()->route('agent.raffles.create', ['room_id' => $room->id])
                ->withErrors('No tienes permiso para crear sorteos en esta sala.');
        }
    
        Raffle::create([
            'room_id' => $room->id,
            'agent_id' => Auth::guard('agent')->id(),
            'name' => $request->name,
            'game_type' => $request->game_type,
            'prize' => $request->prize,
            'order' => $request->order,
            'status' => $request->status,
            'total_cards' => $request->total_cards ?? 0, // ✅ Asegura que tenga un valor por defecto
        ]);
    
        return redirect()->route('agent.raffles.index')->with('success', 'Sorteo creado correctamente.');
    }
    

    /**
     * Muestra el formulario de edición de un sorteo.
     */
    public function edit($raffle_id)
    {
        $raffle = Raffle::findOrFail($raffle_id);
        return view('agent.raffles.edit', compact('raffle'));
    }

    /**
     * Actualiza un sorteo existente.
     */
    public function update(Request $request, $raffle_id)
    {
        $raffle = Raffle::findOrFail($raffle_id);

        $request->validate([
            'name' => 'required|string|max:255',
            'game_type' => 'required|string',
            'prize' => 'required|numeric|min:0',
            'order' => 'required|integer|min:1',
            'status' => 'required|in:Pendiente,En curso,Finalizado',
        ]);

        $raffle->update([
            'name' => $request->name,
            'game_type' => $request->game_type,
            'prize' => $request->prize,
            'order' => $request->order,
            'status' => $request->status,
        ]);

        return redirect()->route('agent.raffles.index')->with('success', 'Sorteo actualizado correctamente.');
    }

    /**
     * Inicia un sorteo, cambiando su estado a "En curso".
     */
    public function startRaffle($raffle_id)
    {
        $raffle = Raffle::findOrFail($raffle_id);
        if ($raffle->status === 'Pendiente') {
            $raffle->update(['status' => 'En curso']);
        }

        return redirect()->route('agent.raffles.play', ['raffle' => $raffle->id])
            ->with('success', 'Sorteo iniciado correctamente.');
    }

    /**
     * Finaliza un sorteo.
     */
    public function finishRaffle($raffle_id)
    {
        $raffle = Raffle::findOrFail($raffle_id);
        $raffle->update(['status' => 'Finalizado']);

        return redirect()->route('agent.raffles.index')->with('success', 'Sorteo finalizado.');
    }

    /**
     * Elimina un sorteo.
     */
    public function destroy($raffle_id)
    {
        $raffle = Raffle::findOrFail($raffle_id);
        $raffle->delete();

        return redirect()->route('agent.raffles.index')->with('success', 'Sorteo eliminado correctamente.');
    }

    /**
     * Genera un nuevo número para el sorteo.
     */
    public function generateNumber($raffle_id)
    {
        try {
            $raffle = Raffle::findOrFail($raffle_id);

            if ($raffle->status === 'Pendiente') {
                $raffle->update(['status' => 'En curso']);
            }

            $existingNumbers = RaffleNumber::where('raffle_id', $raffle_id)->pluck('number')->toArray();
            $availableNumbers = array_diff(range(1, 75), $existingNumbers);

            if (empty($availableNumbers)) {
                return response()->json(['error' => 'Todos los números han sido sorteados.'], 400);
            }

            $randomNumber = $availableNumbers[array_rand($availableNumbers)];

            RaffleNumber::create([
                'raffle_id' => $raffle_id,
                'number' => $randomNumber
            ]);

            broadcast(new NumberGenerated($randomNumber, $raffle_id))->toOthers();

            return response()->json([
                'message' => "Número sorteado: $randomNumber",
                'number' => $randomNumber
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error interno: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Obtiene los números sorteados en un sorteo específico.
     */
    public function getGeneratedNumbers($raffleId)
    {
        $raffle = Raffle::find($raffleId);

        if (!$raffle) {
            return response()->json(['error' => 'No se encontró un sorteo con este ID.'], 404);
        }

        $numbers = RaffleNumber::where('raffle_id', $raffle->id)->pluck('number')->toArray();

        return response()->json([
            'raffle_id' => $raffle->id,
            'numbers' => $numbers
        ]);
    }   
    public function buyCard(Request $request)
    {
        $player = Auth::guard('player')->user();
    
        // Obtener los cartones seleccionados
        $selectedCards = json_decode($request->selected_cards, true);
    
        if (!$selectedCards || count($selectedCards) == 0) {
            return back()->withErrors('No has seleccionado ningún cartón.');
        }
    
        // Obtener los cartones disponibles
        $cards = BingoCard::whereIn('id', $selectedCards)
                          ->where('status', 'Disponible')
                          ->get();
    
        if ($cards->count() !== count($selectedCards)) {
            return back()->withErrors('Uno o más cartones ya han sido comprados.');
        }
    
        // Calcular el costo total (asegurarse de que sea un número correcto)
        $totalPrice = $cards->sum(fn($card) => floatval($card->room->card_price)); // ✅ Convertir a float para evitar problemas de comparación
    
        // 🔥 Verificar saldo correctamente
        if (floatval($player->balance) < $totalPrice) { // ✅ Convertir saldo a float para evitar errores
            return back()->withErrors('Saldo insuficiente. Por favor, recarga tu cuenta.');
        }
    
        // 🔥 Asignar los cartones al jugador y cambiar estado a "Comprado"
        foreach ($cards as $card) {
            $card->update([
                'player_id' => $player->id,
                'status' => 'Comprado'
            ]);
        }
    
        // 🔥 Descontar el saldo (Asegurar que se reste correctamente)
        $player->decrement('balance', $totalPrice);
    
        return redirect()->route('player.my-cards', ['room_id' => $cards->first()->room_id])
            ->with('success', 'Cartones comprados con éxito.');
    }

    /**
 * Muestra la pantalla para jugar un sorteo específico.
 */
public function play($raffle_id)
{
    $raffle = Raffle::findOrFail($raffle_id);
    $agent = Auth::guard('agent')->user();

    if ($raffle->room->agent_id !== $agent->id) {
        return redirect()->route('agent.dashboard')->withErrors('No tienes permiso para este sorteo.');
    }

    // Obtener los números generados
    $generatedNumbers = RaffleNumber::where('raffle_id', $raffle->id)->pluck('number')->toArray();

    // Obtener los cartones vendidos en la sala de este sorteo
    $soldCards = BingoCard::where('room_id', $raffle->room_id)
                          ->where('status', 'Comprado')
                          ->count();

    return view('agent.raffles.play', compact('raffle', 'generatedNumbers', 'soldCards'));
}
public function viewSorteo($roomId)
{
    $room = Room::findOrFail($roomId);
    $player = Auth::guard('player')->user();

    if (!$player) {
        return redirect()->route('player.login.form')->withErrors('Debes iniciar sesión.');
    }

    // Obtener los cartones del jugador en esta sala
    $playerCards = $player->cards()->where('room_id', $roomId)->get();

    // Obtener los números sorteados
    $generatedNumbers = RaffleNumber::whereHas('raffle', function ($query) use ($roomId) {
        $query->where('room_id', $roomId);
    })->pluck('number')->toArray();

    return view('player.sorteo', compact('room', 'playerCards', 'generatedNumbers'));
}

    
}
