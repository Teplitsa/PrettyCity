<?php

    include_once('qrs.php');
    include_once('funcs.php');
    include_once('points_declarations.php');



//$p['latitude'] = 55.75803;
//$p['longitude'] = 37.572437;
//$x = getRanges($p, 0.5, 3);

//Создаёт сетку для Москвы
function create_points() {
    for ($i_x = 0; $i_x < count_lat; $i_x++) {
        for ($i_y = 0; $i_y < count_long; $i_y++) {
            $point = getPointByNum($i_x, $i_y);

            addPoint($i_x, $i_y, $point['longitude'], $point['latitude']);
        }
    }
}


function getPointByNum($num_x, $num_y) {
    $res = array();
    $res['latitude'] = start_latitude - $num_x * delta_lat;
    $res['longitude'] = $num_y * delta_long + start_longitude;

    return $res;
}

function getPrevPointNums($point) {
    $latitude = $point['latitude'];
    $longitude = $point['longitude'];

    if (! (is_numeric($latitude) && is_numeric($longitude)) ||
        ($latitude > start_latitude) || ($latitude < end_latitude) ||
        ($longitude < start_longitude) || ($longitude > end_longitude)) {
        return false;
    }

    $res = array();
    $res['num_x'] = floor((start_latitude - $latitude) / delta_lat);
    $res['num_y'] = floor(($longitude - start_longitude) / delta_long);

    return $res;
}

function getNearestPoint($point) {
    $num = getPrevPointNums($point);
    $min_distance = getDistanceInKm($point, getPointByNum($num['num_x'], $num['num_y']));
    $res = $num;

    $temp_point = getPointByNum($num['num_x'] + 1, $num['num_y']);
    $temp_distance = getDistanceInKm($point, $temp_point);
    if ($temp_distance < $min_distance) {
        $min_distance = $temp_distance;
        $res['num_x'] = $num['num_x'] + 1;
        $res['num_y'] = $num['num_y'];
    }

    $temp_point = getPointByNum($num['num_x'], $num['num_y'] + 1);
    $temp_distance = getDistanceInKm($point, $temp_point);
    if ($temp_distance < $min_distance) {
        $min_distance = $temp_distance;
        $res['num_x'] = $num['num_x'];
        $res['num_y'] = $num['num_y'] + 1;
    }

    $temp_point = getPointByNum($num['num_x'] + 1, $num['num_y'] + 1);
    $temp_distance = getDistanceInKm($point, $temp_point);
    if ($temp_distance < $min_distance) {
        $res['num_x'] = $num['num_x'] + 1;
        $res['num_y'] = $num['num_y'] + 1;
    }

    return $res;
}

function getNearestPoints($NumLatitude, $NumLongitude, $distance) {
    $delta = floor($distance / km_in_delta);

    $res = array();
    for ($latitude = $NumLatitude - $delta; $latitude <= $NumLatitude + $delta; $latitude++) {
        for ($longitude = $NumLongitude - $delta; $longitude <= $NumLongitude + $delta; $longitude++) {
            $point = array();
            $point[Points_NumLatitude] = $latitude;
            $point[Points_NumLongitude] = $longitude;

            $res[] = $point;
        }
    }

    return $res;
}

function getRanges($point, $radius_1, $radius_2) {
    $x = $point['longitude'];
    $y = $point['latitude'];

    if (! (is_numeric($x) && is_numeric($y) &&
        (is_numeric($radius_1) && is_numeric($radius_2)))) {
        return false;
    }

    $nums = getPrevPointNums($point);

    $radius_num_x = (floor($radius_2 * grad_in_km_lat / delta_lat) + 1);
    $radius_num_y = (floor($radius_2 * grad_in_km_long / delta_long) + 1);

    $range_1 = array();
    $range_2 = array();

    for ($i_x = -$radius_num_x; $i_x <= $radius_num_x; $i_x++) {
        for ($i_y = -$radius_num_y; $i_y <= $radius_num_y; $i_y++) {
            $temp_point = getPointByNum($nums['num_x'] + $i_x, $nums['num_y'] + $i_y);
            $distance = getDistanceInKm($point, $temp_point);
            if ($distance <= $radius_2) {
                $id = getPointId($nums['num_x'] + $i_x, $nums['num_y'] + $i_y);

                if ($distance <= $radius_1) {
                    $elem = array();
                    $elem['id'] = $id;
                    $elem['distance'] = $distance;
                    $range_1[] = $elem;
                } else {
                    $elem = array();
                    $elem['id'] = $id;
                    $elem['distance'] = $distance;
                    $range_2[] = $elem;
                }
            }
        }
    }

    $res = array();
    $res['range_1'] = $range_1;
    $res['range_2'] = $range_2;

    return $res;
}

function toPoint($latitude, $longitude) {
    $res = array();
    $res['latitude'] = $latitude;
    $res['longitude'] = $longitude;
    return $res;
}