<?php

header('Content-Type: application/json');

function getClientIp() {
    
    if (!empty($_SERVER['HTTP_CLIENT_IP']) && filter_var($_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP)) {
        return $_SERVER['HTTP_CLIENT_IP'];
    }

    
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']) && filter_var($_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP)) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    }

    
    if (!empty($_SERVER['REMOTE_ADDR']) && filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP)) {
        return $_SERVER['REMOTE_ADDR'];
    }

   
    return 'unknown';
}

function getCityFromIpInfo($ip) {
    $ipInfoJson = file_get_contents("http://ipinfo.io/{$ip}/json?token=d4c4083a42fd48");
    $ipInfo = json_decode($ipInfoJson, true);
    return isset($ipInfo['city']) ? $ipInfo['city'] : 'unknown';
}

$city = getCityFromIpInfo($clientIp);
function getCurrentTemperature($city, $apiKey) {
    $url = "https://api.openweathermap.org/data/2.5/weather?q={$city}&appid={$apiKey}&units=metric";
    $weatherJson = file_get_contents($url);
    $weatherData = json_decode($weatherJson, true);
    return isset($weatherData['main']['temp']) ? $weatherData['main']['temp'] : 'unavailable';
}


$apiKey = '4db2a2adbdd4dc4ceff76ab4135f9bd2';


$clientIp = getClientIp();





$temperature = getCurrentTemperature($city, $apiKey);


$visitorName = isset($_GET['visitor_name']) ? $_GET['visitor_name'] : 'visitor';


$greeting = "Hello, {$visitorName}! The temperature is {$temperature} degrees Celsius in {$city}.";





echo json_encode([
    'client_ip' => $clientIp,
    'location' => $city,
    'greeting' => $greeting
]);

?>