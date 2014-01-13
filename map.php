<?php
    if (!(isset($_GET['x']) && isset($_GET['y']))) {
        $x = 55.734046;
        $y = 37.588628;
    } else {
        $x = $_GET['x'];
        $y = $_GET['y'];
    }
?>
<!DOCTYPE html>
<html>
<head>
    <title>Карта</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <script src="//api-maps.yandex.ru/2.0.31/?load=package.standard,package.geoQuery&lang=ru-RU" type="text/javascript"></script>
    <style type="text/css">
    html, body, #map {
            width: 100%;
            height: 100%;
            margin: 0;
            padding: 0;
            font-family: "Arial";
            font-size: 11pt;
        }
    </style>

<script type="text/javascript">
function init() {
    var myMap = new ymaps.Map('map', {
            center: [<?php echo $x.", ".$y ?>],
            zoom: 15,
            behaviors: ['default', 'scrollZoom']
        });

    // Можно создать выборку из запроса к геокодеру.
    // В этом случае результаты запроса будут добавлены в выборку после того,
    // как сервер вернет ответ.
    var objects = ymaps.geoQuery(ymaps.geocode([<?php echo $x.", ".$y ?>]))
        .addToMap(myMap);

    // Обратите внимание, что все операции асинхронные, поэтому для продолжения
    // работы с выборкой следует дождаться готовности данных.
    objects.then(function () {
        // Этот код выполнится после того, как все запросы к геокодеру
        // вернут ответ и объекты будут добавлены на карту.
        objects.get(0).balloon.open();
    });
}

ymaps.ready(init);


</script>
</head>
<body>
    <div id="map"/>
</body>
</html>