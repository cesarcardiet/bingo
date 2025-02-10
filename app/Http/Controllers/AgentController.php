<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Agent;
use Illuminate\Support\Facades\Auth;

class AgentController extends Controller
{
    /**
     * Muestra el formulario de registro para agentes.
     */
    public function showRegistrationForm()
    {
        return view('agent.register');
    }

    /**
     * Registra un nuevo agente en la base de datos.
     */
    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'id_number' => 'required|string|unique:agents',
            'email' => 'required|email|unique:agents',
            'password' => 'required|string|min:6|confirmed',
            'phone' => 'required|string|unique:agents',
            'bank_name' => 'required|string',
        ]);

        // Encriptar contraseña
        $validatedData['password'] = bcrypt($validatedData['password']);

        // Crear el agente
        $agent = Agent::create($validatedData);

        return redirect()->route('agent.login.form')->with('success', 'Registro exitoso. Inicia sesión.');
    }

    /**
     * Muestra el dashboard del agente autenticado.
     */
    public function dashboard()
    {
        $agent = Auth::guard('agent')->user();
        $referralLink = url('/register/player?ref=' . $agent->referral_id); // 🔹 Usa el `referral_id`
        
        return view('agent.dashboard', compact('agent', 'referralLink'));
    }
    
    
}
