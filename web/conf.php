<?php
declare(strict_types=1);
namespace org\etrusci\sky;
use Throwable;




class Conf {
    public int $error_reporting = E_ALL;
    public int $recent_expected_interval = 1800_000; # ms
    public int $recent_check_interval = 600_000; # ms
    public string $recent_img_url = './recent.jpg';


    public function __construct()
    {
        error_reporting($this->error_reporting);
    }
}


$conf = new Conf;
