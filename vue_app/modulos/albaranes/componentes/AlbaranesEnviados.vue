<template>
    <div class="background my-container">

        <v-custom-title text="Albaranes Enviados"></v-custom-title>

        <v-btn rounded depressed color="blue" class="mt-5 white--text" :to="'/form-albaranes-enviados/' + userId">Nuevo</v-btn>

        <loader v-if="isloading"></loader>

        <v-row>
            <v-col cols="12" md="4">
                <v-text-field prepend-icon="mdi-account-search" v-model="search" label="busqueda"></v-text-field>
            </v-col>
        </v-row>

        <v-data-table :headers="headers" :items="albaranesEnviados" :search="search" disable-pagination hide-default-footer item-key="id" class="elevation-1">

            <template v-slot:item.url="{ item }">
                <a target="_blank" :href="'/storage/albaranes/enviados/' +item.url">
                    <v-icon medium color="orange" class="mr-2">
                        mdi-cloud-download
                    </v-icon>
                </a>
            </template>

            <template v-slot:item.action="{ item }">

                <router-link  :to="'/form-albaranes-enviados-update/'+ userId + '/' + item.id" class="action-buttons">
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
                albaranesEnviados: [],
                headers: [
                    // {
                    //     text: 'Id',
                    //     align: 'left',
                    //     value: 'id',
                    // }, 
                    {
                        text: 'Nro Albaran',
                        value: 'nro_factura',
                    },
                    {
                        text: 'Cliente',
                        value: 'cliente.nombre'
                    },
                   
                    {
                        text: 'Total',
                        value: 'importe',
                    },
                    {
                        text: 'PDF',
                        value: 'url'
                    },
                    {
                        text: 'Contabilizado',
                        value: 'contabilizado'
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
                axios.get(`api/get-albaranes-enviados/${localStorage.getItem('user_id')}`).then(res => {
                    this.albaranesEnviados = res.data.enviados
                }, err => {
                    this.$toast.error('Error consultando albaranes')
                })
            },
            deleteAlbaran(item) {
                axios.post(`api/delete-albaran-enviados/${item.id}`).then(res => {
                    this.albaranesEnviados.splice(this.albaranesEnviados.indexOf(item), 1)
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
            userId(){
                return localStorage.user_id
            }
        }
    }
</script>