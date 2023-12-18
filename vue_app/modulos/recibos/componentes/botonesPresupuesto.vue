<template>
    <v-row dense>
        <v-col cols="12">
            <v-btn :disabled="isloading" rounded depressed @click="savePresupuesto" color="success" class="white--text">guardar</v-btn>

            <v-btn v-if="recibo.factura_url == null && recibo.id != null" :disabled="isloading" rounded depressed @click="saveFactura('factura', true)" color="orange" class="white--text">convertir a factura</v-btn>

            <v-btn v-if="recibo.nota == null && recibo.id != null" :disabled="isloading" rounded depressed @click="saveNota('nota', true)" color="light-blue" class="white--text">convertir a nota</v-btn>

            <v-btn :disabled="isloading" v-if="recibo.presupuesto_url" rounded depressed target="_blank" :href="`storage/recibos/${recibo.presupuesto_url}`" class="white--text" color="blue">Ver PDF
            </v-btn>
        </v-col>
         
    </v-row>

</template>

<script>
   
    export default {

        props: ['recibo', 'tipo', 'isloading'],
        data(){
            return {
               
            }
        },
        methods: {
           
            savePresupuesto() {
                this.$emit('save_presupuesto')
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