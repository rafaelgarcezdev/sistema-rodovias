<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";
import { router } from "@inertiajs/vue3";
import MainLayout from "@/Pages/MainLayout.vue";

const dataReferencia = ref("");
const uf = ref("");
const rodovia = ref("");
const tipoTrecho = ref("");
const quilometragemInicial = ref("");
const quilometragemFinal = ref("");
const responseMessage = ref("");

const ufs = ref([]);
const rodovias = ref([]);

const carregarDados = async () => {
    try {
        const [responseUFs, responseRodovias] = await Promise.all([
            axios.get("api/ufs"),
            axios.get("api/rodovias"),
            axios.get("api/trechos"),
        ]);
        console.log(responseUFs.data);
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
        await axios.post("/api/trechos", {
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

        responseMessage.value = "Trecho cadastrado com sucesso!";
        router.push("/api/trechos");
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
