<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use App\Jobs\FetchWeatherLocationData;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class WeatherService
{
    public function getWeather($user)
    {
        $cacheKey = "weather_user_{$user->id}";

        if (Cache::has($cacheKey)) {
            $cachedData = Cache::get($cacheKey);

            // Verificar que la estructura de los datos sea la esperada
            if (isset($cachedData['last_updated']) && isset($cachedData['data'])) {
                $lastUpdated = Carbon::parse($cachedData['last_updated']);
                $diffInMinutes = $lastUpdated->diffInMinutes(now());
                Log::info('obteniedno la diferencia en minutos');

                if ($diffInMinutes < 60 ) {
                    Log::info('ha pasasdo una hora');
                    return $cachedData['data'];
                }
            } else {
                Log::warning("Datos de clima en caché con estructura incorrecta para el usuario {$user->id}");
            }
        }

        FetchWeatherLocationData::dispatch($user);

        return [
            'message' => 'Datos en proceso de obtención. Por favor, actualice la página en unos momentos.',
        ];
    }



}
