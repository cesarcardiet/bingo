<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Player;
use App\Models\Agent;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\BingoCard;
use App\Models\Room;
use App\Models\RaffleNumber;
use App\Models\Raffle;
use App\Traits\BingoHelper;



class PlayerController extends Controller
{
    use BingoHelper;
    public function __construct()
    {
        $this->middleware('auth:player')->except(['register', 'showRegistrationForm']);
        $this->middleware(function ($request, $next) {
            header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
            header("Pragma: no-cache");
            header("Expires: Sat, 01 Jan 2000 00:00:00 GMT");
            return $next($request);
        });
    }

    /**
     * Muestra el formulario de registro para jugadores con código de referido.
     */
    public function showRegistrationForm(Request $request)
    {
        $referralCode = $request->query('ref');
    
        // Buscar al agente usando `referral_id`
        $agent = Agent::where('referral_id', $referralCode)->first();
    
        if (!$agent) {
            return redirect('/register/player')->withErrors('El código de referido no es válido.');
        }
    
        return view('auth.player-register', compact('referralCode'));
    }

    /**
     * Registra un nuevo jugador con código de referido válido.
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:players',
            'password' => 'required|string|min:6|confirmed',
        ]);
    
        // Buscar al agente usando el código de referido
        $agent = Agent::where('referral_id', $request->referral_id)->first();
    
        if (!$agent) {
            return redirect()->back()->withErrors('El código de referido no es válido.');
        }
    
        // Crear el jugador con el agent_id
        Player::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'agent_id' => $agent->id,
            'referral_id' => $request->referral_id,
        ]);
    
        return redirect('/login/player')->with('success', 'Registro exitoso. Ahora puedes iniciar sesión.');
    }

    /**
     * Muestra el dashboard del jugador autenticado.
     */
    public function dashboard()
    {
        $player = Auth::guard('player')->user();
    
        // Obtener solo las salas activas del agente asignado al jugador
        $rooms = Room::where('agent_id', $player->agent_id)->get();
    
        return view('player.dashboard', compact('player', 'rooms'));
    }
    
    /**
     * Permite a un jugador comprar cartones.
     */
    public function buyCard(Request $request, $roomId)
    {
        $room = Room::findOrFail($roomId);
    
        // 🔹 Verificar si hay un sorteo activo en la sala antes de permitir la compra
        $activeRaffles = Raffle::where('room_id', $roomId)
                               ->where('status', 'Pendiente')
                               ->exists();
    
        if (!$activeRaffles) {
            return back()->with('error', 'No hay sorteos activos en esta sala. No puedes comprar cartones aún.');
        }
    
        // Lógica de compra normal
        $player = auth()->user();
    
        if ($player->balance < $room->card_price) {
            return back()->with('error', 'Saldo insuficiente para comprar un cartón.');
        }
    
        // 🔹 Generar número único para el cartón
        $cardNumber = BingoCard::where('room_id', $roomId)->max('card_number') + 1;
    
        $cardData = $this->generateBingoCardData(); // Asegurar que esta función existe en el controlador
    
        $card = BingoCard::create([
            'room_id' => $roomId,
            'player_id' => $player->id,
            'card_number' => $cardNumber,
            'card_data' => json_encode($cardData),
            'status' => 'Comprado'
        ]);
    
        // Descontar saldo del jugador
        $player->balance -= $room->card_price;
        $player->save();
    
        return redirect()->route('player.sala', ['roomId' => $roomId])

                         ->with('success', 'Cartón comprado exitosamente.');
    }
    
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

    
    

public function showRoom($roomId)
{
    $room = Room::findOrFail($roomId);
    $availableCards = BingoCard::where('room_id', $roomId)
                               ->where('status', 'Disponible')
                               ->get();

                               return redirect()->route('player.room.show', ['room' => $roomId])
                               ->with('success', 'Cartón comprado exitosamente.');
                           
}



    /**
     * Muestra los sorteos de una sala específica donde el jugador ha comprado cartones.
     */
    public function viewSorteo($roomId)
    {
        $player = Auth::guard('player')->user();
        $room = Room::findOrFail($roomId);

        $playerCards = BingoCard::where('player_id', $player->id)
            ->where('room_id', $roomId)
            ->get();

        if ($playerCards->isEmpty()) {
            return redirect()->route('player.dashboard')->withErrors('No tienes cartones en esta sala.');
        }

        return view('player.sorteo', compact('room', 'playerCards'));
    }

    /**
     * Muestra las salas en las que el jugador ha comprado cartones.
     */
    public function myCards(Request $request)
    {
        $player = Auth::guard('player')->user();
        
        // Obtener el ID de la sala desde la URL
        $roomId = $request->query('room_id');
        if (!$roomId) {
            return redirect()->route('player.dashboard')->withErrors('No se ha seleccionado una sala.');
        }
    
        // Verificar si la sala existe
        $room = Room::find($roomId);
        if (!$room) {
            return redirect()->route('player.dashboard')->withErrors('La sala no existe.');
        }
    
        // Obtener los cartones comprados por el jugador en esta sala
        $playerCards = BingoCard::where('player_id', $player->id)
                                ->where('room_id', $roomId)
                                ->get();
    
        // Obtener los números sorteados en esta sala
        $generatedNumbers = RaffleNumber::whereHas('raffle', function ($query) use ($roomId) {
            $query->where('room_id', $roomId);
        })->pluck('number')->toArray();
    
        return view('player.my-cards', compact('room', 'playerCards', 'generatedNumbers'));
    }
    
    
    

    /**
     * Muestra los cartones comprados en una sala específica.
     */
    public function myCardsByRoom($room_id)
    {
        $player = Auth::guard('player')->user();
        $room = Room::findOrFail($room_id);

        $cards = BingoCard::where('room_id', $room->id)
                          ->where('player_id', $player->id)
                          ->get();

        return view('player.my-cards-by-room', compact('room', 'cards'));
    }
}
