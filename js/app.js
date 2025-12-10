$(document).ready(function () {
    let currentPage = 1;
    let currentSource = '';
    let currentParams = {};

    // handle source selection change
    $('#source').on('change', function () {
        const source = $(this).val();
        currentSource = source;

        // hide all source-specific options
        $('.source-options').hide();

        // show relevant options based on source
        if (source === 'news') {
            $('#newsOptions').show();
        } else if (source === 'weather') {
            $('#weatherOptions').show();
        } else if (source === 'shop') {
            $('#shopOptions').show();
        }
    });

    // handle form submission
    $('#dataForm').on('submit', function (e) {
        e.preventDefault();

        const source = $('#source').val();
        if (!source) {
            alert('Please select a data source');
            return;
        }

        // collect form parameters
        const params = {
            source: source,
            page: 1,
            perPage: 10
        };

        // add source-specific parameters
        if (source === 'news') {
            params.country = $('#country').val();
            params.category = $('#category').val() || '';
        } else if (source === 'weather') {
            params.city = $('#city').val();
            params.units = $('#units').val();
        } else if (source === 'shop') {
            params.q = $('#shopSearch').val() || 'iphone';
        }

        currentParams = params;
        currentPage = 1;

        // fetch data
        fetchData(params);
    });

    // handle reset button
    $('#resetBtn').on('click', function () {
        $('#dataForm')[0].reset();
        $('.source-options').hide();
        $('#resultsSection').hide();
        currentPage = 1;
        currentParams = {};
    });

    function fetchData(params) {
        $('#resultsSection').show();
        $('#loadingIndicator').show();
        $('#errorMessage').hide();
        $('#resultsContainer').empty();
        $('#paginationContainer').empty();

        $.ajax({
            url: 'api.php',
            method: 'POST',
            data: params,
            dataType: 'json',
            timeout: 30000,
            success: function (response) {
                $('#loadingIndicator').hide();
                if (response.success) {
                    displayResults(response.data, response.pagination, params.source);
                } else {
                    showError(response.error || 'An unknown error occurred');
                }
            },
            error: function (xhr, status, error) {
                $('#loadingIndicator').hide();
                let errorMsg = 'Failed to fetch data. ';
                if (status === 'timeout') errorMsg += 'Request timed out.';
                else if (xhr.responseJSON && xhr.responseJSON.error) errorMsg += xhr.responseJSON.error;
                else errorMsg += 'Check connection or API settings.';
                showError(errorMsg);
            }
        });
    }

    //display results based on source
    function displayResults(data, pagination, source) {
        const container = $('#resultsContainer');
        container.empty();

        if (!data || data.length === 0) {
            container.html('<p class="no-results">No products found.</p>');
            return;
        }
        console.log(source);

        if (source === 'news') {
            displayNewsResults(data, container);
        } else if (source === 'weather') {
            displayWeatherResults(data, container);
        } else if (source === 'shop') {
            displayShopResults(data, container);
        }

        displayPagination(pagination);
    }

    //news display
    function displayNewsResults(data, container) {
        data.forEach(function (article) {
            const card = $('<div>').addClass('news-card');
            const title = $('<h3>').html(
                $('<a>').attr('href', article.url).attr('target', '_blank').text(article.title)
            );
            const meta = $('<div>').addClass('news-meta')
                .append($('<span>').html('<strong>Source:</strong> ' + article.source))
                .append($('<span>').html('<strong>Published:</strong> ' + formatDate(article.publishedAt)));
            const desc = $('<div>').addClass('news-description').text(article.description);

            card.append(title).append(meta).append(desc);
            if (article.imageUrl) {
                card.append($('<img>').addClass('news-image').attr('src', article.imageUrl).attr('alt', article.title));
            }
            container.append(card);
        });
    }

    //weather display
    function displayWeatherResults(data, container) {
        data.forEach(function (weather) {
            const card = $('<div>').addClass('weather-card');
            const header = $('<div>').addClass('weather-header')
                .append($('<div>').addClass('weather-location').text(weather.city + ', ' + weather.country));
            if (weather.icon) {
                header.append($('<img>').addClass('weather-icon').attr('src', weather.icon));
            }
            const main = $('<div>').addClass('weather-main')
                .append($('<div>').addClass('weather-temp').text(weather.temperature + '°'))
                .append($('<div>').addClass('weather-desc').text(weather.description));
            card.append(header).append(main);
            container.append(card);
        });
    }



    //display pagination
    function displayPagination(pagination) {
        if (!pagination || pagination.totalPages <= 1) return;

        const container = $('#paginationContainer');
        container.empty();

        const prevBtn = $('<button>').text('Previous')
            .prop('disabled', !pagination.hasPrev)
            .on('click', () => {
                if (pagination.hasPrev) {
                    currentPage--;
                    currentParams.page = currentPage;
                    fetchData(currentParams);
                }
            });

        const info = $('<span>').addClass('pagination-info')
            .text(`Page ${pagination.currentPage} of ${pagination.totalPages}`);

        const nextBtn = $('<button>').text('Next')
            .prop('disabled', !pagination.hasNext)
            .on('click', () => {
                if (pagination.hasNext) {
                    currentPage++;
                    currentParams.page = currentPage;
                    fetchData(currentParams);
                }
            });

        container.append(prevBtn).append(info).append(nextBtn);
    }

    function showError(message) {
        $('#errorMessage').text(message).show();
        $('#resultsContainer').empty();
        $('#paginationContainer').empty();
    }

    function formatDate(dateString) {
        if (!dateString) return 'N/A';
        const date = new Date(dateString);
        return isNaN(date.getTime()) ? dateString : date.toLocaleString();
    }

    function displayShopResults(data, container) {
        data.forEach(function (product) {
            const card = $('<div>').addClass('product-card');

            // image link
            const imageLink = $('<a>')
                .attr('href', product.url)
                .attr('target', '_blank')
                .attr('rel', 'noopener');

            const image = $('<div>').addClass('product-image')
                .append(imageLink.append(
                    $('<img>')
                        .attr('src', product.imageUrl || 'https://via.placeholder.com/300')
                        .attr('alt', product.title)
                        .on('error', function () {
                            $(this).attr('src', 'https://via.placeholder.com/300');
                        })
                ));

            // info
            const info = $('<div>').addClass('product-info');

            // title
            const title = $('<h3>').append(
                $('<a>')
                    .attr('href', product.url)
                    .attr('target', '_blank')
                    .attr('rel', 'noopener')
                    .text(product.title)
            );

            // category
            const category = $('<div>').addClass('product-category')
                .text(product.source || 'eBay');

            // collect and append
            info.append(title).append(price).append(category).append(desc);
            card.append(image).append(info);
            container.append(card);
        });
    }
});