<template>
    <div class="background my-container">

        <v-custom-title text="Lista Presupuestos"></v-custom-title>

        <v-btn rounded depressed color="blue" class="mt-5 white--text" :to="{ path: `/guardar-recibo?tipo=presupuesto` }">Nuevo</v-btn>

        <loader v-if="isloading"></loader>

        <v-row>
            <v-col cols="12" md="4">
                <v-text-field prepend-icon="mdi-account-search" v-model="search" label="busqueda"></v-text-field>
            </v-col>
        </v-row>

        <v-data-table :headers="headers" :items="recibos" :search="search" disable-pagination hide-default-footer item-key="id" class="elevation-1">

            <template v-slot:item.archivo="{ item }">

                <a v-if="item.nombre_presupuesto != 'false'" target="_blank" :href="item.presupuesto_path">
                    <v-icon medium color="orange" class="mr-2">
                        mdi-file-pdf-outline
                    </v-icon>
                </a>

                <a v-if="item.nombre_factura != null" target="_blank" :href="item.factura_path">
                    <v-icon medium color="blue" class="mr-2">
                        mdi-file-pdf-outline
                    </v-icon>
                </a>

            </template>

            <template v-slot:item.action="{ item }">

                <router-link :to="{ path: `/guardar-recibo?id=${item.id}&tipo=presupuesto` }" class="action-buttons">
                    <v-icon small class="mr-2" color="blue">
                        mdi-pencil
                    </v-icon>
                </router-link>

                <v-icon @click="deleteRecibo(item)" small class="mr-2" color="red">
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
                recibos: [],
                headers: [{
                        text: 'Nro.Presupuesto',
                        align: 'left',
                        value: 'nro_presupuesto',
                    },
                    {
                        text: 'Fecha',
                        value: 'fecha',
                    },
                    {
                        text: 'Cliente',
                        value: 'cliente'
                    },
                    {
                        text: 'total',
                        value: 'total'
                    },
                    {
                        text: 'PDF',
                        value: 'archivo'
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
            this.getRecibos()
        },
        methods: {
            getRecibos() {
                axios.get(`api/get-recibos/${localStorage.getItem('user_id')}`).then(res => {
                    this.recibos = res.data
                }, err => {
                    this.$toast.error('Error consultando presupuestos')
                })
            },
            deleteRecibo(item) {
                axios.get(`api/delete-recibo/${item.id}`).then(res => {
                    this.recibos.splice(this.recibos.indexOf(item), 1)
                    this.$toast.sucs('Presupuesto eliminado')
                }, err => {
                    this.$toast.error('Error eliminando presupuesto')
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