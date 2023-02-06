require('./bootstrap');

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/inertia-vue3';
import { InertiaProgress } from '@inertiajs/progress';
import qs from 'qs';

const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => require(`./Pages/${name}.vue`),
    setup({ el, app, props, plugin }) {
        return createApp({ render: () => h(app, props) })
        .use(plugin)
        .mixin({ methods: { route } })
        .mount(el);
    },
});

// Format nested params correctly
axios.interceptors.request.use(config => {
    //window.console.log(config);
    config.paramsSerializer = params => {
        return qs.stringify(params, {
            arrayFormat: "brackets",
            encode: false
        });
    };
    
    return config;
});

import "bootstrap/dist/js/bootstrap.min.js";
import 'bootstrap-icons/font/bootstrap-icons.css';

InertiaProgress.init({ color: '#4B5563' });
