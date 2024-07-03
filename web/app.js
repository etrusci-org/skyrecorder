// @ts-check
'use strict'


const DEV_MODE = false


window.addEventListener('load', () => {
    init_theme_toggle()
    main()
}, false)


const main = () =>
{
    const recent_img = document.querySelector('.recent img')
    if (!(recent_img instanceof HTMLImageElement)) {
        console.error('bad recent_img element')
        return
    }

    const timelapse_nav = document.querySelectorAll('.timelapse nav a')
    timelapse_nav.forEach(element => {
        if (!(element instanceof HTMLAnchorElement)) {
            console.error('bad timelapse nav element')
            return
        }

        element.addEventListener('click', (event) => {
            event.preventDefault()
            render_timelapse_media(element.dataset.date, new LazyMedia())
        }, false)
    })

    if (!DEV_MODE) recent_worker(recent_img)
}


const render_timelapse_media = async (date = new String, LM = new LazyMedia) => {
    const timelapse_media = document.querySelector('.timelapse .media')
    if (!(timelapse_media instanceof HTMLDivElement)) {
        console.error('bad timelapse_media element')
        return
    }

    const r = await api_request(`timelapse=${date}`)

    timelapse_media.innerHTML = ''

    r.timelapse.urls.forEach(url => {
        const e1 = document.createElement('div')
        const e2 = document.createElement('h3')

        e1.classList.add('lazycode')
        e2.textContent = `→ ${date} · ${get_month_name(date)} ${date.split('-')[0]} · ${r.timelapse.dur}`

        if (url.includes('youtube.com/')) {
            e1.textContent = JSON.stringify({
                type: 'youtubevideo',
                slug: url.split('?v=')[1],
            })
        }
        else if (url.includes('odysee.com/')) {
            e1.textContent = JSON.stringify({
                type: 'odyseevideo',
                slug: url.split('odysee.com/')[1],
            })
        }
        else {
            console.warn(`unknown video platform: ${url}`)
            e1.textContent = `unknown video platform: ${url}`
        }

        timelapse_media.append(e2)
        timelapse_media.append(e1)
    })

    if (!DEV_MODE) LM.autoembed()
}


const recent_worker = (recent_img = new HTMLImageElement) =>
{
    let recent_mtime = Number(recent_img.dataset.mtime)

    setInterval(async () => {
        const r = await api_request('recent_mtime')

        if (r.recent_mtime != recent_mtime) {
            recent_img.setAttribute('src', `${recent_img.getAttribute('src')?.split('?')[0]}?mtime=${r.recent_mtime}`)
            recent_img.dataset['mtime'] = r.recent_mtime
            recent_mtime = r.recent_mtime
        }
    // @ts-expect-error: we set CHECK_INTERVAL in index.php before the app.js script
    }, RECENT_CHECK_INTERVAL)
}


const api_request = async (query = '') =>
{
    return fetch(`api.php?${query}`, { method: 'GET', cache: 'no-cache' }).then((r) => r.json())
}


const get_month_name = (date, locale='en-US') =>
{
    return new Date(date).toLocaleString(locale, { month: 'long'})
}


const init_theme_toggle = () =>
{
    const current_theme = localStorage.getItem('skyrecorder.web.theme') ?? 'light'

    if (current_theme) {
        document.documentElement.dataset['theme'] = current_theme
    }

    const theme_toggle = document.querySelector('a.theme-toggle')
    if (!(theme_toggle instanceof HTMLAnchorElement)) {
        console.error('bad theme toggle element')
        return
    }

    if (current_theme == 'light') {
       theme_toggle.classList.replace('moon', 'sun')
    }
    else {
        theme_toggle.classList.replace('sun', 'moon')
    }

    theme_toggle.addEventListener('click', (event) => {
        event.preventDefault()

        const current_theme = document.documentElement.dataset['theme']
        let new_theme = 'light'

        if (current_theme == 'light') {
            new_theme = 'dark'
            theme_toggle.classList.replace('sun', 'moon')
        }
        else {
            new_theme = 'light'
            theme_toggle.classList.replace('moon', 'sun')
        }

        document.documentElement.dataset['theme'] = new_theme
        localStorage.setItem('skyrecorder.web.theme', new_theme)
    })
}


class LazyMedia {
    // stripped down version
    element_selector = '.lazycode'
    videobox_class = 'videobox'
    error_class = 'error'
    slug_template = {
        // twitchstream: 'https://player.twitch.tv/?muted=false&autoplay=false&channel={SLUG}',
        // twitchchat: 'https://twitch.tv/embed/{SLUG}',
        youtubevideo: 'https://youtube.com/embed/{SLUG}?modestbranding=1&color=white&rel=0&start=0',
        // youtubeplaylist: 'https://youtube.com/embed/videoseries?list={SLUG}&modestbranding=1&color=white&rel=0',
        odyseevideo: 'https://odysee.com/$/embed/{SLUG}',
    }
    autoembed() {
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
    get_lazycode_elements() {
        return document.querySelectorAll(this.element_selector)
    }
    get_baked_element(code) {
        let baked_element = null
        // if (code.type == 'twitchstream') {
        //     code.slug = this.slug_template.twitchstream.replace('{SLUG}', code.slug)
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
        // if (code.type == 'twitchchat') {
        //     code.slug = this.slug_template.twitchchat.replace('{SLUG}', code.slug)
        //     baked_element = document.createElement('iframe')
        //     baked_element.setAttribute('src', code.slug)
        //     baked_element.setAttribute('loading', 'lazy')
        //     this.#add_code_attr(code, baked_element)
        //     this.#add_code_css(code, baked_element)
        // }
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
    #add_code_attr(code, baked_element) {
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
    #add_code_css(code, baked_element) {
        baked_element.classList.add('lazymedia', code.type)
        if (code.class) {
            baked_element.classList.add(...code.class)
        }
    }
}
