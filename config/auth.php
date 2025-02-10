<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Configuración de Autenticación
    |--------------------------------------------------------------------------
    |
    | Se definen los valores predeterminados de autenticación para toda la aplicación.
    | Cada tipo de usuario (agentes, jugadores, usuarios normales) tiene su propio guard.
    |
    */

    'defaults' => [
        'guard' => 'web', // Puedes cambiarlo según el usuario más común
        'passwords' => 'users',
    ],

    /*
    |--------------------------------------------------------------------------
    | Guards de Autenticación
    |--------------------------------------------------------------------------
    |
    | Los guards controlan cómo los usuarios inician sesión y gestionan sesiones separadas
    | para jugadores, agentes y administradores.
    |
    */

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],
        'agent' => [
            'driver' => 'session',
            'provider' => 'agents',
        ],
        'player' => [
            'driver' => 'session',
            'provider' => 'players',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Proveedores de Usuarios
    |--------------------------------------------------------------------------
    |
    | Aquí se definen las fuentes de autenticación para cada tipo de usuario.
    | Se usa "eloquent" para manejar autenticación basada en modelos.
    |
    */

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => App\Models\User::class,
        ],
        'agents' => [
            'driver' => 'eloquent',
            'model' => App\Models\Agent::class,
        ],
        'players' => [
            'driver' => 'eloquent',
            'model' => App\Models\Player::class,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuración de Restablecimiento de Contraseña
    |--------------------------------------------------------------------------
    |
    | Se manejan por separado las opciones de restablecimiento de contraseña
    | para evitar conflictos entre jugadores, agentes y administradores.
    |
    */

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => 'password_reset_tokens',
            'expire' => 60, // Expira en 60 minutos
            'throttle' => 60, // Mínimo 60 segundos entre intentos
        ],
        'agents' => [
            'provider' => 'agents',
            'table' => 'password_reset_tokens_agents',
            'expire' => 60,
            'throttle' => 60,
        ],
        'players' => [
            'provider' => 'players',
            'table' => 'password_reset_tokens_players',
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Tiempo de Espera para Confirmación de Contraseña
    |--------------------------------------------------------------------------
    |
    | Define cuántos segundos pueden pasar antes de que el usuario tenga que
    | volver a ingresar su contraseña en áreas sensibles.
    |
    */

    'password_timeout' => 10800, // 3 horas (10800 segundos)
];
