import "./bootstrap";
import { createApp, h } from "vue";
import { createInertiaApp } from "@inertiajs/vue3";
import "../css/app.css";
import "leaflet/dist/leaflet.css";
import axios from "axios";
import api from "./services/api";

createInertiaApp({
    resolve: async (name) => {
        const pages = import.meta.glob("./Pages/**/*.vue");
        const importPage = pages[`./Pages/${name}.vue`];

        if (!importPage) {
            console.error(`Página ${name} não encontrada!`);
            return;
        }

        return importPage().then((module) => module.default);
    },
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .mount(el);
    },
});
