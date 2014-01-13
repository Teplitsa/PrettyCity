<?php
    include_once('lib_DB_init.php');


    define("TABLE_Coords", "Coords");

    define("Coords_ID", "ID");
    define("Coords_address", "Address");
    define("Coords_latitude", "Latitude");
    define("Coords_longitude", "Longitude");

function getCoordsFromDB($address) {
    $q = "SELECT ".Coords_latitude.", ".Coords_longitude." FROM ".TABLE_Coords." WHERE ".Coords_address."='".$address."'";

    $res = grfdb($q);
    if (!$res) {
        return FALSE;
    }

    $coords = array();
    $coords['latitude'] = $res[Coords_latitude];
    $coords['longitude'] = $res[Coords_longitude];
    return $coords;
}

function addCoordsToDB($address, $latitude, $longitude) {
    if (! (is_numeric($latitude) && is_numeric($longitude)) ) {
        return false;
    }

    $props[Coords_address] = $address;
    $props[Coords_latitude] = $latitude;
    $props[Coords_longitude] = $longitude;

    return itdb(TABLE_Coords, $props);
}



define("TABLE_Points", "Points");

define("Points_ID", "ID");
define("Points_NumLatitude", "Num_Latitude");
define("Points_NumLongitude", "Num_Longitude");
define("Points_Latitude", "Latitude");
define("Points_Longitude", "Longitude");

function addPoint($num_x, $num_y, $x, $y) {
    if (! (is_numeric($x) && is_numeric($y) && is_numeric($num_x) && is_numeric($num_y)) ) {
        return false;
    }

    $props[Points_NumLatitude] = $num_x;
    $props[Points_NumLongitude] = $num_y;
    $props[Points_Latitude] = $x;
    $props[Points_Longitude] = $y;

    return itdb(TABLE_Points, $props);
}

function getPointId($num_x, $num_y) {
    if (! (is_numeric($num_x) && is_numeric($num_y)) ) {
        return false;
    }

    $q = "SELECT ".Points_ID." FROM ".TABLE_Points." WHERE ".Points_NumLatitude."=".$num_x." AND ".Points_NumLongitude."=".$num_y.";";
    return gefdb($q);
}



define("TABLE_Results", "Results");

define("Results_ID", "ID");
define("Results_PointId", "ID_Point");
define("Results_DatasetId", "ID_Dataset");
define("Results_DataId", "ID_Data");
define("Results_Result", "Result");

function addDataResult($datasetId, $pointId, $result, $dataId) {
    if (! (is_numeric($pointId) && is_numeric($datasetId) && is_numeric($result))) {
        return false;
    }

    $temp_result = getDataIdAndResult($datasetId, $pointId);
    if ( (!$temp_result) || ($temp_result == "") ) {
        $props[Results_DatasetId] = $datasetId;
        $props[Results_PointId] = $pointId;
        $props[Results_Result] = $result;
        $props[Results_DataId] = $dataId;

        return itdb(TABLE_Results, $props);
    } elseif ($temp_result[Results_Result] > $result) {
        return updateDataResult($temp_result[Results_ID],  $temp_result, $dataId);
    }
    return 1;
}

function getDataResult($datasetId, $pointId) {
    if (! (is_numeric($pointId) && is_numeric($datasetId))) {
        return false;
    }

    $q = "SELECT ".Results_Result." FROM ".TABLE_Results." WHERE ".
        Results_DatasetId."=".$datasetId." AND ".Results_PointId."=".$pointId.";";

    return gefdb($q);
}

function getDataIdAndResult($datasetId, $pointId) {
    if (! (is_numeric($pointId) && is_numeric($datasetId))) {
        return false;
    }

    $q = "SELECT ".Results_ID.", ".Results_Result." FROM ".TABLE_Results." WHERE ".
        Results_DatasetId."=".$datasetId." AND ".Results_PointId."=".$pointId.";";

    return grfdb($q);
}

