<?php

require_once __DIR__ . '/DataSource.php';
require_once __DIR__ . '/NewsDataSource.php';
require_once __DIR__ . '/WeatherDataSource.php';
require_once __DIR__ . '/ShopDataSource.php';

class DataFetcher {
    private $sources = [];
    
    public function __construct() {
        // initialize news data source
        if (!empty(NEWS_API_KEY)) {
            $this->sources['news'] = new NewsDataSource();
        }
        
        // initialize weather data source
        if (!empty(WEATHER_API_KEY)) {
            $this->sources['weather'] = new WeatherDataSource();
        }

         // initialize shop data source
        if (!empty(SHOP_API_KEY)) {
            $this->sources['shop'] = new ShopDataSource();
        }
    }
    
    // get list of available data sources
    public function getAvailableSources() {
        $available = [];
        foreach ($this->sources as $key => $source) {
            $available[$key] = $source->getName();
        }
        return $available;
    }
    
    // fetch data from specified source
    public function fetchFromSource($sourceId, $params = []) {
        if (!isset($this->sources[$sourceId])) {
            throw new Exception("Data source '{$sourceId}' is not available");
        }
        
        return $this->sources[$sourceId]->fetchData($params);
    }
    
    // get paginated data
    public function paginateData($data, $page = 1, $perPage = RECORDS_PER_PAGE) {
        $page = max(1, intval($page));
        $perPage = max(1, intval($perPage));
        
        $totalItems = count($data);
        $totalPages = ceil($totalItems / $perPage);
        $page = min($page, max(1, $totalPages));
        
        $offset = ($page - 1) * $perPage;
        $paginatedData = array_slice($data, $offset, $perPage);
        
        return [
            'data' => $paginatedData,
            'pagination' => [
                'currentPage' => $page,
                'totalPages' => $totalPages,
                'totalItems' => $totalItems,
                'perPage' => $perPage,
                'hasNext' => $page < $totalPages,
                'hasPrev' => $page > 1
            ]
        ];
    }
}

