# Data Fetcher

Simple web app that pulls data from three sources:
- News (Mediastack)
- Weather (OpenWeatherMap)
- Shop / Products (eBay via SerpApi)

Everything works with real API keys and falls back gracefully if something is missing.

### Features
- One-page interface
- Choose source → fill parameters → get results
- Pagination support
- Clean card layout for each source
- Setup verification script (`setup.php`)

### Requirements
- PHP 8.0+
- cURL + JSON extensions

### Quick Start

1. Copy the project to your server
2. Edit `config.php` and put your API keys  
   (keys are already filled for demo/testing)
3. Open `check_setup.php` in browser — it will tell you if everything is OK
4. Open `index.php` — ready

### API Keys (config.php)

```php
define('NEWS_API_KEY',   'your-news-key');
define('WEATHER_API_KEY','your-weather-key');
define('SHOP_API_KEY',   'your-serpapi-key');   // eBay search