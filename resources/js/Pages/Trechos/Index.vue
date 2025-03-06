<script setup>
import { ref, onMounted, watch } from "vue";
import axios from "axios";
import MainLayout from "@/Pages/MainLayout.vue";
import { usePage } from "@inertiajs/vue3";
import TrechoForm from "@/Components/TrechoForm.vue";
import MapaTrecho from "@/Components/MapaTrecho.vue";

const trechos = ref(JSON.parse(localStorage.getItem("trechos")) || []);
const successMessage = ref("");
const errorMessage = ref("");
const modalAberto = ref(false);
const modalNovoAberto = ref(false);
const trechoEditando = ref({
    tipo: "",
    quilometragem_inicial: 0,
    quilometragem_final: 0,
    uf_id: "",
    rodovia_id: "",
});

const trechoSelecionado = ref(null);
const ufs = ref([]);
const rodovias = ref([]);

watch(
    trechos,
    () => {
        localStorage.setItem("trechos", JSON.stringify(trechos.value));
    },
    { deep: true }
);

const carregarTrechos = () => {
    const pageProps = usePage().props;
    if (pageProps && pageProps.trechos) {
        trechos.value = pageProps.trechos;
    } else {
        trechos.value = [];
    }
};

const carregarUfsERodovias = async () => {
    try {
        const response = await axios.get("/api/trechos");
        ufs.value = response.data.ufs;
        rodovias.value = response.data.rodovias;
    } catch (error) {
        console.error("Erro ao carregar UFs e Rodovias", error);
    }
};

const selecionarTrecho = (trecho) => {
    trechoSelecionado.value = trecho;
};

const abrirModalEdicao = (trecho) => {
    trechoEditando.value = { ...trecho };
    modalAberto.value = true;
};

const abrirModalNovo = () => {
    modalNovoAberto.value = true;
};

const fecharModalNovo = () => {
    modalNovoAberto.value = false;
};

const fecharModal = () => {
    modalAberto.value = false;
    trechoEditando.value = {
        tipo: "",
        quilometragem_inicial: 0,
        quilometragem_final: 0,
        uf_id: "",
        rodovia_id: "",
    };
};

const salvarEdicao = async () => {
    try {
        await axios.put(
            `/api/trechos/${trechoEditando.value.id}`,
            trechoEditando.value
        );

        trechos.value = trechos.value.map((trecho) =>
            trecho.id === trechoEditando.value.id
                ? trechoEditando.value
                : trecho
        );

        modalAberto.value = false;
    } catch (error) {
        errorMessage.value = "Erro ao salvar edição. Tente novamente.";
        console.error(error);
    }
};

const excluirTrecho = async (id) => {
    if (confirm("Tem certeza que deseja excluir este trecho?")) {
        try {
            await axios.delete(`/api/trechos/${id}`);
            successMessage.value = "Trecho excluído com sucesso!";

            trechos.value = trechos.value.filter((trecho) => trecho.id !== id);

            setTimeout(() => {
                successMessage.value = "";
            }, 3000);
        } catch (error) {
            errorMessage.value = "Erro ao excluir trecho.";
        }
    }
};

onMounted(() => {
    carregarTrechos();
    carregarUfsERodovias();
});
</script>

