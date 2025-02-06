<?php
namespace org\etrusci\sky;
require __DIR__.'/../protected/web.php'; ?>
<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light dark">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.zinc.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat&family=Poiret+One&display=swap');

        :root {
            --pico-font-family: 'Montserrat', serif;
            --pico-font-weight: 400;
            font-style: normal;
        }

        h1, h2 {
            --pico-font-family: 'Poiret One', serif;
            --pico-font-weight: 400;
        }

        body {
            padding: .5rem 2rem;
            margin-bottom: 100vh;
        }

        h1, h2 {
            letter-spacing: 2px;
        }

        header {
            margin-bottom: 3rem;
        }

        main {
            min-width: 300px;
            max-width: 1000px;
            margin: 0 auto;
        }

        article {
            margin-bottom: 10rem;
        }

        ul.archive {
            font-family: var(--pico-font-family-monospace);
            padding-left: 0;
        }

        ul.archive li {
            list-style-type: none;
        }

        ul.archive li a {
            margin-left: .2rem;
        }

        .selected {
            font-weight: bold;
        }

        a.selected {
            text-decoration: none;
            color: var(--pico-mark-color);
        }

        .lazycode {
            font-family: var(--pico-font-family-monospace);
        }

        .videobox {
            position: relative;
            padding-top: 56.25%;
            overflow: hidden;
            max-width: 100%;
            margin-bottom: 1.5rem;
        }

        .videobox iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            max-height: 100%;
        }
    </style>

    <title>SKYRECORDER</title>
