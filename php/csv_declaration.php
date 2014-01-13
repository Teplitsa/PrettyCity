<?php

// id массивов данных
define("Pharmacies_id", 1);
define("Kindergartens_id", 2);
define("Parks_id", 3);
define("Cinema_id", 4);
define("Metro_id", 5);
define("Sport_id", 6);
define("Market_id", 7);

function getDatasetName($datasetId) {

    switch ($datasetId) {
        case Pharmacies_id:
            return "Аптека";
        case Kindergartens_id:
            return "Детский сад";
        case Parks_id:
            return "Парк";
        case Cinema_id:
            return "Кинотеатр";
        case Metro_id:
            return "Метро";
        case Sport_id:
            return "Спортивная площадка";
        case Market_id:
            return "Рынок";
        default: return "Unnamed";
    }
}


function getCsvColumns($datasetId) {
    switch ($datasetId) {
        case Pharmacies_id:
            return array(
                'id',
                'name',
                'address',
                'phone',
                'work_time',
                'company',
                'type',
                'weekend_type',
                'comment');
        case Kindergartens_id:
            return array(
                'id',
                'name',
                'label',
                'address',
                'x',
                'y',
                'bti',
                'cad_no',
                'street_bti',
                'house_bti',
                'hadd_bti',
                'org_form',
                'type',
                'class',
                'phone',
                'site',
                'owner');
        case Parks_id:
            return array(
                'id',
                'name',
                'label',
                'address',
                'x',
                'y',
                'bti',
                'cad_no',
                'street_bti',
                'house_bti',
                'hadd_bti',
                'adm_okrug',
                'area',
                'urid_addr',
                'phone',
                'fax',
                'site',
                'email');
        case Cinema_id:
            return array(
                'id',
                'name',
                'label',
                'address',
                'x',
                'y',
                'bti',
                'cad_no',
                'street_bti',
                'house_bti',
                'hadd_bti',
                'adm_okrug',
                'area',
                'urid_addr',
                'phone',
                'fax',
                'site',
                'email');
        case Metro_id:
            return array(
                'id',
                'name',
                'label',
                'address',
                'x',
                'y',
                'bui_bti',
                'cad_no',
                'street_bti',
                'house_bti',
                'hadd_bti',
                'line',
                'status',
                'vestibul',
                'time1',
                'time2',
                'BPA_count',
                'remont_date',
                'escalator_type',
                'escalator_lenght',
                'escalator_count',
                'moddate',
                'moduser',
                'BPA_type');
        case Sport_id:
            return array(
                'ROWNUM',
                'address',
                'Stoimost',
                'Prokat',
                'Vremya_raboty',
                'Pokrytie',
                'Osveschenie',
                'Besplatnye_zanyatiya',
                'Stoimost_prokata',
                'Vedomstvo',
                'Okrug',
                'Rajon',
                'TehObsluzhivanie',
                'Razdevalka',
                'Zvukovoe_soprovozhdenie',
                'Tochka_pitaniya',
                'Tualet',
                'Tochka_dostupaWiFi',
                'Bankomat',
                'MedPunkt',
                'OGRN_ExpOrg',
                'Name_ExpOrg',
                'Telephone_ExpOrg',
                'Site_ExpOrg',
                'EMail_ExpOrg');
        case Market_id:
            return array(
                'id',
                'name',
                'sobstven',
                'company',
                'company_address',
                'address',
                'type');
        default: return false;
    }
}

$address_row = array();
function getAddressRowByDatasetId($datasetId) {
    global $address_row;

    if (isset($address_row[$datasetId])) {
        return $address_row[$datasetId];
    }

    $cols = getCsvColumns($datasetId);
    foreach ($cols as $ind => $col) {
        if ($col == "address") {
            $address_row[$datasetId] = $ind;
            return $ind;
        }
    }

    return false;
}

$dataset_radius = array();
function getDatasetRadius($datasetId) {
    global $dataset_radius;

    if (isset($dataset_radius[$datasetId])) {
        return $dataset_radius[$datasetId];
    }

    $radius = getDatasetRadiusFromDB($datasetId);
    $dataset_radius[$datasetId] = $radius;
    return $radius;
}