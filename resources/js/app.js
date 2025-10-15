import '../css/app.css';
import axios from 'axios';
import {createApp} from 'vue'
import {createInertiaApp} from '@inertiajs/vue3'
import {Modal, ModalLink, renderApp} from '@inertiaui/modal-vue'
import {ZiggyVue} from '../../vendor/tightenco/ziggy';
import NProgress from 'nprogress'
import {router} from '@inertiajs/vue3'

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Конфигурация
NProgress.configure({
    showSpinner: false, // Скрыть спиннер (если не нужен)
    easing: 'ease', // Анимация
    speed: 500, // Скорость
    template: `
    <div class="bar" role="bar">
      <div class="peg"></div>
    </div>
    <div class="spinner" role="spinner">
      <div class="spinner-icon"></div>
    </div>
  `
})

router.on('start', () => NProgress.start())
router.on('finish', () => NProgress.done())
router.on('error', () => NProgress.done())

createInertiaApp({
    resolve: name => {
        const pages = import.meta.glob('./pages/**/*.vue', {eager: true})
        return pages[`./pages/${name}.vue`]
    },
    setup({el, App, props, plugin}) {
        return createApp({render: renderApp(App, props)})
            .use(plugin)
            .use(ZiggyVue)
            .component('Modal', Modal)
            .component('ModalLink', ModalLink)
            .mount(el)
    },
})
