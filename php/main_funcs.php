<?php

include_once('funcs.php');
include_once('Ratings.php');
include_once('qrs.php');
include_once('Nearest.php');
include_once('get_rating_img.php');

function func_getRatingByAddress($address = null) {
    if (($address == null) && !isset($_POST["address"])) {
        throw new Exception("Incorrect address");
    }

    if ($address == null) {
        $address = $_POST["address"];
    }
    $coords = getCoordsByAddress($address);
    if (!$coords) {
        throw new Exception("Can't calculate coordinates for this address");
    }

    $res = getCumulativeRating($coords);
    $res['coords'] = $coords;
    $res['nearest'] = getNearest();
    $res['map'] = "http://".$_SERVER['SERVER_NAME']."/map.php?x=".$coords['longitude']."&y=".$coords['latitude'];

    return $res;
}

function func_generateImg() {
    if (!isset($_GET['address'])) {
        throw new Exception("Incorrect address");
    }else{
        $address = $_GET['address'];
    }
    if ( isset($_GET['size']) )
        $size = $_GET['size'];
    else
        $size = 100;
    $image = get_rating_image($address, $size);
    
    header("Content-type: image/png");
    imagepng( $image );

    /*
    $image = imagecreatetruecolor(140, 18);
    $fon = imagecolorallocate($image, 255, 255, 255);
    imagefill($image, 0, 0, $fon);
    $text_color = imagecolorallocate($image, 0, 0, 0);
    $rating = func_getRatingByAddress($_GET['address']);
    imagestring($image, 4, 0, 0, round($rating['raiting']), $text_color);

    header('Content-type: image/png');
    imagepng($image);
    */

}

function func_getPoints() {
    $res = array();
    $res['points'] = getPointsForMap();
    return $res;
}

function func_getPointArrays() {
    $res = array();
    define("FuncName", "PointArrays");
    $pointArrays = array();

    $points = getPointsForMap();
    $pointArrays['count'] = count($points);
    if ($pointArrays['count'] == 0) {
        $res[FuncName] = $pointArrays;
        return $res;
    }

    foreach ($points as $point) {
        foreach ($point as $k => $v) {
            $res[$k][] = $v;
        }
    }

    $res[FuncName] = $pointArrays;
    return $res;
}

function func_getFilesInformation() {
    $res = array();

    define('subfunc_getInfo', 'getFilesInformation');
    define('subfunc_newInfo', 'newFilesInformation');
    define('subfunc_updInfo', 'updFilesInformation');
    define('subfunc_delInfo', 'delFilesInformation');

    switch ($_POST['subfunc']) {
        case subfunc_getInfo: {
                $files = getFilesInformationFromDB();
                $res['files'] = $files;
                break;
            }
        case subfunc_newInfo: {
                $id = $_POST["id"];
                $Name = $_POST["Name"];
                $Url = $_POST["Url"];
                $Filename = $_POST["Filename"];
                $Last_update = $_POST["Last_update"];
                $MinRange = $_POST["MinRange"];
                $MaxRange = $_POST["MaxRange"];

                addFileInformationToDB($id, $Name, $Url, $Filename, $Last_update, $MinRange, $MaxRange);
                break;
            }
        case subfunc_updInfo: {
                $curr_id = $_POST["curr_id"];
                $id = $_POST["id"];
                $Name = $_POST["Name"];
                $Url = $_POST["Url"];
                $Filename = $_POST["Filename"];
                $Last_update = $_POST["Last_update"];
                $MinRange = $_POST["MinRange"];
                $MaxRange = $_POST["MaxRange"];

                updFileInformationFromDB($curr_id, $id, $Name, $Url, $Filename, $Last_update, $MinRange, $MaxRange);
                break;
        }
        case subfunc_delInfo: {
                $id = $_POST["id"];

                delFileInformationFromDB($id);
                break;
            }

        default: {
            throw new Exception("Неизвестная подфункция");
            }
    }

    return $res;
}