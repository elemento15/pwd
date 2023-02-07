<script>
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
import Paginator from '@/Components/Paginator.vue';
import Confirmation from '@/Components/Confirmation.vue';
import { Head } from '@inertiajs/inertia-vue3';
import axios from 'axios';
import { ref } from 'vue';

export default {
    components: {
        Head,
        BreezeAuthenticatedLayout,
        Paginator,
        Confirmation,
    },

    setup() {
        const dataTable = ref({
            list: [],
            paginator: {
                page: 1,
                last: 0,
            }
        });

        const form = ref({
            id: 0,
            name: '',
            url: '',
            comments: '',
            types: []
        });

        const form_type = ref({
            key: false,
            label: '',
            type_id: 0,
        });

        const remove = ref({
            id: false,
            show: false,
        });

        const def_input = ref(null);

        const show_form = ref(false);

        const def_input_type = ref(null);

        const types = ref({
            data: []
        });


        return {
            dataTable,
            form,
            form_type,
            remove,
            def_input,
            show_form,
            def_input_type,
            types,
        }
    },

    mounted() {
        this.fetchData();
        this.fetchTypes();
    },

    methods: {
        fetchData(page = false) {
            let params = {
                page: page || this.dataTable.paginator.page,
                order: {
                    field: 'name'
                }
            };

            axios.get('api/platforms', { 
                params: params
            }).then((response) => {
                this.dataTable.list = response.data.data;
                this.dataTable.paginator.page = response.data.current_page;
                this.dataTable.paginator.last = response.data.last_page;
            });
        },

        showForm(opt) {
            this.show_form = opt;

            // for new platform, add the password label
            if (this.form.types.length == 0) {
                this.form.types.push({
                    label: this.getTypeByCode('password','name'),
                    type_id: this.getTypeByCode('password','id'),
                });
            }

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
            axios.post('api/platforms', this.form)
                .then(() => {
                    this.showForm(false);
                    this.fetchData();
                    this.clearForm();
                });
        },

        updateRecord() {
            axios.patch(`api/platforms/${this.form.id}`, this.form)
                .then(() => {
                    this.showForm(false);
                    this.fetchData();
                    this.clearForm();
                });
        },

        removeRecord() {
            axios.delete(`api/platforms/${this.remove.id}`)
                .then(() => {
                    this.remove.show = false;
                    this.fetchData(1);
                });
        },

        show(id) {
            axios.get(`api/platforms/${id}`)
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
            this.form.name = '';
            this.form.url = '';
            this.form.comments = '';
            this.form.types = [];
        },

        clearFormType() {
            this.form_type.key = false;
            this.form_type.label = '';
            this.form_type.type_id = 0;
        },

        selectDefaultInput() {
            // delay to focus default input
            setTimeout(() => {
                this.$refs.def_input.select();
            }, 100);
        },

        getTypeByCode(code, field) {
            let record = _.find(this.types.data, ['code', code]);
            if (record) {
                return (field) ? record[field] : record;
            } else {
                return false;
            }
        },

        getTypeById(id, field) {
            let record = _.find(this.types.data, ['id', id]);
            if (record) {
                return (field) ? record[field] : record;
            } else {
                return false;
            }
        },

        fetchTypes() {
            axios.get('api/types').then((response) => {
                this.types.data = response.data;
            });
        },

        validateTypeForm() {
            if (this.form_type.label == '') {
                console.log('Ingrese la etiqueta');
                return false;
            }

            if (this.form_type.type_id == '0') {
                console.log('Seleccione el tipo');
                return false;
            }

            return true;
        },

        addType() {
            if (!this.validateTypeForm()) {
                return false;
            }

            this.form.types.push({
                label: this.form_type.label,
                type_id: this.form_type.type_id
            });

            this.clearFormType();
            this.selectDefaultTypeInput();
        },

        editType(index) {
            let type = this.form.types[index];
            this.form_type.key = index;
            this.form_type.label = type.label;
            this.form_type.type_id = type.type_id;
            this.selectDefaultTypeInput();
        },

        updateType() {
            if (!this.validateTypeForm()) {
                return false;
            }

            let index = this.form_type.key;
            this.form.types[index].label = this.form_type.label;
            this.form.types[index].type_id = this.form_type.type_id;
            this.clearFormType();
            this.selectDefaultTypeInput();
        },

        selectDefaultTypeInput() {
            // delay to focus default input
            setTimeout(() => {
                this.$refs.def_input_type.select();
            }, 100);
        },
    }
}
</script>

