<template>
    <h2 class="custom-title">{{text}}</h2>
</template>

<script>
    export default {
        name: 'v-custom-title',
        props: ['text']
    }
</script>

<style media="screen">
    .custom-title {
        font-weight: lighter;
        color: #757575;
        text-transform: uppercase;
        font-size: 1.2rem;
    }
</style>