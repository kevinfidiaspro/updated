<template>
    <div class="background my-container">

        <v-custom-title text="Lista Parte Trabajo"></v-custom-title>

        <v-btn rounded depressed color="blue" class="mt-5 white--text" :to="{ path: `/guardar-recibo?tipo=parte-trabajo` }">Nuevo</v-btn>

        <loader v-if="isloading"></loader>

        <v-row>
            <v-col cols="12" md="4">
                <v-text-field prepend-icon="mdi-account-search" v-model="search" label="busqueda"></v-text-field>
            </v-col>
        </v-row>

        <v-data-table :headers="headers" :items="items" :search="search" disable-pagination hide-default-footer item-key="id" class="elevation-1">

            <template v-slot:item.archivo="{ item }">

                <a v-if="item.nombre_nota != null" target="_blank" :href="item.nota_path">
                    <v-icon medium color="blue" class="mr-2">
                        mdi-file-pdf-outline
                    </v-icon>
                </a>

            </template>

            <template v-slot:item.action="{ item }">

                <router-link :to="{ path: `/guardar-recibo?id=${item.id}&tipo=parte-trabajo` }" class="action-buttons">
                    <v-icon small class="mr-2" color="blue">
                        mdi-pencil
                    </v-icon>
                </router-link>

                <v-icon @click="deleteParteTrabajo(item)" small class="mr-2" color="red">
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
                items: [],
                headers: [{
                        text: 'Fecha',
                        value: 'fecha',
                    },
                    {
                        text: 'Cliente',
                        value: 'cliente'
                    },
                    {
                        text: 'Presupuesto',
                        value: 'nro_presupuesto',
                    },
                    {
                        text: 'Total Presupuesto',
                        value: 'total_presupuesto',
                    },
                    {
                        text: 'Parcial',
                        value: 'total'
                    },
                    {
                        text: 'beneficio',
                        value: 'beneficio'
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
            this.getPartesTrabajo()
        },
        methods: {
            getPartesTrabajo() {
                axios.get(`api/get-parte-trabajo/${localStorage.getItem('user_id')}`).then(res => {
                    this.items = res.data
                }, err => {
                    this.$toast.error('Error consultando items')
                })
            },
            deleteParteTrabajo(item) {
                axios.get(`api/delete-parte-trabajo/${item.id}`).then(res => {
                    let index = this.items.findIndex(e => e.id == item.id)

                    if (index > -1) {
                        this.items.splice(index, 1)

                        this.$toast.sucs('Parte eliminado')
                    }

                }, err => {
                    this.$toast.error('Error eliminando parte')
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