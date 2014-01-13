<?php

include_once("qrs.php");
include_once("points_declarations.php");

define("default_city", "Москва");

function getCoordsByAddress($address, $city = default_city) {

    $address = "Россия, ".$city.", ".trim($address);
    $coords = getCoordsFromDB($address);
    if ($coords != FALSE) {
        return $coords;
    }
    $template = "http://geocode-maps.yandex.ru/1.x/?format=json&geocode=";

    $answer = getRequest($template.trim($address));
    if (!$answer) {
        return FALSE;
    }

    $answer = json_decode($answer, true);
    $answer = $answer['response'];
    $answer = $answer['GeoObjectCollection'];
    $answer = $answer['featureMember'];
    $answer = $answer[0];
    $answer = $answer['GeoObject'];
    $answer = $answer['Point'];
    $answer = $answer['pos'];

    $res = array();
    $res['latitude'] = substr($answer, 0, strpos($answer, " "));
    $res['longitude'] = substr($answer, strpos($answer, " ") + 1);

    addCoordsToDB($address, $res['latitude'], $res['longitude']);
    return $res;
}

function getRequest($request) {
    return file_get_contents($request, 'r');
}

function getDistanceInKm($firstCoords, $secondCoords) {
    return sqrt(pow(($firstCoords['latitude'] - $secondCoords['latitude']) / grad_in_km_lat, 2) +
    pow(($firstCoords['longitude'] - $secondCoords['longitude']) / grad_in_km_long, 2));
}


$today_date = date('Y-m-d');
function get_today() {
    global $today_date;

    return $today_date;
}