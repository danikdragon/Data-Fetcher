<?php
//API endpoint

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/classes/DataFetcher.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

// initialize response array
$response = [
    'success' => false,
    'data' => null,
    'error' => null,
    'pagination' => null
];

try {
    $method = $_SERVER['REQUEST_METHOD'];
    if (!in_array($method, ['GET', 'POST'])) {
        throw new Exception('Method not allowed');
    }
    $params = $method === 'POST' ? $_POST : $_GET;
    if (!isset($params['source']) || empty($params['source'])) {
        throw new Exception('Source parameter is required');
    }
    
    $sourceId = trim($params['source']);
    $fetcher = new DataFetcher();
    
    $availableSources = $fetcher->getAvailableSources();
    if (!isset($availableSources[$sourceId])) {
        throw new Exception('Invalid data source');
    }
    
    $sourceParams = [];
    
    $page = isset($params['page']) ? intval($params['page']) : 1;
    $perPage = isset($params['perPage']) ? intval($params['perPage']) : RECORDS_PER_PAGE;
    
    // source specific parameters
    if ($sourceId === 'news') {
        $sourceParams['country'] = isset($params['country']) ? trim($params['country']) : 'us';
        $sourceParams['category'] = isset($params['category']) ? trim($params['category']) : '';
        $sourceParams['page'] = $page;
        $sourceParams['pageSize'] = 100;
    } elseif ($sourceId === 'weather') {
        $sourceParams['city'] = isset($params['city']) ? trim($params['city']) : 'London';
        $sourceParams['units'] = isset($params['units']) ? trim($params['units']) : 'metric';
    }
    elseif ($sourceId === 'shop') {
        $sourceParams['q'] = $params['q'] ?? 'iphone';
        $sourceParams['page'] = $page;
        $sourceParams['pageSize'] = $perPage;
    }
            
    // fetch data
    $data = $fetcher->fetchFromSource($sourceId, $sourceParams);
    // paginate data
    $paginated = $fetcher->paginateData($data, $page, $perPage);
    
    $response['success'] = true;
    $response['data'] = $paginated['data'];
    $response['pagination'] = $paginated['pagination'];
    
} catch (Exception $e) {
    $response['success'] = false;
    $response['error'] = $e->getMessage();
    http_response_code(400);
}
echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

