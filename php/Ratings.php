<?php
    include_once("DatasetsRatings.php");
    include_once("funcs.php");
    include_once("csv_declaration.php");

    function getDefaultCoeffs() {
        $coeffs = array();
        for ($i = 1; $i <= 7; $i++) {
            $coeffs[$i] = 0.5;
        }
        return $coeffs;
    }

    function calc_ratings($coeffs, $location) {
        $res = array();
        foreach ($coeffs as $datasetId => $coeff) {
            if ($coeff != 0) {
                $res[$datasetId] = getRatingByDatasetId($datasetId, $location);
            }
        }
        return $res;
    }

    function getLocalRating($ratings, $coeffs, $datasets) {
        $rating = 0;
        $divider = 0;
        $multiplier = 1;
        foreach ($datasets as $dataset) {
            if ($coeffs[$dataset] != 1) {
                $rating += $ratings[$dataset] * $coeffs[$dataset];
                $divider += $coeffs[$dataset];
            } else {
                $multiplier *= $ratings[$dataset] / 100;
            }
        }
        if ($divider == 0) $divider = 1;

        return $rating * $multiplier / $divider;
    }

    function getFullRating($ratings, $coeffs) {
        $rating = 0;
        $divider = 0;
        $multiplier = 1;
        foreach ($coeffs as $datasetId => $coeff) {
            if ($coeff != 1) {
                $rating += $ratings[$datasetId] * $coeff;
                $divider += $coeff;
            } else {
                $multiplier *= $ratings[$datasetId] / 100;
            }
        }
        if ($divider == 0) $divider = 1;

        return $rating * $multiplier / $divider;
    }

    function getCumulativeRating($location) {
        $coeffs = getDefaultCoeffs();
        $ratings = calc_ratings($coeffs, $location);

        $localRatings = array();
        $localRatings['socialRaiting'] = round(getLocalRating($ratings, $coeffs, array(Pharmacies_id, Kindergartens_id)));
        $localRatings['infrastructureRaiting'] = round(getLocalRating($ratings, $coeffs, array(Metro_id)));
        $localRatings['recreationRaiting'] = round(getLocalRating($ratings, $coeffs, array(Parks_id, Cinema_id, Sport_id)));

        $res = array();
        $res['localRaitings'] = $localRatings;
        $res['raiting'] = getFullRating($ratings, $coeffs);

        return $res;
    }