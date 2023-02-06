<script setup>
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
import Paginator from '@/Components/Paginator.vue';
import { Head } from '@inertiajs/inertia-vue3';
import axios from 'axios';
import { ref, reactive, onMounted } from 'vue';

const dataTable = reactive({
    list: [],
    paginator: {
        page: 1,
        last: 0,
    }
});

const form = reactive({
    id: 0,
    email: ''
});

const def_input = ref(null);

const show_form = ref(false);


onMounted(() => {
    fetchData();
});

function fetchData(page = false) {
    let params = {
        page: page || dataTable.paginator.page,
        order: {
            field: 'email'
        }
    };

    axios.get('api/profiles', { 
        params: params
    }).then((response) => {
        dataTable.list = response.data.data;
        dataTable.paginator.page = response.data.current_page;
        dataTable.paginator.last = response.data.last_page;
    });
}

function showForm(opt) {
    show_form.value = opt;
    if (opt) {
        selectDefaultInput();
    }
}

function saveForm() {
    if (!form.id) {
        addRecord();
    } else {
        updateRecord();
    }
}

function addRecord() {
    axios.post('api/profiles', form)
        .then((response) => {
            showForm(false);
            fetchData();
            clearForm();
        });
}

function updateRecord() {
    axios.patch(`api/profiles/${form.id}`, form)
        .then((response) => {
            showForm(false);
            fetchData();
            clearForm();
        });
}

function show(id) {
    axios.get(`api/profiles/${id}`)
        .then((response) => {
            _.forEach(response.data, (item, key) => {
                form[key] = item;
            });
            showForm(true);
        });
}

function remove(id) {
    //
}

function clearForm() {
    form.id = 0;
    form.email = '';
}

function selectDefaultInput() {
    // delay to focus default input
    setTimeout(() => {
        def_input.value.select();
    }, 200);
}
</script>

<template>
    <Head title="Perfiles" />

    <BreezeAuthenticatedLayout>
        <div class="py-4">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-gray-200 border-b border-gray-200">

                        <!-- Table -->
                        <div v-show="!show_form" class="row">
                            <div class="col-5">
                                <div>
                                    <button 
                                        type="button" 
                                        class="btn btn-dark btn-sm" 
                                        @click="showForm(true)">
                                        <i class="bi bi-plus-lg"></i> Agregar
                                    </button>
                                </div>
                                
                                <table class="table table-bordered table-sm cls-table">
                                    <thead class="">
                                        <tr>
                                            <th>&nbsp;</th>
                                            <th>Email</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="row in dataTable.list">
                                            <td style="width: 50px;">
                                                <div class="cls-mini-actions">
                                                    <div>
                                                        <i class="bi bi-pencil-square" @click="show(row.id)"></i>
                                                        <i class="bi bi-trash3" @click="remove(row.id)"></i>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ row.email }}</td>
                                        </tr>
                                    </tbody>
                                </table>
    
                                <Paginator :paginator="dataTable.paginator" @change="fetchData"></Paginator>
                            </div>
                            
                        </div>

                        <!-- Form -->
                        <div v-show="show_form" class="row">
                            <div class="col-5">
                                <div>
                                    <form @submit.prevent="saveForm()">
                                        <div class="mb-3">
                                            <label for="form-email" class="form-label">Email:</label>
                                            <input type="email" class="form-control" id="form-email" v-model="form.email" ref="def_input"/>
                                        </div>
                                    </form>
                                </div>

                                <div>
                                    <button 
                                        type="button" 
                                        class="btn btn-success btn-sm"
                                        @click="saveForm()">
                                        Guardar
                                    </button>

                                    <button 
                                        type="button" 
                                        class="btn btn-outline-secondary btn-sm ml-2"
                                        @click="showForm(false)">
                                        Cerrar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </BreezeAuthenticatedLayout>
</template>
