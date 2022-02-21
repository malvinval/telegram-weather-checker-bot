<?php
    $path = "https://api.telegram.org/<your_bot_token>";
    $update = json_decode(file_get_contents("php://input"), TRUE);
    $chatId = $update["message"]["chat"]["id"];
    $message = $update["message"]["text"];
    if (strpos($message, "/start") === 0) {
        $guide_text = urlencode("Please type /weather location_name");
        file_get_contents($path."/sendmessage?chat_id=".$chatId."&text=".$guide_text);
        exit;
    } else if (strpos($message, "/weather") === 0) {
        $location = substr($message, 9);
    } else {
        $guide_text = urlencode("Please type /weather location_name");
        file_get_contents($path."/sendmessage?chat_id=".$chatId."&text=".$guide_text);
        exit;
    }

    $weather = json_decode(file_get_contents("http://api.openweathermap.org/data/2.5/weather?q=".$location."&appid=<your_API_key>&units=metric"), TRUE);

    if(!isset($weather["sys"]["country"])) {
        $warn_text = urlencode("Location not found !");
        file_get_contents($path."/sendmessage?chat_id=".$chatId."&text=".$warn_text);
        exit;
    } 

    $text = urlencode("Here are the weather conditions in  ". strtoupper($location) . "\r\n" . "\r\n" .
    "Region : " . $weather["sys"]["country"] . "\r\n" .
    "Current temperature  : " . $weather["main"]["temp"] . "°С" . "\r\n" .
    "Condition : " . strtoupper($weather["weather"][0]["description"]) . "\r\n" .
    "Minimum temperature  : " . $weather["main"]["temp_min"] . "°С" . "\r\n" .
    "Maximum temperature  : " . $weather["main"]["temp_max"] . "°С" . "\r\n" .
    "Wind speed : " . $weather["wind"]["speed"] . " m/s" . "\r\n" .
    "Cloud quantity : " . $weather["clouds"]["all"] . "%" . "\r\n" .
    "Geo Coordinates : [" . $weather["coord"]["lat"] . "," . $weather["coord"]["lon"] ."]");

    file_get_contents($path."/sendmessage?chat_id=".$chatId."&text=".$text );
?>
