// @ts-check
'use strict'

import { SKYRECORDER_DATA as D } from "./data.js"

const C = {
    recent: {
        img_url: './recent.jpg',
        expected_interval: 1800_000,
        check_interval: 600_000,
        tick_interval: 1_000,
    },
}

const E = {
    recent: {
        container: document.querySelector('.recent'),
        img: document.querySelector('.recent img'),
        expected_interval: document.querySelector('.recent .expected_interval'),
        timer_last_check: document.querySelector('.recent .timer.last_check'),
        timer_last_update: document.querySelector('.recent .timer.last_update'),
    },
}



//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~


window.addEventListener('load', main, false)


//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~


function main()
{
    console.log(`skyrecorder/web | https://sky.etrusci.org | https://github.com/etrusc-org/skyrecorder`)

    if (!E.recent.container) return
    if (!E.recent.expected_interval) return

    if (!window.Worker) {
        alert('Your webbrowser does not seem to support the Web Worker API.')
        return
    }

    E.recent.expected_interval.textContent = fmt_sec_to_dhms(C.recent.expected_interval, true)

    update_recent_img_src()

    const RecentWorker = new Worker('./worker.recent.js')
    RecentWorker.onmessage = on_bgworker_msg
    RecentWorker.postMessage({
        cmd: 'ready',
        c: C,
    })
}


function on_bgworker_msg(ev)
{
    if (!E.recent.img) return
    if (!E.recent.timer_last_check) return
    if (!E.recent.timer_last_update) return

    if (ev.data.cmd == 'recent_tick') {
        const timer_ago = Date.now() - ev.data.last_update
        const last_check_ago = Date.now() - ev.data.last_check

        E.recent.timer_last_check.textContent = fmt_sec_to_dhms(last_check_ago)
        E.recent.timer_last_update.textContent = fmt_sec_to_dhms(timer_ago)

        if (timer_ago > C.recent.expected_interval) {
            E.recent.timer_last_update.classList.replace('good', 'bad')
        }
        else {
            E.recent.timer_last_update.classList.replace('bad', 'good')
        }
    }

    if (ev.data.cmd == 'recent_update') {
        update_recent_img_src()
    }
}


function update_recent_img_src()
{
    if (!E.recent.img) return

    E.recent.img.setAttribute('src', `${C.recent.img_url}?t=${Date.now()}`)
}


function fmt_sec_to_dhms(seconds = 0, no_word = false)
{
    seconds = Math.floor(Math.max(0, seconds / 1000))

    const d = Math.floor(seconds / (3600 * 24))
    const h = Math.floor(seconds % (3600 * 24) / 3600)
    const m = Math.floor(seconds % 3600 / 60)
    const s = Math.floor(seconds % 60)

    let out = []

    if (d > 0) out.push(`${d}d`)
    if (h > 0) out.push(`${h}h`)
    if (m > 0) out.push(`${m}m`)
    if (s > 0) out.push(`${s}s`)

    if (!no_word) {
        if (out.length == 0) {
            out.push('now')
        }
        else {
            out.push('ago')
        }
    }

    return out.join(' ')

}
