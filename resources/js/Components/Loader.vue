<script setup>
import Loading from 'vue3-loading-overlay';
import { defineProps } from 'vue';

const props = defineProps(['active']);
</script>

<template>
    <!-- remember to add class "vld-parent" to the parent container -->
    <Loading :active="active" 
        loader="dots" 
        :is-full-page="false"
        background-color="#000"
        :opacity="0.2"/>
</template>
