<?php
require_once __DIR__ . '/DataSource.php';

class NewsDataSource extends DataSource {
    
    public function __construct() {
        parent::__construct(
            'News',
            NEWS_API_KEY,
            NEWS_API_URL
        );
    }
    
    public function fetchData($params = []) {
        $url = $this->apiUrl . '?access_key=' . urlencode($this->apiKey);
        
        // country validation
        $validCountries = ['us', 'ru', 'gb', 'de', 'fr', 'br', 'ca', 'au'];
        $country = strtolower($params['country'] ?? 'us');
        if (!in_array($country, $validCountries)) {
            throw new Exception("Invalid country: $country");
        }
        $url .= '&countries=' . $country;
        
        // category validation
        $validCategories = ['general', 'business', 'entertainment', 'health', 'science', 'sports', 'technology'];
        $category = strtolower($params['category'] ?? '');
        if (!empty($category) && !in_array($category, $validCategories)) {
            throw new Exception("Invalid category: $category");
        }
        if (!empty($category)) {
            $url .= '&categories=' . $category;
        }
        
        $keywords = trim($params['q'] ?? '');
        if (!empty($keywords)) {
            $url .= '&keywords=' . urlencode($keywords);
        }
        
        $validLangs = ['en', 'ru', 'de', 'fr', 'es'];
        $lang = strtolower($params['lang'] ?? 'en');
        if (!in_array($lang, $validLangs)) {
            $lang = 'en'; // Fallback
        }
        $url .= '&languages=' . $lang;
        
        $pageSize = min(100, max(1, (int)($params['pageSize'] ?? 20)));
        $url .= '&limit=' . $pageSize;
        
        $page = max(1, (int)($params['page'] ?? 1));
        $offset = ($page - 1) * $pageSize;
        $url .= '&offset=' . $offset;
        
        $headers = ['Accept: application/json'];
        $response = $this->makeRequest($url, $headers);
        
        // error handling
        if (isset($response['success']) && $response['success'] !== true) {
            $errorMsg = $response['error_info']['message'] ?? 'Unknown error';
            $errorCode = $response['error_info']['code'] ?? 'N/A';
            throw new Exception("Mediastack error ($errorCode): $errorMsg");
        }
        
        return $this->formatData($response);
    }

    protected function formatData($rawData) {
        $formatted = [];
        
        foreach ($rawData['data'] ?? [] as $article) {
            $formatted[] = [
                'id'          => md5($article['url'] ?? uniqid()),
                'title'       => $this->sanitizeOutput($article['title'] ?? 'No title'),
                'description' => $this->sanitizeOutput($article['description'] ?? ''),
                'url'         => $this->sanitizeOutput($article['url'] ?? '#'),
                'source'      => $this->sanitizeOutput($article['source'] ?? 'Unknown'),
                'author'      => $this->sanitizeOutput($article['author'] ?? ''),
                'publishedAt' => date('Y-m-d H:i:s', strtotime($article['published_at'] ?? 'now')),
                'imageUrl'    => $this->sanitizeOutput($article['image'] ?? '') 
            ];
        }
        
        return $formatted;
    }
}

