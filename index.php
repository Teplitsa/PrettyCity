<html>

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <script type="text/javascript" src="js/jquery-2.0.3.js"></script>
    <!-- 1. Подключим библиотеку jQuery (без нее jQuery UI не будет работать) -->
    <!--<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>-->
    <!-- 2. Подключим jQuery UI -->
    <link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.1/themes/redmond/jquery-ui.css" rel="stylesheet" type="text/css"/>
    <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>

    <script src="js/bootstrap/dist/js/bootstrap.min.js"></script>
    <link href="js/bootstrap/dist/css/bootstrap.css" rel="stylesheet">
    <script type="text/javascript" src="js/funcs.js"></script>
    <script type="text/javascript" src="js/fabric.all.min.js"></script>

    <script src="http://api-maps.yandex.ru/2.0-stable/?load=package.full&lang=ru-RU" type="text/javascript"></script>
    <script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?libraries=visualization&sensor=false">
    </script>

    <title>Городские зоны комфорта</title>

    <style>
        body{
            font-size:25px;
            color:gray;
        }
        input{
            border-radius: 3px;
        }
        .container{
            margin-top:20px;
        }
        .presets{
            margin-top:10px;
            color:silver;
            border-radius:5px;
            border:3px solid #ddd;
            width: 100%;
        }
        .slider{
            font-size:15px;
        }
        #search_field{
            padding:20px;
        }

        #params li{
            margin-top: 15px;
        }
        #map{
            margin:10px;
            margin-left:20px;
            border:3px solid #ddd;
            border-radius:5px;
        }  
        #desc{
            margin-top:40px;
            font-size:20px;
        }
        #waiting_message{
            text-align: center;
            color: gray;
            border-radius: 5px;
        }
        .ui-widget-overlay{
            opacity: 0.6;
            background-image: none;
            background-color: #2f2a57;
        }
        .ui-dialog-titlebar {
            display: none;
        }
        #btnYet{
            margin-left:10px;
            margin-top: 30px;
            margin-bottom:5px;
            padding-left: 20px;
            padding-right: 20px;
        }
        #yet{
            display:none;
        }

    </style>
</head>

<body>
    <div class="container">

            <div id="search_field" style="clear:both; text-align:center;">
                <input id="address" type="text" value="Фрунзенская, 23"/> 
                <input id="btnCalculate" type="button" class="btn btn-primary btn-large" value="Смотреть" onclick="getRaitings()"><br>
            </div>

            <div id="sidebar" style="float:left; width: 300px;">

                <div id="params"> 
                    <select id="presets" onchange="loadPreset()" class="presets">
                        <option id = "default" value = "default">Кто вы?</option>
                        <option id = "student" value = "student">Студент</option>
                        <option id = "holost"  value = "holost" >Холостяк</option>
                        <option id = "molsem"  value = "molsem" >Молодая семья</option>
                        <option id = "semavg"  value = "semavg" >Cемья</option>
                        <option id = "pensio"  value = "pensio" >Пенсионер</option>
                    </select>
                    <ul class="nav nav-tabs nav-stacked">
                        <li>
                            Школы (<span id="scools_percent"></span> %)
                            <div id="scools_slider" class="slider"></div>
                        </li>
                        <li>
                            Метро (<span id="metro_percent"></span> %)
                            <div id="metro_slider" class="slider"></div>
                        </li>
                        <li>
                            Безопасность  (<span id="zog_percent"></span> %)
                            <div id="zog_slider" class="slider"></div>
                        </li>
                    </ul>

                    <!-- Еще -->
                    <input id="btnYet" type="button" class="btn btn-primary btn-large" value="Еще параметры..." onclick="yetToggle()"><br>
                    <ul class="nav nav-tabs nav-stacked" id="yet">
                        <li>
                            Аптеки (<span id="apteki_percent"></span> %)
                            <div id="apteki_slider" class="slider"></div>
                        </li>
                        <li>
                            Детские сады (<span id="detsad_percent"></span> %)
                            <div id="detsad_slider" class="slider"></div>
                        </li>
                        <li>
                            Парки (<span id="parki_percent"></span> %)
                            <div id="parki_slider" class="slider"></div>
                        </li>
                        <li>
                            Кинотеатры (<span id="kteatr_percent"></span> %)
                            <div id="kteatr_slider" class="slider"></div>
                        </li>
                        <li>
                            Спорт. площадки (<span id="sportpl_percent"></span> %)
                            <div id="sportpl_slider" class="slider"></div>
                        </li>
                        <li>
                            Розничные рынки (<span id="rynki_percent"></span> %)
                            <div id="rynki_slider" class="slider"></div>
                        </li>
                    </ul>
                </div>

                <div id="desc">
                    <div id = "Raiting"></div>
                    <div id = "socialRaiting"></div>
                    <div id = "infrastructureRaiting"></div>
                    <div id = "recreationRaiting"></div>
                    <div id = "x"></div>
                    <div id = "y"></div>
                    <div id = "nearest"></div>
                </div>

            </div>
            <div     id="map"        style="float:left; width:800px; height: 600px;"></div>
    </div>
    <div id="waiting_message">Пожалуйста подождите <br> идет загрузка карты...</div>
</body>


<script type="text/javascript">
    $('document').ready(function(){ymaps.ready(function(){getRaitings();})});

    sliderInit('scools');
    sliderInit('metro');
    sliderInit('zog');

    sliderInit('apteki');
    sliderInit('detsad');
    sliderInit('parki');
    sliderInit('kteatr');
    sliderInit('sportpl');
    sliderInit('rynki');

    function sliderInit(name){
        $("#" + name + "_slider").slider({
            slide  : function(){ set_sl_val($(this), name+"_percent"); },
            change : function(){ set_sl_val($(this), name+"_percent"); },
            create : function(){ set_sl_val($(this), name+"_percent"); },
            min : 0,
            max : 100,
            value: 50
        })
    }

    function set_sl_val(slider,input_id){
        $('#'+input_id).text(slider.slider('option','value'));
    }
    function yetToggle(){
        $('#yet').slideToggle();
    }
</script>

<!-- Yandex.Metrika counter --
<script type="text/javascript">
(function (d, w, c) {
    (w[c] = w[c] || []).push(function() {
        try {
            w.yaCounter22531141 = new Ya.Metrika({id:22531141,
                    clickmap:true,
                    trackLinks:true,
                    accurateTrackBounce:true});
        } catch(e) { }
    });

    var n = d.getElementsByTagName("script")[0],
        s = d.createElement("script"),
        f = function () { n.parentNode.insertBefore(s, n); };
    s.type = "text/javascript";
    s.async = true;
    s.src = (d.location.protocol == "https:" ? "https:" : "http:") + "//mc.yandex.ru/metrika/watch.js";

    if (w.opera == "[object Opera]") {
        d.addEventListener("DOMContentLoaded", f, false);
    } else { f(); }
})(document, window, "yandex_metrika_callbacks");
</script>
<noscript><div><img src="//mc.yandex.ru/watch/22531141" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
!-- /Yandex.Metrika counter -->

</html>