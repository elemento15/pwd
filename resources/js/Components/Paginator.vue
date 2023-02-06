<script setup>
import { defineEmits, defineProps } from 'vue';
import _ from 'lodash';

const props = defineProps(['paginator']);
const emit = defineEmits(['change']);

function changePage(page) {
    if (page != props.paginator.page) {
        emit('change', page);
    }
}
</script>

<template>
    <div class="float-end">
        <nav aria-label="...">
            <ul class="pagination pagination-sm">
                <li class="page-item" @click="changePage(1)">
                    <a class="page-link" href="#" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
                
                <li class="page-item" 
                    :class="{'active': page == paginator.page}"
                    v-for="page in _.range(1, paginator.last + 1)" 
                    @click="changePage(page)">
                    <a class="page-link" href="#">{{ page }}</a>
                </li>

                <li class="page-item" @click="changePage(paginator.last)">
                    <a class="page-link" href="#" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</template>
