<script setup>
import { Modal } from 'usemodal-vue3';
import { defineEmits, defineProps } from 'vue';
import { reactive, onBeforeMount } from 'vue';

const props = defineProps([
    'type', // for now only "remove" 
    'visible'
]);
const emit = defineEmits(['accept','cancel']);

const data = reactive({
    title: '',
    text: '',
    width: 250,
    modalClass: '',
    cancelText: '',
    acceptText: '',
});

onBeforeMount(() => {
    if (props.type == 'remove') {
        data.title = 'Eliminar Registro';
        data.text = '¿Desea eliminar el registro?';
        data.modalClass = 'cls-modal-remove';
        data.cancelText = 'Cancelar';
        data.acceptText = 'Eliminar';
    }
});

function sendAccept() {
    emit('accept');
}

function sendCancel() {
    emit('cancel');
}
</script>

<template>
    <Modal 
        :title="data.title"
        :visible="visible"
        :width="data.width"
        :closable="false"
        :modalClass="data.modalClass"
        :cancelButton="{
            text: data.cancelText,
            onclick: () => sendCancel()
        }"
        :okButton="{
            text: data.acceptText,
            loading: true, 
            onclick: () => sendAccept()
        }">
        <div>{{ data.text }}</div>
    </Modal>
</template>
