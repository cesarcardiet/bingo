<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Raffle;
use App\Models\RaffleNumber;
use App\Models\Room;
use App\Models\BingoCard;
use App\Events\NumberGenerated;
use Illuminate\Support\Facades\DB;
use \App\Models\Winner;
use App\Events\WinnerAnnounced;


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
     * Crea un sorteo en una sala específica.
     */
    public function create($room_id)
    {
        $room = Room::findOrFail($room_id);
        return view('agent.raffles.create', compact('room'));
    }

    /**
     * Guarda un sorteo en la base de datos.
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
            'total_cards' => 'nullable|integer|min:0',
        ]);

        $room = Room::findOrFail($request->room_id);
        $raffle = Raffle::create([
            'room_id' => $room->id,
            'agent_id' => Auth::guard('agent')->id(),
            'name' => $request->name,
            'game_type' => $request->game_type,
            'prize' => $request->prize,
            'order' => $request->order,
            'status' => $request->status,
            'total_cards' => $request->total_cards ?? 0,
        ]);

        return redirect()->route('agent.raffles.index')->with('success', 'Sorteo creado correctamente.');
    }

    /**
     * Muestra la vista de edición del sorteo.
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

        $raffle->update($request->all());

        return redirect()->route('agent.raffles.index')->with('success', 'Sorteo actualizado correctamente.');
    }

    /**
     * Inicia un sorteo.
     */
    public function startRaffle($raffle_id)
    {
        $raffle = Raffle::findOrFail($raffle_id);
        $raffle->update(['status' => 'En curso']);

        return redirect()->route('agent.raffles.play', $raffle->id)->with('success', 'Sorteo iniciado.');
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
     * Muestra la vista de juego del sorteo.
     */
    public function play($id)
    {
        $raffle = Raffle::find($id);
    
        if (!$raffle) {
            return abort(404, 'El sorteo no existe.');
        }
    
        // Obtener los números generados para este sorteo
        $generatedNumbers = \App\Models\RaffleNumber::where('raffle_id', $id)
            ->pluck('number')
            ->toArray();
    
        return view('agent.raffles.play', compact('raffle', 'generatedNumbers'));
    }
    

    /**
     * Genera un nuevo número aleatorio.
     */
    public function generateNumber($raffle_id)
    {
        try {
            $raffle = Raffle::findOrFail($raffle_id);
            $existingNumbers = RaffleNumber::where('raffle_id', $raffle_id)->pluck('number')->toArray();
            $availableNumbers = array_diff(range(1, 75), $existingNumbers);
    
            if (empty($availableNumbers)) {
                return response()->json(['error' => 'Todos los números han sido sorteados.'], 400);
            }
    
            // 🔹 Generar un número aleatorio no repetido
            $randomNumber = $availableNumbers[array_rand($availableNumbers)];
            $raffleNumber = RaffleNumber::create(['raffle_id' => $raffle_id, 'number' => $randomNumber]);
    
            if (!$raffleNumber) {
                return response()->json(['error' => 'No se pudo guardar el número.'], 500);
            }
    
            // 🟢 Registrar en el log para depuración
            Log::info("Número generado: " . $randomNumber);
    
            // 🔥 Emitir evento del número generado
            broadcast(new NumberGenerated($randomNumber, $raffle_id))->toOthers();
    
            // 🔍 Verificar si hay un ganador después de generar el número
            $winner = $this->checkWinnerInternal($raffle_id);
    
            if ($winner) {
                // 🔹 Guardar al ganador en la base de datos
                $newWinner = Winner::create([
                    'raffle_id' => $raffle_id,
                    'player_id' => $winner->player_id,
                    'bingo_card_id' => $winner->id,
                    'prize' => $raffle->prize
                ]);
    
                // 🔥 Emitir evento de ganador
                broadcast(new WinnerAnnounced($newWinner->player_id, $newWinner->raffle_id, $newWinner->prize))->toOthers();
    
                return response()->json([
                    'message' => '¡Tenemos un ganador!',
                    'player_id' => $newWinner->player_id,
                    'number' => $randomNumber
                ]);
            }
    
            return response()->json([
                'message' => "Número sorteado: $randomNumber",
                'number' => $randomNumber
            ]);
    
        } catch (\Exception $e) {
            Log::error("Error al generar número: " . $e->getMessage());
            return response()->json(['error' => 'Error interno: ' . $e->getMessage()], 500);
        }
    }
    
    public function destroy($raffle_id)
{
    $raffle = Raffle::findOrFail($raffle_id);
    $raffle->delete();

    return redirect()->route('agent.raffles.index')->with('success', 'Sorteo eliminado correctamente.');
}

    
public function liveView($raffle_id)
{
    $raffle = Raffle::findOrFail($raffle_id);
    $generatedNumbers = RaffleNumber::where('raffle_id', $raffle->id)->pluck('number')->toArray();
    $players = $raffle->room->players()->with('cards')->get();

    return view('agent.raffles.live', compact('raffle', 'generatedNumbers', 'players'));
}

    /**
     * Obtiene los números generados.
     */
    public function getGeneratedNumbers($id)
    {
        // Primero, intentamos buscar por Raffle ID
        $raffle = Raffle::find($id);
    
        // Si no se encuentra el sorteo por ID, asumimos que es un Room ID y buscamos el más reciente
        if (!$raffle) {
            $raffle = Raffle::where('room_id', $id)
                ->orderBy('created_at', 'desc')
                ->first();
        }
    
        // Si sigue sin encontrarse, devolvemos error
        if (!$raffle) {
            return response()->json([
                'error' => 'No se encontró un sorteo para este ID.',
                'id' => $id
            ], 404);
        }
    
        // Obtener los números sorteados
        $numbers = RaffleNumber::where('raffle_id', $raffle->id)->pluck('number')->toArray();
    
        return response()->json([
            'raffle_id' => $raffle->id,
            'numbers' => $numbers,
            'debug' => [
                'input_id' => $id,
                'room_id' => $raffle->room_id,
                'raffle_id' => $raffle->id,
                'total_numbers' => count($numbers),
            ]
        ]);
    }
    
    
    
    

    /**
     * Muestra el sorteo para los jugadores.
     */
    public function viewSorteo($roomId)
    {
        $room = Room::findOrFail($roomId);
        $player = Auth::guard('player')->user();
    
        if (!$player) {
            return redirect()->route('player.login.form')->withErrors('Debes iniciar sesión.');
        }
    
        // ✅ Obtener los cartones del jugador en esta sala
        $playerCards = $player->cards()->where('room_id', $roomId)->get();
    
        // ✅ Obtener los números sorteados en esta sala
        $generatedNumbers = RaffleNumber::whereHas('raffle', function ($query) use ($roomId) {
            $query->where('room_id', $roomId);
        })->pluck('number')->toArray();
    
        // ✅ Retornar la vista con los datos correctos
        return view('player.sorteo', compact('room', 'playerCards', 'generatedNumbers'));
    }
    
    public function myCardsByRoom($roomId)
    {
        $room = Room::findOrFail($roomId);
        $player = Auth::guard('player')->user();
    
        if (!$player) {
            return redirect()->route('player.login.form')->withErrors('Debes iniciar sesión.');
        }
    
        // ✅ Obtener los cartones del jugador en esta sala
        $playerCards = $player->cards()->where('room_id', $roomId)->get();
    
        // ✅ Obtener los números sorteados en esta sala
        $generatedNumbers = RaffleNumber::whereHas('raffle', function ($query) use ($roomId) {
            $query->where('room_id', $roomId);
        })->pluck('number')->toArray();
    
        // ✅ Retornar la vista asegurando que se envía $generatedNumbers
        return view('player.my-cards', compact('room', 'playerCards', 'generatedNumbers'));
    }
    




    public function createNewRaffle($roomId, Request $request)
    {
        $room = Room::findOrFail($roomId);
    
        // Crear el nuevo sorteo sin afectar cartones anteriores
        $raffle = Raffle::create([
            'room_id' => $room->id,
            'agent_id' => auth()->id(),
            'name' => $request->name,
            'game_type' => $request->game_type,
            'prize' => $request->prize,
            'status' => 'Pendiente'
        ]);
    
        return redirect()->route('agent.raffles.show', $raffle->id)->with('success', 'Nuevo sorteo creado correctamente.');
    }
    



    public function checkWinnerAPI()
    {
        $winner = Winner::latest()->first(); // Obtener el último ganador
    
        if ($winner) {
            return response()->json([
                'message' => '¡Tenemos un ganador!',
                'player_id' => $winner->player_id,
                'prize' => $winner->prize
            ]);
        }
    
        return response()->json(['message' => 'Aún no hay ganadores.']);
    }
    



    //sistema de ganadores

  
    /**
     * Verifica si hay un ganador en el sorteo y lo finaliza si es necesario.
     */
    public function checkWinner($raffleId)
    {
        $raffle = Raffle::findOrFail($raffleId);
    
        if ($raffle->status === 'Finalizado') {
            return response()->json(['message' => 'Este sorteo ya ha finalizado.']);
        }
    
        $numbersDrawn = RaffleNumber::where('raffle_id', $raffleId)->pluck('number')->toArray();
        $bingoCards = BingoCard::where('raffle_id', $raffleId)->where('status', 'Comprado')->get();
    
        foreach ($bingoCards as $card) {
            $cardNumbers = json_decode($card->card_data, true);
            $flatNumbers = array_merge(...array_values($cardNumbers));
    
            if ($this->isWinner($flatNumbers, $numbersDrawn, $raffle->game_type)) {
                DB::transaction(function () use ($raffle, $card) {
                    Winner::create([
                        'raffle_id' => $raffle->id,
                        'player_id' => $card->player_id,
                        'bingo_card_id' => $card->id,
                        'prize' => $raffle->prize,
                    ]);
    
                    $raffle->status = 'Finalizado';
                    $raffle->save();
                    $card->status = 'Ganador';
                    $card->save();
                });
    
                // 🔹 Si hay otro sorteo en la sala, pasamos al siguiente
                $nextRaffle = Raffle::where('room_id', $raffle->room_id)
                                    ->where('status', 'Pendiente')
                                    ->first();
    
                if ($nextRaffle) {
                    return response()->json(['message' => '¡Ganador encontrado! Pasando al siguiente sorteo.', 'next_raffle_id' => $nextRaffle->id]);
                } else {
                    return response()->json(['message' => '¡Ganador encontrado! No hay más sorteos en esta sala.']);
                }
            }
        }
    
        return response()->json(['message' => 'Aún no hay ganadores.']);
    }
    

    /**
     * Verifica si un cartón cumple con la condición de victoria según la modalidad del juego.
     */
    private function isWinner($flatNumbers, $numbersDrawn, $gameType)
    {
        switch ($gameType) {
            case 'Cartón lleno':
                return empty(array_diff($flatNumbers, $numbersDrawn));
    
            case 'Línea':
                return $this->checkLineWin($flatNumbers, $numbersDrawn);
    
            case 'Cruz':
                $crossIndexes = [2, 10, 12, 14, 22]; // Posiciones en una tabla de 5x5 para la cruz
                return count(array_intersect(array_intersect_key($flatNumbers, array_flip($crossIndexes)), $numbersDrawn)) === 5;
    
            case 'Cuatro esquinas':
                return count(array_intersect([$flatNumbers[0], $flatNumbers[4], $flatNumbers[count($flatNumbers)-5], end($flatNumbers)], $numbersDrawn)) === 4;
    
            case 'Esquinas dobles':
                return count(array_intersect([$flatNumbers[0], $flatNumbers[4], $flatNumbers[count($flatNumbers)-5], end($flatNumbers)], $numbersDrawn)) === 4;
    
            case 'X':
                $xIndexes = [0, 4, 6, 8, 16, 18, 24]; // Índices de la X en una tabla 5x5
                return count(array_intersect(array_intersect_key($flatNumbers, array_flip($xIndexes)), $numbersDrawn)) === 7;
    
            default:
                return false;
        }
    }
    



}
