<template>
    <v-row dense>
        <v-col cols="12">

            <v-btn :disabled="isloading" rounded depressed @click="saveParteTrabajo" color="success" class="white--text">guardar</v-btn>

            <v-btn v-if="recibo.id" :disabled="isloading" rounded depressed @click="saveFactura('factura', true)" color="orange" class="white--text">convertir a factura</v-btn>

            <v-btn v-if="recibo.id" :disabled="isloading" rounded depressed @click="saveNota('nota', true)" color="light-blue" class="white--text">convertir a nota</v-btn>

        </v-col>
    </v-row>
</template>

<script>
    export default {
        props: ['recibo', 'tipo', 'isloading'],

        methods: {
            saveParteTrabajo() {
                this.$emit('save_parte_trabajo')
            },

            saveFactura() {
                this.$emit('convertir_factura')
            },

            saveNota() {
                this.$emit('convertir_nota')
            }
        }
    }
</script>