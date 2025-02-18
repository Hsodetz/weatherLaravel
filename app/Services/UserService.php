<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;

class UserService
{
    protected $weatherService;

    public function __construct(WeatherService $weatherService)
    {
        $this->weatherService = $weatherService;
    }

    public function getUsersWeather()
    {
        $userRepository = new UserRepository();
        $users = $userRepository->getAllUsers();

        $usersWithWeather = [];
        foreach ($users as $user) {
            $weather = $this->weatherService->getWeather($user);
            $usersWithWeather[] = [
                'user' => $user,
                'weather' => $weather,
            ];
        }

        return $usersWithWeather;
    }

    public function getWeather($user)
    {
        return $this->weatherService->getWeather($user);
    }

}
