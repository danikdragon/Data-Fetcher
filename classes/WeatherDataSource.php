<?php
require_once __DIR__ . '/DataSource.php';

class WeatherDataSource extends DataSource {
    
    public function __construct() {
        parent::__construct(
            'Weather',
            WEATHER_API_KEY,
            WEATHER_API_URL
        );
    }
    
    public function fetchData($params = []) {
        if (empty($this->apiKey) || $this->apiKey === 'https://api.openweathermap.org/data/2.5/weather') {
            throw new Exception("Weather API key is not configured.");
        }
        
        $queryParams = [
            'appid' => trim($this->apiKey),
            'q'     => isset($params['city']) && !empty($params['city']) ? trim($params['city']) : 'London',
            'units' => isset($params['units']) ? trim($params['units']) : 'metric',
            'lang'  => 'en'
        ];
        
        $url = $this->apiUrl . '?' . http_build_query($queryParams);
        
        try {
            $response = $this->makeRequest($url);
        } catch (Exception $e) {
            throw new Exception("Weather API request failed: " . $e->getMessage());
        }
        
        if (isset($response['cod']) && (int)$response['cod'] !== 200) {
            $errorMsg = $response['message'] ?? 'Unknown error';
            throw new Exception("Weather API error: " . $errorMsg);
        }
        
        return $this->formatData($response);
    }
    
    protected function formatData($rawData) {
        return [[
            'id' => md5(($rawData['name'] ?? '') . ($rawData['dt'] ?? time())),
            'city' => $this->sanitizeOutput($rawData['name'] ?? 'Unknown'),
            'country' => $this->sanitizeOutput($rawData['sys']['country'] ?? 'Unknown'),
            'temperature' => isset($rawData['main']['temp']) 
                ? round($rawData['main']['temp'], 1) 
                : 'N/A',
            'feelsLike' => isset($rawData['main']['feels_like']) 
                ? round($rawData['main']['feels_like'], 1) 
                : 'N/A',
            'description' => $this->sanitizeOutput(
                ucfirst($rawData['weather'][0]['description'] ?? 'N/A')
            ),
            'humidity' => $rawData['main']['humidity'] ?? 'N/A',
            'pressure' => $rawData['main']['pressure'] ?? 'N/A',
            'windSpeed' => isset($rawData['wind']['speed']) 
                ? round($rawData['wind']['speed'], 1) 
                : 'N/A',
            'windDirection' => isset($rawData['wind']['deg']) 
                ? $this->getWindDirection($rawData['wind']['deg']) 
                : 'N/A',
            'visibility' => isset($rawData['visibility']) 
                ? round($rawData['visibility'] / 1000, 1) 
                : 'N/A',
            'icon' => isset($rawData['weather'][0]['icon']) 
                ? 'https://openweathermap.org/img/w/' . $rawData['weather'][0]['icon'] . '.png'
                : '',
            'timestamp' => isset($rawData['dt']) 
                ? date('Y-m-d H:i:s', $rawData['dt']) 
                : date('Y-m-d H:i:s')
        ]];
    }
    
    private function getWindDirection($degrees) {
        $directions = ['N', 'NNE', 'NE', 'ENE', 'E', 'ESE', 'SE', 'SSE', 
                      'S', 'SSW', 'SW', 'WSW', 'W', 'WNW', 'NW', 'NNW'];
        $index = round($degrees / 22.5) % 16;
        return $directions[$index];
    }
}