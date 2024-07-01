<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\HelloController;
use GuzzleHttp\Client;

class HelloController extends Controller
{
    public function greet(Request $request)
    {
        $visitorName = $request->query('visitor_name', 'Visitor');
        $clientIp = $request->ip("");

        $client = new Client();
        $response = $client->get("http://ip-api.com/json/{$clientIp}");
        $locationData = json_decode($response->getBody()->getContents(), true);

        $Location = $locationData['city'] ?? 'Unknown location';

        
        $latitude = $locationData['lat'] ?? null;
        $longitude = $locationData['lon'] ?? null;

        $temperature = 'Unknown';

        if ($latitude && $longitude) {
            
            $apiKey = 'abc11e317743d6c5a5acd6e162f307bf';
            $weatherResponse = $client->get("http://api.openweathermap.org/data/2.5/weather", [
                'query' => [
                    'lat' => $latitude,
                    'lon' => $longitude,
                    'units' => 'metric',
                    'appid' => $apiKey
                ]
            ]);
            $weatherData = json_decode($weatherResponse->getBody()->getContents(), true);

            if (isset($weatherData['main']['temp'])) {
                $temperature = $weatherData['main']['temp'];
            }
        }

        return response()->json([
            'client_ip' => "$clientIp",
            'Location' => "$Location",
            'greeting' => "Hello, $visitorName!,the temperature is $temperature degrees Celcius in $Location"]);
    }
}
