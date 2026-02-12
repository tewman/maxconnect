import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';

const rootId = 'app';

function initInertia() {
    const el = document.getElementById(rootId);
    if (!el) {
        console.error(`Inertia: root element #${rootId} not found. Ensure the server returns the Inertia root view with @inertia.`);
        document.body.innerHTML = '<div style="padding:2rem;font-family:sans-serif;"><p>Root element not found. Check that the page is served by Laravel with the Inertia root view (app.blade.php) and that SSR is disabled in config/inertia.php.</p></div>';
        return;
    }
    createInertiaApp({
        id: rootId,
        title: (title) => title ? `${title} - ${import.meta.env.VITE_APP_NAME || 'MaxConnect'}` : (import.meta.env.VITE_APP_NAME || 'MaxConnect'),
        resolve: (name) => {
            const pages = import.meta.glob('./Pages/**/*.vue');
            return pages[`./Pages/${name}.vue`]();
        },
        setup({ el, App, props, plugin }) {
            return createApp({ render: () => h(App, props) })
                .use(plugin)
                .mount(el);
        },
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initInertia);
} else {
    initInertia();
}
