<script>
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
import Paginator from '@/Components/Paginator.vue';
import Confirmation from '@/Components/Confirmation.vue';
import SearchTable from '@/Components/SearchTable.vue';
import Loader from '@/Components/Loader.vue';
import { Head } from '@inertiajs/inertia-vue3';
import axios from 'axios';
import { ref } from 'vue';

export default {
    components: {
        Head,
        BreezeAuthenticatedLayout,
        Paginator,
        Confirmation,
        SearchTable,
        Loader,
    },

    setup() {
        const dataTable = ref({
            list: [],
            paginator: {
                page: 1,
                last: 0,
            },
            search: ''
        });

        const form = ref({
            id: 0,
            platform_id: 0,
            profile_id: 0,
            comments: '',
            profile: {},
            platform: {},
            properties: [],
        });

        const remove = ref({
            id: false,
            show: false,
        });
        
        const lists = ref({
            types: [],
            profiles: [],
            platforms: [],
        });

        const def_input = ref(null);

        const show_form = ref(false);

        const isLoading = ref(false);

        return {
            dataTable,
            form,
            remove,
            def_input,
            show_form,
            lists,
            isLoading,
        }
    },

    mounted() {
        this.fetchData();

        this.fetchProfiles();
        this.fetchPlatforms();
        this.fetchTypes();
    },

    methods: {
        fetchData(page = false) {
            let params = {
                page: page || this.dataTable.paginator.page,
                search: this.dataTable.search || '',
                order: {
                    field: 'created_at'
                },
            };

            this.isLoading = true;

            axios.get('api/credentials', { 
                params: params
            }).then((response) => {
                this.dataTable.list = response.data.data;
                this.dataTable.paginator.page = response.data.current_page;
                this.dataTable.paginator.last = response.data.last_page;
                this.isLoading = false;
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
            this.isLoading = true;
            axios.post('api/credentials', this.form)
                .then((response) => {
                    this.show(response.data.id);
                    this.fetchData();
                });
        },

        updateRecord() {
            this.isLoading = true;
            axios.patch(`api/credentials/${this.form.id}`, this.form)
                .then(() => {
                    this.showForm(false);
                    this.fetchData();
                    this.clearForm();
                });
        },

        removeRecord() {
            this.isLoading = true;
            axios.delete(`api/credentials/${this.remove.id}`)
                .then(() => {
                    this.remove.show = false;
                    this.fetchData(1);
                });
        },

        show(id) {
            this.isLoading = true;
            axios.get(`api/credentials/${id}`)
                .then((response) => {
                    _.forEach(response.data, (item, key) => {
                        this.form[key] = item;
                    });
                    this.setCredentialProperties();
                    this.showForm(true);
                    this.isLoading = false;
                });
        },

        setCredentialProperties() {
            _.forEach(this.form.platform.types, (item) => {
                item._show = false;
                item.answer = this.getAnswer(item.id);
            })
        },

        getAnswer(id) {
            let record = _.find(this.form.properties, ['platform_type_id', id]);
            return (record) ? record.answer : '';
        },

        confirmRemove(id) {
            this.remove.id = id;
            this.remove.show = true;
        },

        clearForm() {
            this.form.id = 0;
            this.form.platform_id = 0;
            this.form.profile_id = 0;
            this.form.comments = '';
            this.form.profile = {};
            this.form.platform = {};
            this.form.properties = [];
        },

        search(searchText) {
            this.dataTable.search = searchText;
            this.fetchData(1);
        },

        selectDefaultInput() {
            // ---
        },

        fetchTypes() {
            axios.get('api/types').then((response) => {
                this.lists.types = response.data;
            });
        },

        fetchProfiles() {
            axios.get('api/profiles').then((response) => {
                this.lists.profiles = response.data;
            });
        },

        fetchPlatforms() {
            axios.get('api/platforms').then((response) => {
                this.lists.platforms = response.data;
            });
        },

        copyToClipBoard (field) {
            if (field.answer) {
                var dummy = document.createElement("textarea");
                // to avoid breaking orgain page when copying more words
                // cant copy when adding below this code
                // dummy.style.display = 'none'
                document.body.appendChild(dummy);
                // Be careful if you use texarea. setAttribute('value', value), 
                // which works with "input" does not work with "textarea". – Eduard
                dummy.value = field.answer;
                dummy.select();
                document.execCommand("copy");
                document.body.removeChild(dummy);
    
                field._copied = true;
                setTimeout(() => {
                    field._copied = false;
                }, 500);
            }
        }
    }
}
</script>

<template>
    <Head title="Credenciales" />

    <BreezeAuthenticatedLayout>
        <div class="py-4">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-gray-200 border-b border-gray-200 vld-parent">

                        <Loader :active="isLoading"></Loader>

                        <!-- Table -->
                        <div v-show="!show_form" class="row">
                            <div class="col-lg-8">
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
                                            <th>Plataforma</th>
                                            <th>Perfil</th>
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
                                            <td>{{ row.platform.name }}</td>
                                            <td>{{ row.profile.email }}</td>
                                        </tr>
                                    </tbody>
                                </table>

                                <Paginator :paginator="dataTable.paginator" @change="fetchData"></Paginator>
                            </div>
                        </div>

                        <!-- Form -->
                        <div v-show="show_form" class="row">
                            <div class="col-md-6 col-lg-5">
                                <div>
                                    <form @submit.prevent="saveForm()">
                                        <!-- selects for new form-->
                                        <div class="mb-3" v-show="!form.id">
                                            <label for="form-platform" class="form-label">Plataforma:</label>
                                            <select class="form-control" id="form-platform" v-model="form.platform_id">
                                                <option value="0">-- Seleccione --</option>
                                                <option :value="opt.id" v-for="opt in lists.platforms">{{ opt.name }}</option>
                                            </select>
                                        </div>

                                        <div class="mb-3" v-show="!form.id">
                                            <label for="form-profile" class="form-label">Perfil:</label>
                                            <select class="form-control" id="form-profile" v-model="form.profile_id">
                                                <option value="0">-- Seleccione --</option>
                                                <option :value="opt.id" v-for="opt in lists.profiles">{{ opt.email }}</option>
                                            </select>
                                        </div>
                                        <!-- end selects for new form-->
                                        
                                        <!-- readonly for edit form -->
                                        <div class="mb-3" v-show="form.id">
                                            <label class="form-label">Plataforma:</label>
                                            <input class="form-control" type="text" :value="form.platform.name" aria-label="readonly input" readonly/>
                                        </div>

                                        <div class="mb-3" v-show="form.id">
                                            <label class="form-label">Perfil:</label>
                                            <input class="form-control" type="text" :value="form.profile.email" aria-label="readonly input" readonly/>
                                        </div>
                                        <!-- end readonly for edit form -->

                                        <div class="mb-3">
                                            <label for="form-comments" class="form-label">Comentarios:</label>
                                            <textarea class="form-control" id="form-comments" v-model="form.comments" rows="6"></textarea>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <div class="col-md-5 col-lg-4">
                                <div class="mb-3" v-for="field in form.platform.types">
                                    <label for="" class="form-label">{{ field.label }}:</label>

                                    <i class="bi bi-eye-fill cls-pointer ml-2" 
                                       v-show="field.type.code == 'password' && !field._show" 
                                       @click="field._show = true"></i>

                                    <i class="bi bi-eye-slash-fill cls-pointer ml-2" 
                                       v-show="field.type.code == 'password' && field._show"
                                       @click="field._show = false"></i>
                                    
                                    <i class="bi bi-clipboard cls-pointer ml-2" 
                                       v-show="field.type.code == 'password' && !field._copied"
                                       @click="copyToClipBoard(field)"></i>

                                    <i class="bi bi-clipboard-check text-success cls-pointer ml-2" 
                                       v-show="field.type.code == 'password' && field._copied"></i>

                                    <input 
                                        :type="field.type.code" 
                                        class="form-control" 
                                        v-model="field.answer"
                                        v-show="field.type.code == 'password' && !field._show || field.type.code != 'password'"/>
                                    
                                    <input 
                                        type="text" 
                                        class="form-control" 
                                        v-model="field.answer" 
                                        v-show="field.type.code == 'password' && field._show"/>
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
