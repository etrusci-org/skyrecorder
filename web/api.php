<?php
declare(strict_types=1);
namespace org\etrusci\sky;
require_once 'conf.php';
require_once 'data.php';


function dur_to_sec(string $dur): int
{
    $dur = explode(':', $dur);
    $sec = 0;

    if (count($dur) == 3) {
        $sec += $dur[0] * 60 * 60;
        $sec += $dur[1] * 60;
        $sec += $dur[2];
    }
    else if (count($dur) == 2) {
        $sec += $dur[0] * 60;
        $sec += $dur[1];
    }
    else if (count($dur) == 1) {
        $sec += $dur[0];
    }

    return $sec;
}


$response = [];

// ?recent_mtime
if (isset($_GET['recent_mtime'])) {
    $response['recent_mtime'] = filemtime($conf->recent_img_url);
}
else if (isset($_GET['timelapse'])) {

    // ?timelapse
    if (empty($_GET['timelapse'])) {
        $response['timelapse'] = [];
        foreach ($timelapse_archive as $date => $timelapse) {
            $timelapse['dur_sec'] = dur_to_sec($timelapse['dur']);
            $response['timelapse'][] = $timelapse;
        }
    }
    // ?timelapse=<YYYY-MM>
    else {
        if (preg_match('/[0-9]{4}-[0-9]{2}/', $_GET['timelapse']) == 1) {
            $response['timelapse'] = $timelapse_archive[$_GET['timelapse']];
            $response['timelapse']['date'] = $_GET['timelapse'];
            $response['timelapse']['dur_sec'] = dur_to_sec($response['timelapse']['dur']);
        }
    }
}
// help
else {
    $response = [
        'hello' => 'PLEASE CACHE RESULTS ON YOUR SIDE. THANK YOU!',
        'usage' => [
            'base_url' => sprintf('%s://%s%s', $_SERVER['REQUEST_SCHEME'], $_SERVER['HTTP_HOST'], $_SERVER['REQUEST_URI']),
            'valid_params' => [
                '?recent_mtime | returns filemtime of recent.jpg',
                '?timelapse | returns list of timelapses',
                '?timelapse=<YYYY-MM> | returns specific timelapse',
            ],
        ],
    ];
}


header('content-type: application/json; charset=utf-8');
print(json_encode($response, JSON_NUMERIC_CHECK | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR));
exit(0);
