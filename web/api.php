<?php
declare(strict_types=1);
namespace org\etrusci\sky;
require_once 'conf.php';
require_once 'data.php';


$response = [];

// ?recent_mtime
if (isset($_GET['recent_mtime'])) {
    $response['recent_mtime'] = filemtime($conf->recent_img_url);
}
else if (isset($_GET['timelapse'])) {

    // ?timelapse
    if (empty($_GET['timelapse'])) {
        $response['timelapse'] = $timelapse_archive;
    }
    // ?timelapse=<YYYY-MM>
    else {
        if (preg_match('/[0-9]{4}-[0-9]{2}/', $_GET['timelapse']) == 1) {
            $response['timelapse'] = $timelapse_archive[$_GET['timelapse']];
            $response['timelapse']['date'] = $_GET['timelapse'];
        }
    }
}
// help
else {
    $response = [
        'hello' => 'PLEASE DO NOT ABUSE AND CACHE RESULTS ON YOUR SIDE. THANK YOU!',
        'base_url' => sprintf('%s://%s%s', $_SERVER['REQUEST_SCHEME'], $_SERVER['HTTP_HOST'], $_SERVER['REQUEST_URI']),
        'valid_params' => [
            '?recent_mtime | returns filemtime of recent.jpg',
            '?timelapse | returns list of timelapses',
            '?timelapse=<YYYY-MM> | returns specific timelapse',
        ],
    ];
}


header('content-type: application/json; charset=utf-8');
print(json_encode($response, JSON_NUMERIC_CHECK | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR));
exit(0);
