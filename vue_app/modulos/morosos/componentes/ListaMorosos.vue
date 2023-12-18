<template>
    <div class="background my-container">

        <v-custom-title text="Lista Pendientes de Pago"></v-custom-title>

        <loader v-if="isloading"></loader>

        <v-row>
            <v-col cols="12" md="4">
                <v-text-field prepend-icon="mdi-account-search" v-model="search" label="Codigo / Nombre"></v-text-field>
            </v-col>
        </v-row>

        <v-data-table :headers="headers" :items="morosos" :search="search" disable-pagination hide-default-footer item-key="id" class="elevation-1">
 
             <template v-slot:item.action="{ item }">
                <router-link :to="{ path: `/guardar-ingreso?identi=${item.nro_factura}&tipo=${item.tipo}` }" class="action-buttons">
                    <v-icon small class="mr-2" color="blue">
                        mdi-plus
                    </v-icon>
                </router-link>
            </template>
        </v-data-table>
    </div>

</template>

<script>
    export default {
        data() {
            return {
                search: '',
                morosos: [],
                headers: [{
                        text: 'Tipo',
                        value: 'tipo',
                    },
                    {
                        text: 'Nro',
                        value: 'nro_factura',
                    },
                    {
                        text: 'Cliente',
                        value: 'cliente'
                    },
                    {
                        text: 'Total',
                        value: 'total'
                    },
                    {
                        text: 'Pagado',
                        value: 'pagado'
                    },
                    {
                        text: 'Deuda',
                        value: 'deuda'
                    },
                    {
                        text: 'Fecha',
                        value: 'fecha'

                    },
                    {
                        text: '+ Ingreso',
                        value: 'action',
                        sortable: false
                    },
                ],
            }
        },
        created() {
            this.getMorosos()
        },
        methods: {
            getMorosos() {
                axios.get(`api/get-morosos/${localStorage.getItem('user_id')}`).then(res => {
                    this.morosos = res.data
                }, err => {
                    this.$toast.error('Error consultando clientes')
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