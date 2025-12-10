<?php
abstract class DataSource {
    protected $name;
    protected $apiKey;
    protected $apiUrl;
    
    public function __construct($name, $apiKey, $apiUrl) {
        $this->name = $name;
        $this->apiKey = $apiKey;
        $this->apiUrl = $apiUrl;
    }
    
    // get source name
    public function getName() {
        return $this->name;
    }
    
    // fetch data from API
    abstract public function fetchData($params = []);
    
    // format raw data into standardized structure
    abstract protected function formatData($rawData);
    
    // make HTTP request
    protected function makeRequest($url, $headers = []) {
        $ch = curl_init();
        
        // set cURL options
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_USERAGENT => 'DataFetcher/1.0'
        ]);
        
        // execute request
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
                
        // check for errors
        if ($error) {
            throw new Exception("cURL error: " . $error);
        }
        
        $data = json_decode($response, true);
        
        // handle non-200 HTTP codes
        if ($httpCode !== 200) {
            // try to get error message from API response
            $errorMessage = "HTTP error: " . $httpCode;
            
            if (is_array($data)) {
                if (isset($data['message'])) {
                    $errorMessage = $data['message'];
                } elseif (isset($data['error'])) {
                    $errorMessage = $data['error'];
                } elseif (isset($data['cod']) && isset($data['message'])) {
                    $errorMessage = "API Error " . $data['cod'] . ": " . $data['message'];
                }
            }
            
            if ($httpCode === 401) {
                $errorMessage .= " (Invalid API key. Please check your API key in config.php)";
            } elseif ($httpCode === 404) {
                $errorMessage .= " (Resource not found. Please check your request parameters)";
            }
            
            throw new Exception($errorMessage);
        }
        
        // check for JSON decode errors
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("JSON decode error: " . json_last_error_msg());
        }
        
        return $data;
    }
    
    protected function sanitizeOutput($text) {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }
}

