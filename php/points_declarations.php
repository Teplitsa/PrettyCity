<?php
    define("grad_in_km_lat", 0.009016);
    define("grad_in_km_long", 0.015986);

    define("km_in_delta", 2);

    define("delta_lat", grad_in_km_lat / km_in_delta);
    define("delta_long", grad_in_km_long / km_in_delta);

    define("start_latitude", 55.914238);
    define("start_longitude", 37.367587);

    define("end_latitude", 55.570116);
    define("end_longitude", 37.848494);

    define("count_lat", floor( (start_latitude - end_latitude) / delta_lat) + 1);
    define("count_long", floor( -(start_longitude - end_longitude) / delta_long) + 1);