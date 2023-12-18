<template>
    <div class="background my-container">

        <v-custom-title text="Lista Proveedores"></v-custom-title>

        <v-btn rounded depressed color="blue" class="mt-5 white--text" :to="{ path: `/guardar-proveedor` }">Nuevo</v-btn>

        <loader v-if="isloading"></loader>

        <v-row>
            <v-col cols="12" md="4">
                <v-text-field prepend-icon="mdi-account-search" v-model="search" label="busqueda"></v-text-field>
            </v-col>
        </v-row>

        <v-data-table :headers="headers" :items="proveedores" :search="search" disable-pagination hide-default-footer item-key="id" class="elevation-1">

            <template v-slot:item.action="{ item }">

                <router-link :to="{ path: `/guardar-proveedor?id=${item.id}` }" class="action-buttons">
                    <v-icon small class="mr-2" color="blue">
                        mdi-pencil
                    </v-icon>
                </router-link>

                <v-icon @click="deleteProveedor(item)" small class="mr-2" color="red">
                    mdi-trash-can
                </v-icon>

            </template>

        </v-data-table>

    </div>

</template>

<script>
    export default {
        data() {
            return {
                search: '',
                proveedores: [],
                headers: [{
                        text: 'Id',
                        value: 'id',
                    },
                    {
                        text: 'Nombre',
                        value: 'nombre'
                    },
                    {
                        text: 'Email',
                        value: 'email'
                    },
                    {
                        text: 'Teléfono',
                        value: 'telefono'
                    },
                    {
                        text: 'Fecha',
                        value: 'created_at'
                    },
                    {
                        text: 'Acciones',
                        value: 'action',
                        sortable: false
                    },
                ],
            }
        },
        created() {
            this.getProveedores()
        },
        methods: {
            getProveedores() {
                axios.get(`api/get-proveedores/${localStorage.getItem('user_id')}`).then(res => {
                    this.proveedores = res.data
                }, err => {
                    this.$toast.error('Error consultando proveedores')
                })
            },
            deleteProveedor(item) {
                axios.get(`api/delete-proveedor/${item.id}`).then(res => {
                    this.proveedores.splice(this.proveedores.indexOf(item), 1)
                    this.$toast.sucs('Proveedor eliminado')
                }, err => {
                    this.$toast.error('Error eliminando proveedor')
                })
            }
        },
        computed: {
            isloading: function() {
                return this.$store.getters.getloading
            },
        }
    }
</script>