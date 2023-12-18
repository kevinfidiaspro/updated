<template>

    <v-container>

        <v-custom-title text="Guardar / Editar Proveedor"></v-custom-title>

        <loader v-if="isloading"></loader>

        <v-form class="mt-5">

            <v-row dense>
                <v-col cols="12" md="3">
                    <v-text-field filled :error-messages="errors.errors.nombre ? errors.errors.nombre[0] : null" v-model="proveedor.nombre" label="Nombre" required></v-text-field>
                </v-col>

                <v-col cols="12" md="3">
                    <v-text-field filled :error-messages="errors.errors.email ? errors.errors.email[0] : null" v-model="proveedor.email" label="Email" required></v-text-field>
                </v-col>

                <v-col cols="12" md="3">
                    <v-text-field filled :error-messages="errors.errors.telefono ? errors.errors.telefono[0] : null" v-model="proveedor.telefono" label="Teléfono" :rules="[rules.number_rule]" counter maxlength="9" required>
                    </v-text-field>
                </v-col>
            </v-row>

            <v-row class="mt-3">
                <v-col cols="12">
                    <v-btn rounded depressed @click="saveProveedor" :disabled="isloading" color="success" class="white--text">Guardar</v-btn>
                </v-col>
            </v-row>

        </v-form>

    </v-container>

</template>

<script>
    export default {

        data() {
            return {
                proveedor: {
                    id: '',
                    nombre: '',
                    email: '',
                    telefono: '',
                    user_id: localStorage.getItem('user_id')
                },
                rules: {
                    number_rule: value => /^\d+$/.test(value) || 'Campo numérico'
                }
            }
        },

        created() {
            if (this.$route.query.id) {
                this.getProveedorById(this.$route.query.id)
            }
        },

        methods: {
            getProveedorById(proveedor_id) {
                axios.get(`api/get-proveedor-by-id/${proveedor_id}`).then(res => {
                    this.proveedor = res.data
                }, res => {
                    this.$toast.error('Error consultando proveedor')
                })
            },
            saveProveedor() {
                axios.post('api/save-proveedor', this.proveedor).then(res => {
                    this.$toast.sucs('Proveedor guardado con exito')
                    this.$router.push('/lista-proveedores')
                }, res => {
                    this.$toast.error('Error guardando proveedor')
                })
            },
        },
        computed: {
            isloading() {
                return this.$store.getters.getloading
            },

            errors() {
                return this.$store.getters.geterrors
            }
        }
    }
</script>