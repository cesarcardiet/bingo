<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\BingoCard;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Traits\BingoHelper;

class RoomController extends Controller
{
    use BingoHelper;
    /**
     * Muestra la lista de salas del agente autenticado.
     */
    public function index()
    {
        $agent = Auth::guard('agent')->user();

        if (!$agent) {
            return redirect()->route('agent.login.form')->withErrors('Debes iniciar sesión.');
        }

        $rooms = Room::where('agent_id', $agent->id)->get();
        
        return view('agent.rooms.index', compact('rooms'));
    }

    /**
     * Muestra el formulario para crear una nueva sala.
     */
    public function create()
    {
        return view('agent.rooms.create');
    }

    /**
     * Almacena una nueva sala creada por el agente autenticado y genera cartones.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'start_time' => 'required|date',
            'total_prizes' => 'required|numeric|min:0',
            'card_price' => 'required|numeric|min:0',
            'total_cards' => 'required|integer|min:1',
        ]);
    
        // Crear la sala
        $room = Room::create([
            'agent_id' => Auth::guard('agent')->id(),
            'name' => $request->name,
            'description' => $request->description,
            'start_time' => $request->start_time,
            'total_prizes' => $request->total_prizes,
            'card_price' => $request->card_price,
            'total_cards' => $request->total_cards,
            'status' => 'active',
        ]);
    
        // Generar cartones
        for ($i = 1; $i <= $request->total_cards; $i++) {
            BingoCard::create([
                'room_id' => $room->id,
                'card_number' => $i,
                'card_data' => json_encode($this->generateBingoCardData()),
                'status' => 'Disponible',
            ]);
            
        }
    
        return redirect()->route('rooms.index')->with('success', 'Sala creada correctamente con cartones generados.');
    }
    

    /**
     * Genera datos para un cartón de bingo.
     */
    private function generateBingoCardData()
    {
        $columns = ['B', 'I', 'N', 'G', 'O'];
        $card = [];

        foreach ($columns as $index => $letter) {
            $min = $index * 15 + 1;
            $max = $min + 14;
            $card[$letter] = array_rand(array_flip(range($min, $max)), 5);
        }

        return $card;
    }

    /**
     * Muestra el formulario para editar una sala.
     */
    public function edit(Room $room)
    {
        if ($room->agent_id !== Auth::guard('agent')->id()) {
            return redirect()->route('rooms.index')->withErrors('No tienes permiso para editar esta sala.');
        }

        return view('agent.rooms.edit', compact('room'));
    }

    /**
     * Actualiza los datos de la sala.
     */
    public function update(Request $request, Room $room)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_time' => 'required|date',
            'total_prizes' => 'required|numeric|min:0',
            'card_price' => 'required|numeric|min:1',
            'total_cards' => 'required|integer|min:1',
            'max_players' => 'nullable|integer|min:1',
            'status' => 'required|in:active,inactive',
        ]);

        if ($room->agent_id !== Auth::guard('agent')->id()) {
            return redirect()->route('rooms.index')->withErrors('No tienes permiso para modificar esta sala.');
        }

        $room->update($request->all());

        return redirect()->route('rooms.index')->with('success', 'Sala actualizada correctamente.');
    }

    /**
     * Muestra una sala y sus cartones disponibles para jugadores.
     */
    public function show($id)
    {
        $room = Room::with('raffles')->findOrFail($id);
    
        // Obtener cartones disponibles en la sala
        $availableCards = BingoCard::where('room_id', $room->id)
            ->where('status', 'Disponible')
            ->get();
    
        return view('player.rooms.show', compact('room', 'availableCards'));
    }
    public function destroy(Room $room)
{
    // Eliminar la sala y sus sorteos asociados
    $room->raffles()->delete(); // 🔹 Elimina todos los sorteos relacionados
    $room->delete(); // 🔹 Elimina la sala

    return redirect()->route('rooms.index')->with('success', 'Sala eliminada con éxito.');
}


    
}
