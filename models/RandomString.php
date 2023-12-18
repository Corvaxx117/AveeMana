<?php

namespace Models;

class RandomString
{
    // GENERE UNE SUITE DE CARACTERE ALEATOIRE
    public function getRandomString(int $n): string
    {
        // Stocke toutes les lettres et chiffres possibles dans une chaîne.
        $str = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomStr = '';
        // Génére un index aléatoire de 0 à la longueur de la chaîne -1.
        // boucle for qui va itérer n fois. $n est une variable qui doit contenir le nombre de caractères dans la chaîne aléatoire
        for ($i = 0; $i < $n; $i++) {
            $index = rand(0, strlen($str) - 1);
            $randomStr .= $str[$index];
        }

        return $randomStr;
    }
}
