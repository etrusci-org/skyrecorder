// @ts-check
'use strict'


onmessage = function(ev)
{
    if (ev.data.cmd == 'ready') {
        _recent_worker(ev.data.c.recent.img_url, ev.data.c.recent.check_interval, ev.data.c.recent.tick_interval)
    }
    else if (ev.data.cmd == 'die') {
        close()
    }
}


async function _recent_worker(img_url = '', check_interval = 10_000, tick_interval = 1_000)
{
    let next_check_on = Date.now() + check_interval
    let last_update_on = Date.now()
    let last_check_on = Date.now()
    let prev_etag = await _get_etag(img_url)

    setInterval(async () => {
        postMessage({
            cmd: 'recent_tick',
            next_check_on: next_check_on,
            last_check: last_check_on,
            last_update: last_update_on,
        })

        if (Date.now() < next_check_on) {
            return
        }

        const etag = await _get_etag(img_url)

        if (etag !== null && etag != prev_etag) {
            postMessage({
                cmd: 'recent_update',
            })
            last_update_on = Date.now()
            prev_etag = etag ?? ''
        }

        last_check_on = Date.now()
        next_check_on = Date.now() + check_interval
    }, tick_interval)
}


async function _get_etag(url = '')
{
    const r = await fetch(url, { method: 'HEAD', cache: 'no-cache' })

    const etag = r.headers.get('etag')

    if (!etag || r.status != 200) {
        return null
    }

    return etag
}