<template>
    <Head title="Plataformas" />

    <BreezeAuthenticatedLayout>
        <div class="py-4">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-gray-200 border-b border-gray-200">

                        <!-- Table -->
                        <div v-show="!show_form" class="row">
                            <div class="col-md-10 col-lg-8">
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
                                            <th>Nombre</th>
                                            <th>URL</th>
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
                                            <td>{{ row.name }}</td>
                                            <td>{{ row.url }}</td>
                                        </tr>
                                    </tbody>
                                </table>

                                <Paginator :paginator="dataTable.paginator" @change="fetchData"></Paginator>
                            </div>
                        </div>

                        <!-- Form -->
                        <div v-show="show_form" class="row">
                            <div class="col-md-12 col-lg-6">
                                <div>
                                    <form @submit.prevent="saveForm()">
                                        <div class="mb-3">
                                            <label for="form-name" class="form-label">Nombre:</label>
                                            <input type="text" class="form-control" id="form-name" v-model="form.name" ref="def_input"/>
                                        </div>
                                        <div class="mb-3">
                                            <label for="form-url" class="form-label">Url:</label>
                                            <input type="text" class="form-control" id="form-url" v-model="form.url"/>
                                        </div>
                                        <div class="mb-3">
                                            <label for="form-comments" class="form-label">Commentarios:</label>
                                            <textarea class="form-control" id="form-comments" v-model="form.comments" rows="6"></textarea>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <!-- form of Types -->
                            <div class="col-md-12 col-lg-6">
                                <form class="row g-3">
                                    <div class="col">
                                        <label for="form-name-label" class="form-label">Etiqueta:</label>
                                        <input type="text" class="form-control" id="form-type-label" v-model="form_type.label" ref="def_input_type"/>
                                    </div>
                                    <div class="col">
                                        <label for="form-type-id" class="form-label">Tipo:</label>
                                        <select class="form-control" id="form-type-id" v-model="form_type.type_id">
                                            <option value="0">-- Seleccione --</option>
                                            <option :value="opt.id" v-for="opt in types.data">{{ opt.name }}</option>
                                        </select>
                                    </div>
                                    <div class="col-3">
                                        <label class="form-label">&nbsp;</label>
                                        <div v-show="this.form_type.key === false">
                                            <button type="button" class="btn btn-sm btn-dark" @click="addType()">
                                                <i class="bi bi-plus-lg"></i>&nbsp;
                                            </button>
                                        </div>
                                        <div v-show="this.form_type.key !== false">
                                            <button type="button" class="btn btn-sm btn-dark mx-1" @click="updateType()">
                                                <i class="bi bi-check-lg"></i>&nbsp;
                                            </button>
                                            <button type="button" class="btn btn-sm btn-dark" @click="clearFormType()">
                                                <i class="bi bi-x-lg"></i>&nbsp;
                                            </button>
                                        </div>
                                    </div>
                                </form>

                                <!-- list of Types -->
                                <div>
                                    <table class="table table-bordered table-sm cls-table">
                                        <thead class="">
                                            <tr>
                                                <th>&nbsp;</th>
                                                <th>Etiqueta</th>
                                                <th>Tipo</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr 
                                                v-for="(item, key) in form.types" 
                                                v-show="!item._deleted"
                                                :class="{'text-danger': item.deleted}">
                                                <td style="width: 50px;">
                                                    <div class="cls-mini-actions">
                                                        <div>
                                                            <i class="bi bi-pencil-square" @click="editType(key)"></i>
                                                            <i class="bi bi-trash3" v-show="!item.deleted" @click="item.deleted = true"></i>
                                                            <i class="bi bi-plus-square" v-show="item.deleted" @click="item.deleted = false"></i>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>{{ item.label }}</td>
                                                <td>{{ getTypeById(item.type_id, 'name') }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
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
