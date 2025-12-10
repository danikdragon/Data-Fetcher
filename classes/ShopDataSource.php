<?php
require_once __DIR__ . '/DataSource.php';

class ShopDataSource extends DataSource {

    public function __construct() {
        parent::__construct('Shop', SHOP_API_KEY, SHOP_API_URL);
    }

    public function fetchData($params = []) {
        $q = trim($params['q'] ?? 'iphone');
        
        $url = $this->apiUrl . '?engine=ebay&api_key=' . urlencode($this->apiKey);
        $url .= '&_nkw=' . urlencode($q);
        $url .= '&_ipg=20';
        $url .= '&_pgn=' . max(1, (int)($params['page'] ?? 1));

        $response = $this->makeRequest($url, ['Accept: application/json']);

        if (isset($response['error'])) {
            throw new Exception('eBay API error: ' . $response['error']);
        }

        return $this->formatData($response['organic_results'] ?? []);
    }

    protected function formatData($rawData) {
        $formatted = [];

        foreach ($rawData as $item) {
            // $price = $item['price']['value'] ?? 0;
            // $currency = $item['price']['currency'] ?? 'USD';

            $formatted[] = [
                'id'          => $item['position'] ?? uniqid(),
                'title'       => $this->sanitizeOutput($item['title'] ?? 'No title'),
                'description' => $this->sanitizeOutput($item['extensions'][0] ?? $item['snippet'] ?? ''),
                'url'         => $this->sanitizeOutput($item['link'] ?? '#'),
                'source'      => $this->sanitizeOutput($item['source'] ?? 'eBay'),
                'imageUrl'    => $this->sanitizeOutput($item['thumbnail'] ?? '')
            ];
        }

        return $formatted;
    }
}