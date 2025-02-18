<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class WeatherDataUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $userId;
    public $weatherData;

    public function __construct($userId, $weatherData)
    {
        $this->userId = $userId;
        $this->weatherData = $weatherData;
    }

    public function broadcastOn()
    {
        Log::info('Evento enviado correctamente.');
        return new Channel('weather-updates.' . $this->userId);
    }
}
