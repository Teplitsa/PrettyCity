<?php

include_once("php/main_funcs.php");

// available functions names
define("getRatingByAddress", "getRaitingByAddress");
define("getRatingImage", "getRaitingImage");
define("getPoints", "getPoints");
define("getPointArrays", "getPointArrays");
define("FilesInformation", "FilesInformation");

$res = array();
$func = "";

try {
    if (!isset($_POST["func"])) {
        if (isset($_GET['address'])) {
            $func = getRatingImage;
        } else {
            $func = getRatingByAddress;
        }
    } else {
        $func = $_POST["func"];
    }

    switch ($func) {
        case getRatingByAddress: $res = func_getRatingByAddress(); break;
        case getRatingImage: func_generateImg(); break;
        case getPoints: $res = func_getPoints(); break;
        case getPointArrays: $res = func_getPointArrays(); break;

        case FilesInformation: $res = func_getFilesInformation(); break;

        default: throw new Exception("Неизвестная функция");
    }

} catch (Exception $e) {
    $res['errorMessage'] = $e->getMessage();
}

if (isset($res['errorMessage'])) {
    $res['result'] = 'fail';
} else {
    $res['result'] = 'success';
}

if (($res['result'] == 'fail') || ($func != getRatingImage)) {
    echo json_encode($res);
}