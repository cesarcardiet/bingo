<?php

namespace App\Traits;

trait BingoHelper
{
    public function generateBingoCardData()
    {
        $columns = ['B', 'I', 'N', 'G', 'O'];
        $card = [];

        foreach ($columns as $index => $letter) {
            $min = $index * 15 + 1;
            $max = $min + 14;
            $card[$letter] = array_rand(array_flip(range($min, $max)), 5);
        }

        // Añadir espacio libre en la columna N, posición central
        $card['N'][2] = 'X';

        return $card;
    }
}
