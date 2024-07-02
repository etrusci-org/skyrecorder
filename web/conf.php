<?php
declare(strict_types=1);
namespace org\etrusci\sky;


error_reporting(E_ALL);


class Conf {
    public int $recent_expected_interval = 1800_000; # ms
    public int $recent_check_interval = 600_000; # ms
    public string $recent_img_url = './recent.jpg';
}


$conf = new Conf;
