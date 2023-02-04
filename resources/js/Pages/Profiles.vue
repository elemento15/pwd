<script setup>
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
import BreezeButton from '@/Components/Button.vue';
import { Head } from '@inertiajs/inertia-vue3';
import axios from 'axios';
import { ref, reactive, onMounted } from 'vue';

const dataTable = reactive({
    list: []
});

const showForm = ref(false);


onMounted(() => {
    fetchData();
});

function fetchData() {
    axios.get('/api/profiles')
        .then((response) => {
            dataTable.list = response.data;
        });
}
</script>

<template>
    <Head title="Perfiles" />

    <BreezeAuthenticatedLayout>
        <div class="py-4">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">

                        <!-- Table -->
                        <div v-show="!showForm">
                            <div>
                                <BreezeButton 
                                    type="button"
                                    @click="showForm = true">
                                    Agregar
                                </BreezeButton>
                            </div>
                            
                            <div class="grid grid-cols-3 mt5">
                                <table class="table cls-table">
                                    <thead class="">
                                        <tr>
                                            <th>&nbsp;</th>
                                            <th>Email</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="row in dataTable.list">
                                            <td>...</td>
                                            <td>{{ row.email }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Form -->
                        <div v-show="showForm">
                            <div>
                                <div>
                                    <BreezeButton 
                                        type="button"
                                        @click="showForm = false">
                                        Cerrar
                                    </BreezeButton>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </BreezeAuthenticatedLayout>
</template>
