<?php
header('Content-Type: application/json');
function getVisitorIp() {
    
    if (!empty($_SERVER['HTTP_CLIENT_IP']) && filter_var($_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP)) {
        return $_SERVER['HTTP_CLIENT_IP'];
    }

    
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']) && filter_var($_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP)) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    }

    
    if (!empty($_SERVER['REMOTE_ADDR']) && filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP)) {
        return $_SERVER['REMOTE_ADDR'];
    }

   
    return 'Unknown IP address';
}

function getVisitorCityFromIpInfo($ip) {
    $ipInfoJson = file_get_contents("http://ipinfo.io/{$ip}/json");
    $ipInfo = json_decode($ipInfoJson, true);
    return isset($ipInfo['city']) ? $ipInfo['city'] : 'unknown';
}


function getCurrentTemperature($UserCity, $apiKey) {
    $WeatherApiUrl = "https://api.openweathermap.org/data/2.5/weather?q={$UserCity}&appid={$WeatherApiKey}&units=metric";
    $weatherJson = file_get_contents($WeatherApiUrl);
    $weatherData = json_decode($weatherJson, true);
    return isset($weatherData['main']['temp']) ? $weatherData['main']['temp'] : '(Not found Temp)';
}


$WeatherApiKey = '3dfb9919a4259f4cc65c6db7a0a6b35b';


$clientIp = getVisitorIp();


$UserCity = getVisitorCityFromIpInfo($clientIp);


$temperature = getCurrentTemperature($UserCity, $WeatherApiKey);


$visitorName = isset($_GET['visitor_name']) ? $_GET['visitor_name'] : 'visitor';


$greeting = "Hello, {$visitorName}! The weather temperature is {$temperature} degrees Celsius in {$UserCity}.";





echo json_encode([
    'client_ip' => $clientIp,
    'location' => $UserCity,
    'greeting' => $greeting
]);

?>