<?php

namespace App\Services;

class PasswordGeneratorService
{
    /**
     * Generate a random password.
     *
     * @param int $length (The length of the password to generate)
     *
     * @return string (The generated password)
     */
    public function generatePassword(int $length = 10): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $password = '';

        for ($i = 0; $i < $length; $i++) {
            $password .= $characters[rand(0, $charactersLength - 1)];
        }

        return $password;
    }
}
