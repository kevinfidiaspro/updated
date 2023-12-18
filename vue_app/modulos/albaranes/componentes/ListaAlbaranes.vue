<template>
    <div class="background my-container">

        <v-custom-title text="Lista Albaranes"></v-custom-title>

        <v-btn rounded depressed color="blue" class="mt-5 white--text" :to="{ path: `/guardar-albaran` }">Nuevo</v-btn>

        <loader v-if="isloading"></loader>

        <v-row>
            <v-col cols="12" md="4">
                <v-text-field prepend-icon="mdi-account-search" v-model="search" label="busqueda"></v-text-field>
            </v-col>
        </v-row>

        <v-data-table :headers="headers" :items="albaranes" :search="search" disable-pagination hide-default-footer item-key="id" class="elevation-1">

            <template v-slot:item.imagen="{ item }">
                <a target="_blank" :href="item.path">
                    <v-icon medium color="orange" class="mr-2">
                        mdi-cloud-download
                    </v-icon>
                </a>
            </template>

            <template v-slot:item.action="{ item }">

                <router-link :to="{ path: `/guardar-albaran?id=${item.id}` }" class="action-buttons">
                    <v-icon small class="mr-2" color="blue">
                        mdi-pencil
                    </v-icon>
                </router-link>

                <v-icon @click="deleteAlbaran(item)" small class="mr-2" color="red">
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
                albaranes: [],
                headers: [{
                        text: 'Id',
                        align: 'left',
                        value: 'id',
                    }, {
                        text: 'Fecha',
                        value: 'fecha',
                    },
                    {
                        text: 'Proveedor',
                        value: 'proveedor'
                    },
                    {
                        text: 'Descripción',
                        value: 'descripcion'
                    },
                    {
                        text: 'Imagen',
                        value: 'imagen'
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
            this.getAlbaranes()
        },
        methods: {
            getAlbaranes() {
                axios.get(`api/get-albaranes/${localStorage.getItem('user_id')}`).then(res => {
                    this.albaranes = res.data.albaranes
                }, err => {
                    this.$toast.error('Error consultando albaranes')
                })
            },
            deleteAlbaran(item) {
                axios.get(`api/delete-albaran/${item.id}`).then(res => {
                    this.albaranes.splice(this.albaranes.indexOf(item), 1)
                    this.$toast.sucs('Albaran eliminado')
                }, err => {
                    this.$toast.error('Error eliminando albaran')
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