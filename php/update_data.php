<?php

require_once("qrs.php");
require_once('DatasetsRatings.php');

define('files_dir', dirname(__FILE__).'/../datasets/');

function update_all() {
    // 1. загружаем файлы из интернета
    $files = getFilesAddressesFromDB();
    if ($files == false) {
        throw new Exception('Не удалось обновить список файлов с датасетами');
    }

    foreach ($files as $file) {
        $file_address = files_dir.$file['Filename'];

        if (downloadFile($file['Url'], $file_address)) {
            // 2. каждый из них открываем, парсим, заливаем в таблицу Data:
            updateDataset($file['ID'], $file_address);
        } else {
            error_log("Не удалось обновить файл ".$file['Url'], 0);
        }
    }
}



function recalculate_data() {
    $new_rows = getNewDataRowsFromDB();

    foreach ($new_rows as $row) {
        recalculateNewRow(
            $row[Data_ID],
            $row[Data_DatabaseID],
            $row[Data_String]);
    }

    $rows_to_remove = getRowsToRemoveFromDB();

    foreach ($rows_to_remove as $row) {
        recalculateRowsToRemove(
            $row[Data_ID],
            $row[Data_DatabaseID]);
    }
}

    // Обработка
    // 1. Просчитываем Result всех точек, у которых взведен флаг "новый" и меняем их флаг на "текущий"
    // 2. Для каждой точки, у которой Lastupdate меньше, чем у соответствующего database_id:
    //   2.1 Если есть Result, использующий эту точку, то просчитываем все точки, которые лежат вокруг соответствующего Result
    //   2.2 Удаляем данную строку


function updateDataset($datasetId, $file_address) {
    //   2.1 если элемента в этой точке ранее не было, то взводим флаг "новый"
    //   2.2 если элемент в данной точке был, то обновляем существующую запись без изменения флага

    $data = parseCSV($file_address, getCsvColumns($datasetId), ';');
    $current_data = getDatasetRowsByDatasetId($datasetId);

    $proc_date = get_today();
    foreach ($data as $row) {
        $rowText = implode(";", $row);

        $datasetRowId = getRowInDatasetArray($rowText, $current_data);
        if ($datasetRowId == false) {
            addDatasetRow($datasetId, $rowText, $proc_date);
        } else {
            updDatasetRow($datasetRowId, $rowText, $proc_date);
        }
    }
}

function getRowInDatasetArray($row, $datasetArray) {
    foreach ($datasetArray as $datasetRow) {
        if ($row == $datasetRow[Data_String]) {
            return $datasetRow[Data_ID];
        }
    }
    return FALSE;
}

function downloadFile($source, $destination) {
    $content = file_get_contents($source);
    if ($content == FALSE) {
        return FALSE;
    }

    $f = fopen( "$destination", "w" );
    if ($f == FALSE) {
        return FALSE;
    }

    if (fwrite( $f, $content ) == FALSE) {
        return FALSE;
    }

    if (fclose( $f ) == FALSE) {
        return FALSE;
    }

    return TRUE;
}

function recalculateNewRow($id, $datasetId, $text) {
    include_once('funcs.php');

    $parsed_text = explode(";", $text);
    $address_row = getAddressRowByDatasetId($datasetId);
    $address = $parsed_text[$address_row];
    $location = getCoordsByAddress($address);
    $radius = getDatasetRadius($datasetId);

    $ranges = getRanges($location, $radius[Files_MinRange], $radius[Files_MaxRange]);
    foreach ($ranges['range_1'] as $point) {
        addDataResult($datasetId, $point['id'], $point['distance'], $id);
    }
    foreach ($ranges['range_2'] as $point) {
        addDataResult($datasetId, $point['id'], $point['distance'], $id);
    }

    updDatasetRow($id, $text, get_today(), false, $location['latitude'], $location['longitude']);
}

function recalculateRowsToRemove($id, $datasetId) {
    $row_points = getPointsUsingRow($id);

    foreach ($row_points as $row_point) {
        recalculate_row($datasetId, $row_point);
    }

    removeDataRow($id);
}

function recalculate_row($dataset_id, $point) {
    $radius = getDatasetRadius($dataset_id);
    $nearest_points = getNearestPoints($point[Points_NumLatitude], $point[Points_NumLongitude], $radius[Files_MaxRange]);

    foreach ($nearest_points as $current_point) {
        setPointRating($current_point, $dataset_id, $radius[Files_MinRange], $point[Results_ID]);
    }
}