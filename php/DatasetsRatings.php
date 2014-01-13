<?php

include_once("funcs.php");
include_once("points.php");
include_once("csv_declaration.php");
include_once("Nearest.php");

define('km', 0.01605);

$current_location = null;

function getRatingByDatasetId($datasetId, $location) {
    switch ($datasetId) {
        case Pharmacies_id:
            return getPharmaciesRating($location);
        case Kindergartens_id:
            return getKindergartensRating($location);
        case Parks_id:
            return getParksRating($location);
        case Cinema_id:
            return getCinemaRating($location);
        case Metro_id:
            return getMetroRating($location);
        case Sport_id:
            return getSportRating($location);
        case Market_id:
            return getMarketRating($location);
        default: return false;
    }
}

//Аптеки
    function getPharmaciesRating($location) {
        return getRating($location, 'datasets/pharmacy.csv', Pharmacies_id, 1, 5);
    }

//Детские сады
    function getKindergartensRating($location) {
        return getRating($location, 'datasets/kindergartens.csv', Kindergartens_id, 0.5, 4);
    }

//Парки (включая парки не подведомственные)
    function getParksRating($location) {
        return getRating($location, 'datasets/parks.csv', Parks_id, 2, 10);
    }

//Кинотеатры
    function getCinemaRating($location) {
        return getRating($location, 'datasets/cinema.csv', Cinema_id, 1, 7);
    }

//Станции метрополитена
    function getMetroRating($location) {
        return getRating($location, 'datasets/metro.csv', Metro_id, 0.5, 4);
    }

//Площадки спортивные универсальные
    function getSportRating($location) {
        return getRating($location, 'datasets/sport.csv', Sport_id, 0.5, 4);
    }

//Розничные рынки
    function getMarketRating($location) {
        return getRating($location, 'datasets/market.csv', Market_id, 1, 8);
    }





function parseCSV($filename, $columns, $delimiter) {
    $handle = fopen($filename, "r");
    if ($handle == FALSE) {
        throw new Exception('Ошибка в процессе открытия файла');
    }
    if (($data = fgetcsv($handle, 0, $delimiter)) == FALSE) {
        throw new Exception('Файл не содержит ни одной строки');
    }

    $res = array();

    while (($data = fgetcsv($handle, 0, $delimiter)) !== FALSE) {
        $row = array();
        for ($col = 0; $col < count($columns); $col++) {
            $row[$columns[$col]] = $data[$col];
        }

        $res[] = $row;
    }
    fclose($handle);
    return $res;
}






function getRating($location, $csvName, $datasetId, $minDistance, $maxDistance) {
    $distance = getRatingFromDB($location, $datasetId);
    if (empty($distance)) {
        $data = parseCSV($csvName, getCsvColumns(Metro_id), ";");

        $distance = 1000;
        foreach ($data as $v) {
            $coord = getCoordsByAddress($v['address']);
            $current_distance = getDistanceInKm($location, $coord);
            if ($current_distance <= $minDistance) {
                setNearest($datasetId, $current_distance);
                return 100;
            }
            if ($current_distance < $distance) {
                $distance = $current_distance;
            }
        }
    }
    if ($distance <= $minDistance) {
        setNearest($datasetId, $distance);
        return 100;
    } elseif ($distance > $maxDistance) {
        return 0;
    }
    setNearest($datasetId, $distance);
    return (($maxDistance - $distance) / ($maxDistance - $minDistance)) * 100;
}


function getRatingFromDB($location, $datasetId) {
    $point = getNearestPoint($location);
    $point_id = getPointId($point['num_x'], $point['num_y']);

    $res = getDataResult($datasetId, $point_id);
    return $res;
}


//function addDataToDB($csvName, $datasetId, $minDistance, $maxDistance) {
//    $data = parseCSV($csvName, getCsvColumns($datasetId), ";");
//
//    foreach ($data as $v) {
//        $coord = getCoordsByAddress($v['address']);
//
//        $ranges = getRanges($coord, $minDistance, $maxDistance);
//        foreach ($ranges['range_1'] as  $point) {
//            addDataResult($datasetId, $point['id'], $point['distance']);
//        }
//        foreach ($ranges['range_2'] as $point) {
//            addDataResult($datasetId, $point['id'], $point['distance']);
//        }
//    }
//}

function setPointRating($pointNums, $dataset_id, $minDistance, $result_id) {
    $point = getPointByNum($pointNums[Points_NumLatitude], $pointNums[Points_NumLongitude]);

    $radius = getDatasetRadius($dataset_id);
    $data = getNearestDataRows($point['latitude'], $point['longitude'],
        $radius[Files_MaxRange] / grad_in_km_lat, $radius[Files_MaxRange] / grad_in_km_long);

    $distance = 1000;
    $best_data_row = array();
    foreach ($data as $current_data_row) {
        $current_point = toPoint($current_data_row[Data_Latitude], $current_data_row[Data_Longitude]);

        $current_distance = getDistanceInKm($point, $current_point);
        if ($current_distance <= $minDistance) {
            break;
        }
        if ($current_distance < $distance) {
            $distance = $current_distance;
            $best_data_row = $current_data_row;
        }
    }

    updateDataResult($result_id, $distance, $best_data_row[Data_ID]);
}