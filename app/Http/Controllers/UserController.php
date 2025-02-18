<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use App\Models\User;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function getUsersWeather()
    {
        $usersWithWeather = $this->userService->getUsersWeather();
        return response()->json($usersWithWeather);
    }

    public function showWeather($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['error' => 'Usuario no encontrado.'], 404);
        }
        $weather = $this->userService->getWeather($user);
        return response()->json($weather);
    }

}
