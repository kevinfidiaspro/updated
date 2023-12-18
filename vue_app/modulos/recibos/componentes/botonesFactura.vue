<template>
    <v-row dense>
        <v-col cols="12">
            <v-btn :disabled="isloading" rounded depressed @click="saveRecibo" color="success" class="white--text">Guardar</v-btn>
            <v-btn :disabled="isloading" v-if="recibo.factura_url" rounded depressed target="_blank" :href="`storage/recibos/${recibo.factura_url}`" class="white--text" color="orange">Ver PDF</v-btn>
        </v-col>
    </v-row>
</template>

<script>
    export default {
        props: ['recibo', 'tipo', 'isloading'],

        methods: {
            saveRecibo() {
                this.$emit('save_factura')
            },
        }
    }
</script>