function updateDataResult($id, $result, $data_id) {
    if (! (is_numeric($id) && is_numeric($result))) {
        return false;
    }

    $q = "UPDATE ".TABLE_Results.
        " SET ".Results_Result."=".$result.", ".Results_DataId."=".$data_id.
        " WHERE ".Results_ID."=".$id.";";
    return gefdb($q);
}

function getPointsUsingRow($id) {
    $q = "SELECT ".
        TABLE_Results.".".Results_ID." AS ".Results_ID.", ".
        TABLE_Results.".".Results_PointId." AS ".Results_PointId.", ".
        TABLE_Points.".".Points_Latitude." AS ".Points_Latitude.", ".
        TABLE_Points.".".Points_Longitude." AS ".Points_Longitude.", ".
        TABLE_Points.".".Points_NumLatitude." AS ".Points_NumLatitude.", ".
        TABLE_Points.".".Points_NumLongitude." AS ".Points_NumLongitude.
        " FROM  `".TABLE_Results."`, `".TABLE_Points."`".
        " WHERE ".TABLE_Results.".".Results_DataId."=".$id." AND ".
        Results_PointId."=".TABLE_Points.".".Points_ID;

    return gafdb($q);
}


function getPointsForMap() {
    $q = "SELECT ".Points_Latitude.", ".Points_Longitude.", SUM(".Results_Result.")".
        " FROM ".TABLE_Results.", ".TABLE_Points.
        " WHERE ".TABLE_Points.".".Points_ID."=".TABLE_Results.".".Results_PointId.
        " GROUP BY ".TABLE_Points.".".Points_ID;

    return gafdb($q);
}





define("TABLE_Files", "Files");

define("Files_ID", "ID");
define("Files_Name", "Name");
define("Files_Url", "Url");
define("Files_Filename", "Filename");
define("Files_LastUpdate", "Last_update");
define("Files_MinRange", "Min_range");
define("Files_MaxRange", "Max_range");

function getFilesInformationFromDB() {
    $q = "SELECT * FROM ".TABLE_Files;

    return gafdb($q);
}

function addFileInformationToDB($id, $name, $url, $filename, $lastUpdate, $MinRange, $MaxRange) {
    $props[Files_ID] = $id;
    $props[Files_Name] = $name;
    $props[Files_Url] = $url;
    $props[Files_Filename] = $filename;
    $props[Files_LastUpdate] = $lastUpdate;
    $props[Files_MinRange] = $MinRange;
    $props[Files_MaxRange] = $MaxRange;

    return itdb(TABLE_Files, $props);
}

function delFileInformationFromDB($id) {
    $q = "DELETE FROM ".TABLE_Files." WHERE ".Files_ID."=".$id;

    return gefdb($q);
}

function updFileInformationFromDB($curr_id, $id, $name, $url, $filename, $lastUpdate, $MinRange, $MaxRange) {
    $params = array();

    $params[Files_ID] = $id;
    $params[Files_Name] = $name;
    $params[Files_Url] = $url;
    $params[Files_Filename] = $filename;
    $params[Files_LastUpdate] = $lastUpdate;
    $props[Files_MinRange] = $MinRange;
    $props[Files_MaxRange] = $MaxRange;

    $q = "UPDATE ".TABLE_Files." SET ".get_update_string($params)
        ." WHERE ".Files_ID."=".$curr_id;

    return gefdb($q);
}

function getFilesAddressesFromDB() {
    $q = "SELECT ".Files_ID.", ".Files_Url.", ".Files_Filename." FROM ".TABLE_Files;

    return gafdb($q);
}

function getDatasetRadiusFromDB($datasetId) {
    if (!is_numeric($datasetId)) {
        return false;
    }

    $q = "SELECT ".Files_MaxRange.", ".Files_MinRange." FROM ".TABLE_Files." WHERE ".Files_ID."=".$datasetId;

    return grfdb($q);
}




define("TABLE_Data", "Data");

define("Data_ID", "ID");
define("Data_DatabaseID", "DatabaseID");
define("Data_Latitude", "Latitude");
define("Data_Longitude", "Longitude");
define("Data_String", "String");
define("Data_Last_update", "Last_update");
define("Data_isNew", "isNew");


