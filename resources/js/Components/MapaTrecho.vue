<script>
import { ref, watch, onMounted } from "vue";
import L from "leaflet";
import "leaflet/dist/leaflet.css";

globalThis.L = L;

const zoom = ref(5);
const center = ref([-15.7801, -47.9292]);
const trechoGeoJson = ref(null);

export default {
    props: {
        trechoSelecionado: {
            type: Object,
            default: null,
        },
    },
    setup(props) {
        const map = ref(null);

        const carregarTrecho = async (trecho) => {
            try {
                if (!trecho) {
                    return;
                }

                const geoJson = trecho.geo;

                if (geoJson && geoJson.type === "Feature") {
                    if (map.value) {
                        L.geoJSON(geoJson).addTo(map.value);

                        const bounds = L.geoJSON(geoJson).getBounds();
                        map.value.fitBounds(bounds);
                    }
                } else {
                    console.error("GeoJSON inválido no trecho", trecho);
                }
            } catch (error) {
                console.error("Erro ao carregar o GeoJSON:", error);
            }
        };

        onMounted(() => {
            map.value = L.map("map").setView([-15.7801, -47.9292], 5);
            L.tileLayer(
                "https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
            ).addTo(map.value);

            if (props.trechoSelecionado) {
                carregarTrecho(props.trechoSelecionado);
            }
        });

        watch(
            () => props.trechoSelecionado,
            (newTrecho) => {
                if (newTrecho) {
                    carregarTrecho(newTrecho);
                }
            }
        );

        return {};
    },
};
</script>

<template>
    <div id="map" style="height: 400px"></div>
</template>

<style scoped>
#map {
    width: 100%;
    height: 100%;
    position: relative;
    z-index: 1;
}
</style>
