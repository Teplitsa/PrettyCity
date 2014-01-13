<?php
include_once('csv_declaration.php');

$nearest = array();

function setNearest($datesetId, $distance) {
    global $nearest;
    $nearest[$datesetId] = $distance;
}

function getNearest() {
    global $nearest;

    $res = array();
    foreach ($nearest as $datasetId => $distance) {
        $res[getDatasetName($datasetId)] = $distance;
    }
    return $res;
}