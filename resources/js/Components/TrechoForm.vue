<script setup>
import { ref, onMounted, watch } from "vue";
import { router } from "@inertiajs/vue3";
import MainLayout from "@/Pages/MainLayout.vue";
import api from "@/services/api";
import axios from "axios";

const dataReferencia = ref(localStorage.getItem("dataReferencia") || "");
const uf = ref(localStorage.getItem("uf") || "");
const rodovia = ref(localStorage.getItem("rodovia") || "");
const tipoTrecho = ref(localStorage.getItem("tipoTrecho") || "");
const quilometragemInicial = ref(
    localStorage.getItem("quilometragemInicial") || ""
);
const quilometragemFinal = ref(
    localStorage.getItem("quilometragemFinal") || ""
);
const responseMessage = ref("");

const ufs = ref([]);
const rodovias = ref([]);
const trechos = ref([]);
const geoJson = ref(null);
const fecharModalNovo = ref(false);

watch(
    [
        dataReferencia,
        uf,
        rodovia,
        tipoTrecho,
        quilometragemInicial,
        quilometragemFinal,
    ],
    () => {
        localStorage.setItem("dataReferencia", dataReferencia.value);
        localStorage.setItem("uf", uf.value);
        localStorage.setItem("rodovia", rodovia.value);
        localStorage.setItem("tipoTrecho", tipoTrecho.value);
        localStorage.setItem(
            "quilometragemInicial",
            quilometragemInicial.value
        );
        localStorage.setItem("quilometragemFinal", quilometragemFinal.value);
    }
);

const carregarDados = async () => {
    try {
        const [responseUFs, responseRodovias] = await Promise.all([
            axios.get("/api/ufs"),
            axios.get("/api/rodovias"),
            api.get("/trechos"),
        ]);
        ufs.value = responseUFs.data;
        rodovias.value = responseRodovias.data;
    } catch (error) {
        console.error("Erro ao carregar dados:", error);
    }
};

onMounted(carregarDados);

const cadastrarTrecho = async () => {
    if (
        !dataReferencia.value ||
        !uf.value ||
        !rodovia.value ||
        !tipoTrecho.value ||
        !quilometragemInicial.value ||
        !quilometragemFinal.value
    ) {
        responseMessage.value = "Por favor, preencha todos os campos.";
        return;
    }

    try {
        const response = await api.post("/trechos", {
            data_referencia: dataReferencia.value,
            uf_id: parseInt(uf.value),
            rodovia_id: parseInt(rodovia.value),
            tipo: tipoTrecho.value,
            quilometragem_inicial: parseFloat(
                quilometragemInicial.value
            ).toFixed(3),
            quilometragem_final: parseFloat(quilometragemFinal.value).toFixed(
                3
            ),
        });

        geoJson.value = response.data.geo
            ? JSON.parse(response.data.geo)
            : null;
        trechos.value.push(response.data);

        localStorage.removeItem("dataReferencia");
        localStorage.removeItem("uf");
        localStorage.removeItem("rodovia");
        localStorage.removeItem("tipoTrecho");
        localStorage.removeItem("quilometragemInicial");
        localStorage.removeItem("quilometragemFinal");

        fecharModalNovo.value = false;
        responseMessage.value = "Trecho cadastrado com sucesso!";
        router.push("/trechos");
        window.location.reload();
    } catch (error) {
        console.error("Erro ao cadastrar:", error);
        responseMessage.value =
            error.response?.data?.error || "Erro ao cadastrar o trecho.";
    }
};
</script>

<template>
    <MainLayout>
        <div class="max-w-md mx-auto bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-xl mb-4 text-center">Cadastrar Novo Trecho</h2>

            <form @submit.prevent="cadastrarTrecho">
                <div class="mb-4">
                    <label for="dataReferencia" class="block text-sm"
                        >Data de Referência:</label
                    >
                    <input
                        v-model="dataReferencia"
                        type="date"
                        id="dataReferencia"
                        class="border p-2 w-full rounded"
                    />
                </div>

                <div class="mb-4">
                    <label for="suf" class="block text-sm"
                        >Unidade da Federação:</label
                    >
                    <select
                        v-model="uf"
                        id="suf"
                        class="border p-2 w-full rounded"
                    >
                        <option value="">Selecione</option>
                        <option
                            v-for="item in ufs"
                            :key="item.id"
                            :value="item.id"
                        >
                            {{ item.sigla }}
                        </option>
                    </select>
                </div>

                <div class="mb-4">
                    <label for="sbr" class="block text-sm">Rodovia:</label>
                    <select
                        v-model="rodovia"
                        id="sbr"
                        class="border p-2 w-full rounded"
                    >
                        <option value="">Selecione</option>
                        <option
                            v-for="item in rodovias"
                            :key="item.id"
                            :value="item.id"
                        >
                            {{ item.codigo }}
                        </option>
                    </select>
                </div>

                <div class="mb-4">
                    <label for="stipo" class="block text-sm"
                        >Tipo de Trecho:</label
                    >
                    <select
                        v-model="tipoTrecho"
                        id="stipo"
                        class="border p-2 w-full rounded"
                    >
                        <option value="">Selecione</option>
                        <option value="B">B</option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="skmi" class="block text-sm"
                            >Km Inicial:</label
                        >
                        <input
                            v-model="quilometragemInicial"
                            type="number"
                            step="0.001"
                            id="skmi"
                            class="border p-2 w-full rounded"
                        />
                    </div>

                    <div>
                        <label for="skmf" class="block text-sm"
                            >Km Final:</label
                        >
                        <input
                            v-model="quilometragemFinal"
                            type="number"
                            step="0.001"
                            id="skmf"
                            class="border p-2 w-full rounded"
                        />
                    </div>
                </div>

                <button
                    type="submit"
                    class="bg-blue-600 text-white p-2 rounded w-full mt-4"
                >
                    Cadastrar
                </button>
            </form>

            <div v-if="responseMessage" class="mt-4 text-green-600 text-center">
                {{ responseMessage }}
            </div>
        </div>
    </MainLayout>
</template>
