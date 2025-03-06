<script>
import { ref, watch, onMounted, defineProps } from "vue";
import { LMap, LTileLayer, LGeoJson } from "@vue-leaflet/vue-leaflet";
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

        // Função para carregar o trecho selecionado
        const carregarTrecho = async (trecho) => {
            try {
                if (!trecho) {
                    return;
                }

                const geoJson = trecho.geo; // Acessando o campo geo

                if (geoJson && geoJson.type === "Feature") {
                    // Adicionar o GeoJSON do trecho selecionado ao mapa
                    if (map.value) {
                        L.geoJSON(geoJson).addTo(map.value);
                        // Ajustar a vista do mapa para o centro do trecho
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
            // Inicializar o mapa
            map.value = L.map("map").setView([-15.7801, -47.9292], 5); // Coordenadas iniciais
            L.tileLayer(
                "https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
            ).addTo(map.value);

            // Carregar o trecho selecionado se houver
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
