<script>
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
import Paginator from '@/Components/Paginator.vue';
import Confirmation from '@/Components/Confirmation.vue';
import SearchTable from '@/Components/SearchTable.vue';
import { Head } from '@inertiajs/inertia-vue3';
import { ref } from 'vue';

export default {
    components: {
        Head,
        BreezeAuthenticatedLayout,
        Paginator,
        Confirmation,
        SearchTable,
    },

    setup() {
        const dataTable = ref({
            list: [],
            paginator: {
                page: 1,
                last: 0,
            },
            search: '',
        });

        const form = ref({
            id: 0,
            email: ''
        });

        const remove = ref({
            id: false,
            show: false,
        });

        const def_input = ref(null);

        const show_form = ref(false);


        return {
            dataTable,
            form,
            remove,
            def_input,
            show_form,
        }
    },

    mounted() {
        this.fetchData();
    },

    methods: {
        fetchData(page = false) {
            let params = {
                page: page || this.dataTable.paginator.page,
                search: this.dataTable.search || '',
                order: {
                    field: 'email'
                }
            };

            axios.get('api/profiles', { 
                params: params
            }).then((response) => {
                this.dataTable.list = response.data.data;
                this.dataTable.paginator.page = response.data.current_page;
                this.dataTable.paginator.last = response.data.last_page;
            });
        },
        
        showForm(opt) {
            this.show_form = opt;
            if (opt) {
                this.selectDefaultInput();
            } else {
                this.clearForm();
            }
        },

        saveForm() {
            if (!this.form.id) {
                this.addRecord();
            } else {
                this.updateRecord();
            }
        },

        addRecord() {
            axios.post('api/profiles', this.form)
                .then((response) => {
                    this.showForm(false);
                    this.fetchData();
                    this.clearForm();
                });
        },

        updateRecord() {
            axios.patch(`api/profiles/${this.form.id}`, this.form)
                .then((response) => {
                    this.showForm(false);
                    this.fetchData();
                    this.clearForm();
                });
        },

        removeRecord() {
            axios.delete(`api/profiles/${this.remove.id}`)
                .then((response) => {
                    this.remove.show = false;
                    this.fetchData(1);
                });
        },

        show(id) {
            axios.get(`api/profiles/${id}`)
                .then((response) => {
                    _.forEach(response.data, (item, key) => {
                        this.form[key] = item;
                    });
                    this.showForm(true);
                });
        },

        confirmRemove(id) {
            this.remove.id = id;
            this.remove.show = true;
        },

        clearForm() {
            this.form.id = 0;
            this.form.email = '';
        },

        search(searchText) {
            this.dataTable.search = searchText;
            this.fetchData(1);
        },

        selectDefaultInput() {
            // delay to focus default input
            setTimeout(() => {
                this.$refs.def_input.select();
            }, 50);
        }
    }
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
                            <div class="col-md-8 col-lg-6">
                                <div class="row">
                                    <div class="col-6">
                                        <button 
                                            type="button" 
                                            class="btn btn-dark btn-sm" 
                                            @click="showForm(true)">
                                            <i class="bi bi-plus-lg"></i> Agregar
                                        </button>
                                    </div>
                                    <div class="col-6">
                                        <SearchTable @search="search"/>
                                    </div>
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
                                                        <i class="bi bi-trash3" @click="confirmRemove(row.id)"></i>
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
                            <div class="col-md-6 col-lg-4">
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

                        <!-- Confirm delete modal -->
                        <Confirmation 
                            type="remove" 
                            :visible="remove.show"
                            @cancel="remove.show = false"
                            @accept="removeRecord()">
                        </Confirmation>
                    </div>
                </div>
            </div>
        </div>
    </BreezeAuthenticatedLayout>
</template>
