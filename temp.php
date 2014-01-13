<html><head><meta charset="utf-8"></head><body>
<?php

    require_once('php/update_data.php');

    try {
        //update_all();
//        updateDataset(6, 'datasets\Playground.csv');
        recalculate_data();
        echo "ok";
    } catch (Exception $e) {
        echo "error: ".$e->getMessage();
    }

?></body></html>