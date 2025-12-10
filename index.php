<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/classes/DataFetcher.php';

$fetcher = new DataFetcher();
$availableSources = $fetcher->getAvailableSources();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Fetcher</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Data Fetcher</h1>
            <p class="subtitle">Retrieve data from various open sources</p>
        </header>

        <main>
            <section class="form-section">
                <form id="dataForm" method="POST" action="api.php">
                    <div class="form-group">
                        <label for="source">Data Source:</label>
                        <select id="source" name="source" required>
                            <option value="">-- Select a source --</option>
                            <?php foreach ($availableSources as $key => $name): ?>
                                <option value="<?= htmlspecialchars($key) ?>">
                                    <?= htmlspecialchars($name) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <?php if (empty($availableSources)): ?>
                            <p class="error-message" style="margin-top: 10px;">
                                No data sources available. Check config.php
                            </p>
                        <?php endif; ?>
                    </div>

                    <!-- shop product search -->
                    <div id="shopOptions" class="source-options" style="display: none;">
                        <div class="form-group">
                            <label for="shopSearch">Search products:</label>
                            <input type="text" id="shopSearch" name="q" placeholder="e.g. iphone, laptop">
                        </div>
                    </div>

                    <!-- news country and category filters -->
                    <div id="newsOptions" class="source-options" style="display: none;">
                        <div class="form-group">
                            <label for="country">Country:</label>
                            <select id="country" name="country">
                                <option value="us">United States</option>
                                <option value="gb">United Kingdom</option>
                                <option value="ca">Canada</option>
                                <option value="au">Australia</option>
                                <option value="de">Germany</option>
                                <option value="fr">France</option>
                                <option value="ru">Russia</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="category">Category:</label>
                            <select id="category" name="category">
                                <option value="">All</option>
                                <option value="business">Business</option>
                                <option value="entertainment">Entertainment</option>
                                <option value="general">General</option>
                                <option value="health">Health</option>
                                <option value="science">Science</option>
                                <option value="sports">Sports</option>
                                <option value="technology">Technology</option>
                            </select>
                        </div>
                    </div>

                    <!-- weather city and units -->
                    <div id="weatherOptions" class="source-options" style="display: none;">
                        <div class="form-group">
                            <label for="city">City:</label>
                            <input type="text" id="city" name="city" value="London" placeholder="e.g. Paris">
                        </div>
                        <div class="form-group">
                            <label for="units">Units:</label>
                            <select id="units" name="units">
                                <option value="metric">°C</option>
                                <option value="imperial">°F</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <button type="submit" id="submitBtn">Fetch Data</button>
                        <button type="button" id="resetBtn">Reset</button>
                    </div>
                </form>
            </section>

            <section id="resultsSection" class="results-section" style="display: none;">
                <div id="loadingIndicator" class="loading" style="display: none;">
                    <div class="spinner"></div>
                    <p>Loading data...</p>
                </div>

                <div id="errorMessage" class="error-message" style="display: none;"></div>
                <div id="resultsContainer"></div>
                <div id="paginationContainer" class="pagination"></div>
            </section>
        </main>

        <footer>
            <p>&copy; <?= date('Y') ?> All rights reserved from daniil</p>
        </footer>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/app.js"></script>
</body>
</html>