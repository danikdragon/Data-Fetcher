<?php
//setup verification script

error_reporting(E_ALL);
ini_set('display_errors', 0);

$checks = [];
$allPassed = true;

// check PHP version
$phpVersion = phpversion();
$phpOk = version_compare($phpVersion, '8.0.0', '>=');
$checks[] = [
    'name' => 'PHP Version',
    'status' => $phpOk,
    'message' => $phpOk ? "PHP {$phpVersion} (OK)" : "PHP {$phpVersion} (Requires 8.0+)"
];
if (!$phpOk) $allPassed = false;

// check required extensions
$requiredExtensions = ['curl', 'json'];
foreach ($requiredExtensions as $ext) {
    $loaded = extension_loaded($ext);
    $checks[] = [
        'name' => "PHP Extension: {$ext}",
        'status' => $loaded,
        'message' => $loaded ? "Loaded" : "Not loaded (required)"
    ];
    if (!$loaded) $allPassed = false;
}

// check optional extensions
$optionalExtensions = ['pdo', 'pdo_mysql'];
foreach ($optionalExtensions as $ext) {
    $loaded = extension_loaded($ext);
    $checks[] = [
        'name' => "PHP Extension: {$ext}",
        'status' => $loaded,
        'message' => $loaded ? "Loaded" : "Not loaded (optional)"
    ];
}

// check if config file exists
$configExists = file_exists(__DIR__ . '/config.php');
$checks[] = [
    'name' => 'Configuration File',
    'status' => $configExists,
    'message' => $configExists ? "Found" : "Not found"
];
if (!$configExists) $allPassed = false;

// check API keys if config exists
if ($configExists) {
    require_once __DIR__ . '/config.php';
    
    $newsKeyOk = defined('NEWS_API_KEY') && 
                 !empty(NEWS_API_KEY);
    $checks[] = [
        'name' => 'News API Key',
        'status' => $newsKeyOk,
        'message' => $newsKeyOk ? "Configured" : "Not configured (required for News source)"
    ];

    $shopKeyOk = defined('SHOP_API_KEY') && !empty(SHOP_API_KEY);
    $checks[] = [
        'name' => 'Shop API Key',
        'status' => $shopKeyOk,
        'message' => $shopKeyOk ? "Configured" : "Not configured (required for Shop source)"
    ];
    
    $weatherKeyOk = defined('WEATHER_API_KEY') && 
                    !empty(WEATHER_API_KEY);
    $checks[] = [
        'name' => 'Weather API Key',
        'status' => $weatherKeyOk,
        'message' => $weatherKeyOk ? "Configured" : "Not configured (required for Weather source)"
    ];
    
    // check database connection
    if (defined('DB_HOST') && !empty(DB_NAME)) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $pdo = new PDO($dsn, DB_USER, DB_PASS);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $checks[] = [
                'name' => 'Database Connection',
                'status' => true,
                'message' => "Connected (optional)"
            ];
        } catch (PDOException $e) {
            $checks[] = [
                'name' => 'Database Connection',
                'status' => false,
                'message' => "Failed (optional - app works without database)"
            ];
        }
    }
}

// check if class files exist
$requiredClasses = [
    'classes/DataSource.php',
    'classes/NewsDataSource.php',
    'classes/WeatherDataSource.php',
    'classes/DataFetcher.php',
    'classes/NewsDataSource.php'

];
foreach ($requiredClasses as $class) {
    $exists = file_exists(__DIR__ . '/' . $class);
    $checks[] = [
        'name' => "Class File: {$class}",
        'status' => $exists,
        'message' => $exists ? "Found" : "Not found"
    ];
    if (!$exists) $allPassed = false;
}

// check if frontend files exist
$frontendFiles = ['css/style.css', 'js/app.js', 'index.php', 'api.php'];
foreach ($frontendFiles as $file) {
    $exists = file_exists(__DIR__ . '/' . $file);
    $checks[] = [
        'name' => "File: {$file}",
        'status' => $exists,
        'message' => $exists ? "Found" : "Not found"
    ];
    if (!$exists) $allPassed = false;
}

// check write permissions
$writable = is_writable(__DIR__);
$checks[] = [
    'name' => 'Directory Permissions',
    'status' => $writable,
    'message' => $writable ? "Writable" : "Not writable (may affect logging)"
];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Verification - Data Fetcher</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            margin-bottom: 30px;
        }
        .check-item {
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 5px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .check-item.pass {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        .check-item.fail {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
        .check-item.warning {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
        }
        .status {
            font-weight: bold;
        }
        .summary {
            margin-top: 30px;
            padding: 20px;
            border-radius: 5px;
            text-align: center;
            font-size: 1.2em;
        }
        .summary.success {
            background: #d4edda;
            color: #155724;
        }
        .summary.error {
            background: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Data Fetcher - Setup Verification</h1>
        
        <?php foreach ($checks as $check): ?>
            <div class="check-item <?php 
                echo $check['status'] ? 'pass' : (strpos($check['message'], 'optional') !== false ? 'warning' : 'fail'); 
            ?>">
                <span><?php echo htmlspecialchars($check['name']); ?></span>
                <span class="status"><?php echo htmlspecialchars($check['message']); ?></span>
            </div>
        <?php endforeach; ?>
        
        <div class="summary <?php echo $allPassed ? 'success' : 'error'; ?>">
            <?php if ($allPassed): ?>
                All required checks passed! Your application should be ready to use.
            <?php else: ?>
                Some checks failed. Please review the issues above and fix them.
            <?php endif; ?>
        </div>
        
        <div style="margin-top: 30px; text-align: center;">
            <a href="index.php" style="display: inline-block; padding: 10px 20px; background: #667eea; color: white; text-decoration: none; border-radius: 5px;">
                Go to Application
            </a>
        </div>
    </div>
</body>
</html>

