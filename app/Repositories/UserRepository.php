<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository
{
    public function getAllUsers()
    {
        $users = User::select('id', 'name', 'latitude', 'longitude')->limit(20)->get();

        return $users;
    }

}

