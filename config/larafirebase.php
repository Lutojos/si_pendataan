<?php

return [
    'authentication_key' => env('FIREBASE_SERVER_KEY'),
    'zoom_level'           => 13,
    'detail_zoom_level'    => 20,
    'map_center_latitude'  => env('MAP_CENTER_LATITUDE', '-0.498696490052'),
    'map_center_longitude' => env('MAP_CENTER_LONGITUDE', '117.15355485677'),
];