function addNewDataRowToDB($DatabaseID, $latitude, $longitude, $string) {
    if (! (is_numeric($DatabaseID) && is_numeric($latitude) && is_numeric($longitude) && ($string != "")) ) {
        return false;
    }

    $props[Data_DatabaseID] = $DatabaseID;
    $props[Data_Latitude] = $latitude;
    $props[Data_Longitude] = $longitude;
    $props[Data_String] = $string;

    return itdb(TABLE_Data, $props);
}

function isDatasetRowExist($datasetId, $datasetRow) {
    $q = "SELECT ".Data_ID." FROM ".TABLE_Data." WHERE ".Data_DatabaseID."=".$datasetId." AND ".Data_String."=".$datasetRow;

    $res = gefdb($q);
    return (is_numeric($res) && ($res > 0));
}

function getDatasetRowsByDatasetId($datasetId) {
    $q = "SELECT ".Data_ID.", ".Data_String." FROM ".TABLE_Data." WHERE ".Data_DatabaseID."=".$datasetId;

    return gafdb($q);
}

function updDatasetRow($datasetRowId, $rowText, $last_update, $isNew = "undefined", $latitude = 0, $longitude = 0) {
    $params = array();

    $params[Data_String] = esc($rowText);
    $params[Data_Last_update] = esc($last_update);
    if (is_bool($isNew)) $params[Data_isNew] = intval($isNew);
    if ($latitude != 0) $params[Data_Latitude] = $latitude;
    if ($longitude != 0) $params[Data_Longitude] = $longitude;

    $q = "UPDATE ".TABLE_Data." SET ".get_update_string($params)
        ." WHERE ".Data_ID."=".$datasetRowId.";";

    return gefdb($q);
}

function addDatasetRow($databaseId, $rowText, $last_update) {
    $props = array();

    $props[Data_DatabaseID] = $databaseId;
    $props[Data_String] = $rowText;
    $props[Data_Last_update] = $last_update;
    $props[Data_isNew] = true;

    return itdb(TABLE_Data, $props);
}

function getNewDataRowsFromDB() {
    $q = "SELECT ".Data_ID.", ".Data_DatabaseID.", ".Data_String." FROM ".TABLE_Data." WHERE ".Data_isNew."=1";

    return gafdb($q);
}

function getRowsToRemoveFromDB(){
    $q = "SELECT ".
        TABLE_Data.".".Data_ID." AS ".Data_ID.", ".TABLE_Data.".".Data_DatabaseID.
        " FROM ".TABLE_Data.", ".TABLE_Files.
        " WHERE ".TABLE_Data.".".Data_DatabaseID."=".TABLE_Files.".".Files_ID.
        " AND ".TABLE_Data.".".Data_Last_update."<".TABLE_Files.".".Files_LastUpdate;

    return gafdb($q);
}

function removeDataRow($id) {
    $q = "DELETE FROM ".TABLE_Data." WHERE ".Data_ID."=".$id;

    return gefdb($q);
}

function getNearestDataRows($latitude, $longitude, $latitude_radius, $longitude_radius) {
    $q = "SELECT ".TABLE_Data.".*".
        " FROM .".TABLE_Data.", ".TABLE_Files.
        " WHERE ".TABLE_Data.".".Data_Last_update.">=".TABLE_Files.".".Files_LastUpdate.
            " AND ".TABLE_Data.".".Data_DatabaseID."=".TABLE_Files.".".Files_ID.
            " AND ".TABLE_Data.".".Data_Latitude.">".($latitude - $latitude_radius).
            " AND ".TABLE_Data.".".Data_Latitude."<".($latitude + $latitude_radius).
            " AND ".TABLE_Data.".".Data_Longitude.">".($longitude - $longitude_radius).
            " AND ".TABLE_Data.".".Data_Longitude."<".($longitude + $longitude_radius);

    return gafdb($q);
}