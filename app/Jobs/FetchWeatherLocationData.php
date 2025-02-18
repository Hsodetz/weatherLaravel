<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Events\WeatherDataUpdated;

class FetchWeatherLocationData implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $this->validateUserCoordinates();

        $cacheKey = "weather_user_{$this->user->id}";

        try {

            $gridResponse = Http::withHeaders([
                'User-Agent' => 'Laravel (pulgas@gmail.com)',
            ])->retry(3, 100)->timeout(0.5)->get("https://api.weather.gov/points/{$this->user->latitude},{$this->user->longitude}");

            if ($gridResponse->successful()) {

                $gridData = $gridResponse->json();
                $gridId = $gridData['properties']['gridId'];
                $gridX = $gridData['properties']['gridX'];
                $gridY = $gridData['properties']['gridY'];

                $forecastResponse = Http::retry(3, 100)->timeout(0.5)->get("https://api.weather.gov/gridpoints/{$gridId}/{$gridX},{$gridY}/forecast");

                if ($forecastResponse->successful()) {

                    $weatherData = $forecastResponse->json();

                       // Verificar si la estructura de los datos es la esperada
                       if (isset($weatherData['properties']['periods'])) {
                        $forecastPeriods = $weatherData['properties']['periods'];

                        // Almacenar los datos en caché
                        if (Cache::put($cacheKey, [
                            'data' => $forecastPeriods,
                            'last_updated' => now(),
                        ], 3600)) { // 60 minutos de duración en caché
                            Log::info('Datos guardados en caché correctamente.');
                            event(new WeatherDataUpdated($this->user->id, $forecastPeriods));

                        } else {
                            Log::error('Error al guardar los datos en caché.');
                        }
                    } else {
                        Log::error('Estructura del pronóstico inválida: ' . json_encode($weatherData));
                    }

                } else {
                    Log::error('Error al obtener datos para el usuario: ' . $this->user->id);
                    return ['error' => 'Error al obtener datos de la API.'];
                }
            } else {
                Log::error('Error al obtener el punto de cuadrícula: ' . $gridResponse->status() . ' - ' . $gridResponse->body());
                return ['error' => 'Error al obtener datos de la API.'];
            }

        } catch (\Exception $e) {
            Log::error('La API no está disponible en este momento: ' . $e->getMessage());
            return ['error' => 'La API no está disponible en este momento.'];
        }
    }

    private function validateUserCoordinates()
    {
        $latitudeMin = -90;
        $latitudeMax = 90;
        $longitudeMin = -180;
        $longitudeMax = 180;


        // Recupera las coordenadas del usuario
        $latitude = $this->user->latitude;
        $longitude = $this->user->longitude;


        if ($latitude < $latitudeMin || $latitude > $latitudeMax) {
            Log::error('La latitud está fuera del rango permitido');
            return response()->json(['message' => 'Las coordenadas del usuario son inválidas.'], 400);
        }

        if ($longitude < $longitudeMin || $longitude > $longitudeMax) {
            Log::error('La longitud está fuera del rango permitido');
            return response()->json(['message' => 'Las coordenadas del usuario son inválidas.'], 400);
        }

        // Si las coordenadas son válidas, continúa con el flujo normal
        Log::error('La longitud está fuera del rango permitido');
        return response()->json(['message' => 'Las coordenadas del usuario son válidas.'], 200);
    }
}
