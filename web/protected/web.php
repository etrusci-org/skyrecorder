<?php
declare(strict_types=1);

namespace org\etrusci\sky;


// web/protected/data.json must exist and be writable by the webserver process.
// to force refresh on next page load, empty it... e.g. echo -n > data.json,
// it will be refreshed as long as it is 1byte or less in size.


# CONF

error_reporting(E_ALL);  # set to 0 in prod
$SECRET_FILE = realpath(__DIR__.'/.secret');
$DATA_FILE = realpath(__DIR__.'/data.json');
$RECENT_IMG_SRC = './recent.jpg';  # relative from web/public/ or full URL
$RECENT_UPDATE_INTERVAL = 900;  # seconds
$DATA_TTL = 86_400 * 30;  # seconds



# MAGIC

if (!$SECRET_FILE || !$DATA_FILE) {
    printf('configuration error in %s', basename(__FILE__));
    exit(1);
}

function month_to_name(string $month, array $names = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December']): string
{
    return $names[intval(substr($month, 5, 2)) - 1];
}

$fresh_data = '';

if (time() - filemtime($DATA_FILE) > $DATA_TTL || filesize($DATA_FILE) <= 1) {
    $secret = file_get_contents(filename: $SECRET_FILE);
    $secret = json_decode(json: $secret, associative: false);

    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => 'https://pdv.ourspace.ch/api/collections/dumps/records/skyrecorder',
        CURLOPT_HEADER => false,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => [
            'pdv_api_key: '.$secret->pdv_api_key,
        ],
    ]);

    $response = curl_exec($curl);
    $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    curl_close($curl);
    unset($secret);

    if ($http_code == 200) {
        $response = trim($response);
        $fresh_data = $response;
        file_put_contents(filename: $DATA_FILE, data: $response, flags: LOCK_EX);
    }
}

if (!empty($fresh_data)) {
    $dump = $fresh_data;
}
else {
    $dump = file_get_contents(filename: $DATA_FILE);
}

$dump = json_decode(json: $dump, associative: false);

$DATA = $dump->dump;