</head>
<body>
    <noscript>JavaScript is disabled in your browser, therefore some things won't work on this site.</noscript>


    <header>
        <h1><a href="./">SKYRECORDER</a></h1>
        <em>Recording the sky to create monthly timelapses.</em>
    </header>


    <main>
        <article>
            <h2 id="archive">timelapse video archive</h2>

            <ul class="archive">
                <?php
                $dump = [];
                foreach ($DATA as $timelapse) {
                    $dump[substr($timelapse->month, 0, 4)][] = $timelapse;
                }

                foreach ($dump as $year => $archive) {
                    printf('
                        <li><strong>%1$s</strong> ',
                        $year,
                    );

                    foreach ($archive as $timelapse) {
                        printf('
                            <a href="?timelapse=%1$s#archive"%4$s title="duration: %3$s">%2$s</a>',
                            $timelapse->month,
                            substr($timelapse->month, 5, 2),
                            $timelapse->duration_human,
                            (isset($_GET['timelapse']) && $timelapse->month == $_GET['timelapse']) ? ' class="selected"' : '',
                        );
                    }

                    print('</li>');
                }
                ?>
            </ul>

            <?php
            if (isset($_GET['timelapse'])) {
                foreach ($DATA as $timelapse) {
                    if ($timelapse->month != $_GET['timelapse']) continue;

                    printf('
                        <strong>%1$s &middot; %2$s %3$s &middot; duration: %4$s</strong>',
                        $timelapse->month,
                        month_to_name(month: $timelapse->month),
                        substr($timelapse->month, 0, 4),
                        $timelapse->duration_human,
                    );

                    foreach ($timelapse->dist as $platform_link) {
                        if (strstr($platform_link, 'youtube.com')) {
                            printf('
                                <div class="lazycode">{
                                    "type": "youtubevideo",
                                    "slug": "%1$s"
                                }</div>',
                                explode('?v=', $platform_link)[1],
                            );
                        }

                        if (strstr($platform_link, 'odysee.com')) {
                            printf('
                                backup/mirror server (no ads):
                                <div class="lazycode">{
                                    "type": "odyseevideo",
                                    "slug": "%1$s"
                                }</div>',
                                explode('odysee.com/', $platform_link)[1],
                            );
                        }
                    }
                }
            }
            ?>
        </article>


        <article>
            <h2 id="recent">recent live view snapshot</h2>

            <p>
                This snapshot of the live view will automagically refresh every ~30 minutes if all systems are running.
                It is dark at night...
            </p>

            <?php if (!isset($_GET['timelapse'])): ?>
                <img class="recent" src="<?php print($RECENT_IMG_SRC.'?t='.time()); ?>" alt="recent view" loading="lazy">
            <?php else: ?>
                <p>
                    <a href="./#recent" role="button">open</a>
                </p>
            <?php endif; ?>
        </article>


        <article>
            <h2 id="about">about this project</h2>

            <p>Recording the sky to create monthly timelapses.</p>
            <p>
                Videos and images &copy; 2013-2025 <a href="https://etrusci.org" target="_blank">arT2</a>.<br>
                <a href="https://github.com/etrusci-org/skyrecorder" target="_blank">Source-code</a> is licensed under <a href="https://github.com/etrusci-org/skyrecorder/blob/main/LICENSE.md" target="_blank">The Unlicense</a>.
            </p>
            <p>
                Timelapse videos are hosted on <a href="https://www.youtube.com/playlist?list=PLIfP3a7Gq08B4y7phHDGXPvJ5stvj4VoW" target="_blank">YouTube</a> and <a href="https://odysee.com/@skyrecorder:c?view=content" target="_blank">Odysee</a>.<br>
                <small>Because of the length/filesize of the new 2024+ timelapses, I can not longer upload them to Odysee because of platform limits.</small>
            </p>
            <p>
                Advanced users can also access the timelapse video meta data through the <a href="https://pdv.ourspace.ch" target="_blank">PolyDataVault API</a>.
            </p>
        </article>

    </main>


    <script>
        // @ts-check
        window.addEventListener('load', () => {
            console.log('-=[ SKYRECORDER · https://sky.etrusci.org ]=-')

            const recent_img = document.querySelector('img.recent')

            const LM = new LazyMedia()

            const update_recent_img = () =>
            {
                console.debug('update recent img')
                recent_img?.setAttribute('src', `<?php print($RECENT_IMG_SRC); ?>?t=${Date.now()}`)
            }

            if (recent_img) {
                setInterval(() => update_recent_img(),
                    <?php print($RECENT_UPDATE_INTERVAL); ?> * 1_000
                )
            }

            LM.autoembed()
        })


        class LazyMedia
        {
            // This is a stripped down version of my original LazyMedia lib.

            element_selector = '.lazycode'
            videobox_class = 'videobox'
            error_class = 'error'
            slug_template = {
                youtubevideo: 'https://youtube.com/embed/{SLUG}?modestbranding=1&color=white&rel=0&start=0',
                // youtubeplaylist: 'https://youtube.com/embed/videoseries?list={SLUG}&modestbranding=1&color=white&rel=0',
                odyseevideo: 'https://odysee.com/$/embed/{SLUG}',
            }


            autoembed()
            {
                this.get_lazycode_elements().forEach(target_element => {
                    try {
                        // @ts-expect-error
                        const code = JSON.parse(target_element.innerText)
                        const baked_element = this.get_baked_element(code)
                        if (baked_element) {
                            target_element.replaceWith(baked_element)
                        }
                        else {
                            throw (`bake() returned: ${baked_element}`)
                        }
                    }
                    catch (error) {
                        console.error(`Error on ${target_element.innerHTML}\n-> ${error}`)
                        target_element.classList.add(this.error_class)
                    }
                })
            }


            get_lazycode_elements()
            {
                return document.querySelectorAll(this.element_selector)
            }


            get_baked_element(code)
            {
                let baked_element = null

                if (code.type == 'youtubevideo') {
                    code.slug = this.slug_template.youtubevideo.replace('{SLUG}', code.slug)
                    if (code.start) {
                        code.slug = code.slug.replace('start=0', `start=${code.start}`)
                    }
                    baked_element = document.createElement('div')
                    baked_element.classList.add(this.videobox_class)
                    const inner1 = document.createElement('iframe')
                    inner1.setAttribute('src', code.slug)
                    inner1.setAttribute('loading', 'lazy')
                    inner1.setAttribute('allowfullscreen', 'allowfullscreen')
                    inner1.setAttribute('playsinline', 'playsinline')
                    this.#add_code_attr(code, inner1)
                    this.#add_code_css(code, inner1)
                    baked_element.append(inner1)
                }

                // if (code.type == 'youtubeplaylist') {
                //     code.slug = this.slug_template.youtubeplaylist.replace('{SLUG}', code.slug)
                //     baked_element = document.createElement('div')
                //     baked_element.classList.add(this.videobox_class)
                //     const inner1 = document.createElement('iframe')
                //     inner1.setAttribute('src', code.slug)
                //     inner1.setAttribute('loading', 'lazy')
                //     inner1.setAttribute('allowfullscreen', 'allowfullscreen')
                //     inner1.setAttribute('playsinline', 'playsinline')
                //     this.#add_code_attr(code, inner1)
                //     this.#add_code_css(code, inner1)
                //     baked_element.append(inner1)
                // }

                if (code.type == 'odyseevideo') {
                    code.slug = this.slug_template.odyseevideo.replace('{SLUG}', code.slug)
                    baked_element = document.createElement('div')
                    baked_element.classList.add(this.videobox_class)
                    const inner1 = document.createElement('iframe')
                    inner1.setAttribute('src', code.slug)
                    inner1.setAttribute('loading', 'lazy')
                    inner1.setAttribute('allowfullscreen', 'allowfullscreen')
                    inner1.setAttribute('playsinline', 'playsinline')
                    this.#add_code_attr(code, inner1)
                    this.#add_code_css(code, inner1)
                    baked_element.append(inner1)
                }
                return baked_element
            }


            #add_code_attr(code, baked_element)
            {
                if (code.attr) {
                    for (const [k, v] of code.attr) {
                        if (v !== null) {
                            baked_element.setAttribute(k, v)
                        }
                        else {
                            baked_element.removeAttribute(k)
                        }
                    }
                }
            }


            #add_code_css(code, baked_element)
            {
                baked_element.classList.add('lazymedia', code.type)
                if (code.class) {
                    baked_element.classList.add(...code.class)
                }
            }
        }
    </script>
</body>
</html>
