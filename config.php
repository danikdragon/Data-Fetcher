<?php

// database
define('DB_HOST', 'localhost');
define('DB_NAME', 'data_fetcher');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// app
define('RECORDS_PER_PAGE', 10);
define('CACHE_DURATION', 3600);

// api keys
define('NEWS_API_KEY', '145fcbebfeeaa4c621846468539b5ee2');
define('NEWS_API_URL', 'http://api.mediastack.com/v1/news');

define('WEATHER_API_KEY', 'eb5643f19e6336df50ce50e74046a962');
define('WEATHER_API_URL', 'https://api.openweathermap.org/data/2.5/weather');

define('SHOP_API_KEY', 'f67578c83eee251fa512b93cd3cd2959914af58e6d1c1f483debaf79370894fd');
define('SHOP_API_URL', 'https://serpapi.com/search');

// settings
date_default_timezone_set('UTC');
error_reporting(E_ALL);
ini_set('display_errors', 1);