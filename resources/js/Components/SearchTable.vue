<script setup>
import { reactive, defineEmits } from 'vue';

const emit = defineEmits(['search']);

const data = reactive({
    search: ''
});

function clean() {
    data.search = '';
    emit('search', '');
}
</script>

<template>
    <div class="input-group input-group-sm">
        <input 
            type="text" 
            class="form-control" 
            placeholder="Buscar" 
            v-model="data.search" 
            @change="emit('search', data.search)"
        />
        <button 
            type="button" 
            class="btn btn-outline-secondary" 
            @click="emit('search', data.search)">
            <i class="bi bi-search" title="Buscar"></i>
        </button>
        <button 
            type="button" 
            class="btn btn-outline-secondary" 
            @click="clean()">
            <i class="bi bi-x-lg" title="Limpiar"></i>
        </button>
    </div>
</template>
