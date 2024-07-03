<?php
header('Content-Type: application/json');

// Function to get client IP address from the client request
function get_ip() {
    // Array of known proxy server IPs
    $proxy_ips = ['127.0.0.1', '::1']; // Add more IPs if needed

    foreach ($proxy_ips as $ip) {
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] == $ip) {
            continue;
        }
    }

    // Check for forwarded IPs
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']) && !$ip) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED']) && !$ip) {
        $ip = $_SERVER['HTTP_X_FORWARDED'];
    } elseif (!empty($_SERVER['HTTP_FORWARDED_FOR']) && !$ip) {
        $ip = $_SERVER['HTTP_FORWARDED_FOR'];
    } elseif (!empty($_SERVER['HTTP_FORWARDED']) && !$ip) {
        $ip = $_SERVER['HTTP_FORWARDED'];
    } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
        $ip = $_SERVER['REMOTE_ADDR'];
    } else {
        $ip = 'UNKNOWN';
    }

    return $ip;
}

// Function to parse visitor name from query parameter
function get_visitor_name() {
    return isset($_GET['visitor_name']) ? htmlspecialchars($_GET['visitor_name']) : 'Visitor';
}

// Function to handle /api/hello endpoint
function handle_api_hello() {
    $clientIP = get_ip();
    $visitorName = get_visitor_name();

    // Use ipinfo.io API to get location information
    $ipinfoToken = 'd4c4083a42fd48';
    $ipinfoUrl = "https://ipinfo.io/{$clientIP}?token={$ipinfoToken}";
    $locationData = @json_decode(file_get_contents($ipinfoUrl), true);
    $city = isset($locationData['city']) ? $locationData['city'] : 'Unknown Location';

    // If the city is unknown, set a default city for weather information
    if ($city === 'Unknown Location') {
        $city = 'New York'; // Default city
    }

    // Fetch location and weather data using WeatherAPI based on city
    $weatherApiKey = '3dfb9919a4259f4cc65c6db7a0a6b35b';
    $weatherUrl = "http://api.weatherapi.com/v1/current.json?key={$weatherApiKey}&q={$city}&aqi=no";
    $data = @file_get_contents($weatherUrl);

    if ($data === FALSE) {
        $temperature = 'Unknown';
    } else {
        $weatherData = json_decode($data);

        if ($weatherData && isset($weatherData->current->temp_c)) {
            $temperature = $weatherData->current->temp_c;
        } else {
            $temperature = 'Unknown';
        }
    }

    // Create the response
    $response = [
        'client_ip' => $clientIP,
        'location' => $city,
        'greeting' => "Hello, $visitorName! The temperature is {$temperature} degrees Celsius in {$city}"
    ];

    echo json_encode($response);
}

// Main routing logic
$requestedUrl = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$routes = [
    '/api/hello' => 'handle_api_hello'
];

if (isset($routes[$requestedUrl])) {
    call_user_func($routes[$requestedUrl]);
} else {
    http_response_code(404);
    echo json_encode([
        'error' => 'Error 404: Route not found',
        'requested_url' => $requestedUrl,
        'routes' => array_keys($routes)
    ]);
}
?>
