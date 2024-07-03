<?php


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
    $ipInfoJson = file_get_contents("http://ipinfo.io/{$ip}/json");
    $ipInfo = json_decode($ipInfoJson, true);
    return isset($ipInfo['city']) ? $ipInfo['city'] : 'unknown';
}


function getCurrentTemperature($city, $apiKey) {
    $url = "https://api.openweathermap.org/data/2.5/weather?q={$city}&appid={$apiKey}&units=metric";
    $weatherJson = file_get_contents($url);
    $weatherData = json_decode($weatherJson, true);
    return isset($weatherData['main']['temp']) ? $weatherData['main']['temp'] : 'unavailable';
}


$apiKey = '3dfb9919a4259f4cc65c6db7a0a6b35b';


$clientIp = getClientIp();


$city = getCityFromIpInfo($clientIp);


$temperature = getCurrentTemperature($city, $apiKey);


$visitorName = isset($_GET['visitor_name']) ? $_GET['visitor_name'] : 'visitor';


$greeting = "Hello, {$visitorName}! The temperature is {$temperature} degrees Celsius in {$city}.";


header('Content-Type: application/json');


echo json_encode([
    'client_ip' => $clientIp,
    'location' => $city,
    'greeting' => $greeting
]);

?>