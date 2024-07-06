<?php
declare(strict_types=1);
namespace org\etrusci\sky;
require_once 'conf.php';
require_once 'data.php';
?>
<!DOCTYPE html>
<html lang="en-US" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="application-name" content="skyrecorder">

    <link rel="stylesheet" href="./style.min.css?v=<?php print(filemtime('./style.min.css')); ?>">

    <title>skyrecorder</title>
</head>
<body>
    <noscript>
        <strong>THIS SITE REQUIRES JAVASCRIPT TO WORK.</strong>
        Here are <a href="https://www.enable-javascript.com/">instructions how to enable JavaScript in your web browser</a>.
    </noscript>

    <header>
        <div class="grid-2">
            <div>
                <h1>skyrecorder</h1>
            </div>
            <div>
                <a class="theme-toggle ico sun"></a>
            </div>
        </div>
    </header>

    <main>
        <div class="recent">
            <h2>recent view</h2>
            <p>
                This image will automagically update every ~<?php print(($conf->recent_expected_interval / 1000) / 60) ?> minutes if all systems are running.
                It is dark at night...
            </p>
            <img data-mtime="<?php print(filemtime($conf->recent_img_url)); ?>" src="<?php print($conf->recent_img_url); ?>" alt="recent view">
        </div>

        <div class="timelapse">
            <h2>timelapse archive</h2>
            <div class="grid-2">
                <nav>
                    <ul>
                        <?php
                        foreach ($timelapse_archive as $date => $month) {
                            printf('<li><a data-date="%1$s" title="duration: %2$s">%1$s</a></li>', $date, $month['dur']);
                        }
                        ?>
                    </ul>
                </nav>
                <div class="media"></div>
            </div>
        </div>

        <div class="about">
            <h2>about</h2>

            <h3>project</h3>
            <p>Recording the sky to create monthly timelapses.</p>
            <p>
                Images and videos © 2024 <a href="https://etrusci.org" target="_blank">arT2</a><br>
                Source-code is licensed under <a href="https://github.com/etrusci-org/skyrecorder/blob/main/LICENSE.md" target="_blank">The Unlicense</a>
            </p>

            <!-- TODO: add paragraph for vj's and artists who want to use the timelapses or raw recordings for their work. -->

            <h3>hardware</h3>
            recorder:
            <ul>
                <li>Raspberry Pi 4 Model B 4GB (Rev 1.1)</li>
                <li>Logitech C920 HD Pro Webcam</li>
            </ul>
            cruncher:
            <ul>
                <li>HP EliteDesk 705 G1 DM</li>
                <li>Western Digital Elements 1 TB External Hard Drive</li>
            </ul>
            code repository:
            <p><a href="https://github.com/etrusci-org/skyrecorder" target="_blank">github.com/etrusci-org/skyrecorder</a></p>
        </div>
    </main>

    <script>
        const RECENT_CHECK_INTERVAL = <?php print($conf->recent_check_interval); ?>
    </script>
    <script src="./app.js?v=<?php print(filemtime('./app.js')); ?>"></script>
</body>
</html>
