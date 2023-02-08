<script setup>
import { reactive } from 'vue';

defineEmits(['search']);

const data = reactive({
    search: ''
});
</script>

<template>
    <input 
        type="text" 
        class="form-control form-control-sm" 
        placeholder="Buscar" 
        v-model="data.search" 
        @change="$emit('search', data.search)"
    />
</template>