<template>
    <MainLayout>
        <div class="max-w-4xl mx-auto p-4">
            <h2 class="text-2xl font-semibold mb-4 text-center">
                Lista de Trechos Cadastrados
            </h2>

            <button
                @click="abrirModalNovo"
                class="bg-green-500 text-white px-4 py-2 rounded mb-4"
            >
                Adicionar Trecho
            </button>

            <table
                class="w-full table-auto border-collapse border border-gray-300"
            >
                <thead class="bg-gray-200">
                    <tr>
                        <th class="border border-gray-300 p-1 text-sm">UF</th>
                        <th class="border border-gray-300 p-1 text-sm">
                            Rodovia
                        </th>
                        <th class="border border-gray-300 p-1 text-sm">
                            KM Inicial
                        </th>
                        <th class="border border-gray-300 p-1 text-sm">
                            KM Final
                        </th>
                        <th class="border border-gray-300 p-1 text-sm">
                            Ações
                        </th>
                    </tr>
                </thead>
                <tbody v-if="trechos.length > 0">
                    <tr v-for="trecho in trechos" :key="trecho.id">
                        <td class="border border-gray-300 p-2 text-sm">
                            {{ trecho.uf?.sigla || "N/A" }}
                        </td>
                        <td class="border border-gray-300 p-2 text-sm">
                            {{ trecho.rodovia?.codigo || "N/A" }}
                        </td>

                        <td class="border border-gray-300 p-2 text-sm">
                            {{ trecho.quilometragem_inicial || "N/A" }}
                        </td>
                        <td class="border border-gray-300 p-2 text-sm">
                            {{ trecho.quilometragem_final || "N/A" }}
                        </td>
                        <td class="border border-gray-300 p-2 text-sm">
                            <button
                                @click="selecionarTrecho(trecho)"
                                class="bg-blue-500 text-white p-1 rounded mr-2"
                            >
                                Selecionar
                            </button>
                            <button
                                @click="abrirModalEdicao(trecho)"
                                class="bg-blue-500 text-white p-1 rounded mr-2"
                            >
                                Editar
                            </button>
                            <button
                                @click="excluirTrecho(trecho.id)"
                                class="bg-red-500 text-white p-1 rounded"
                            >
                                Excluir
                            </button>
                        </td>
                    </tr>
                </tbody>
                <tbody v-else>
                    <tr>
                        <td
                            colspan="6"
                            class="border p-4 text-center text-gray-500"
                        >
                            Nenhum trecho cadastrado.
                        </td>
                    </tr>
                </tbody>
            </table>

            <div v-if="trechoSelecionado" class="mt-8">
                <h3 class="text-xl font-semibold mb-4 text-center"></h3>
            </div>
        </div>

        <div
            v-if="modalNovoAberto"
            class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 p-4 z-50"
        >
            <div
                class="bg-white p-4 md:p-6 rounded-lg shadow-lg w-full sm:w-3/4 md:w-1/2 lg:w-1/3 max-w-lg sm:max-w-xl"
            >
                <h3 class="text-lg md:text-xl font-semibold mb-4 text-center">
                    Adicionar Novo Trecho
                </h3>

                <TrechoForm />

                <div class="flex justify-end mt-4">
                    <button
                        @click="fecharModalNovo"
                        class="bg-gray-500 text-white px-4 py-2 rounded"
                    >
                        Cancelar
                    </button>
                </div>
            </div>
        </div>

        <div
            v-if="modalAberto"
            class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 z-50"
        >
            <div class="bg-white p-6 rounded-lg shadow-lg w-1/3">
                <h3 class="text-xl font-semibold mb-4">Editar Trecho</h3>

                <label class="block text-sm font-medium">Tipo:</label>
                <input
                    v-model="trechoEditando.tipo"
                    class="w-full border p-2 mb-2"
                />

                <label class="block text-sm font-medium">KM Inicial:</label>
                <input
                    v-model="trechoEditando.quilometragem_inicial"
                    type="number"
                    class="w-full border p-2 mb-2"
                />

                <label class="block text-sm font-medium">KM Final:</label>
                <input
                    v-model="trechoEditando.quilometragem_final"
                    type="number"
                    class="w-full border p-2 mb-2"
                />

                <div class="flex justify-end mt-4">
                    <button
                        @click="salvarEdicao"
                        class="bg-green-500 text-white px-4 py-2 rounded mr-2"
                    >
                        Salvar
                    </button>
                    <button
                        @click="fecharModal"
                        class="bg-gray-500 text-white px-4 py-2 rounded"
                    >
                        Cancelar
                    </button>
                </div>
            </div>
        </div>

        <div
            class="relative z-10 w-3/4 h-96 sm:h-80 md:h-96 lg:h-128 border-2 border-solid border-gray-500 rounded-lg mt-12 overflow-hidden mx-auto"
        >
            <MapaTrecho :trechoSelecionado="trechoSelecionado" />
        </div>
    </MainLayout>
</template>
