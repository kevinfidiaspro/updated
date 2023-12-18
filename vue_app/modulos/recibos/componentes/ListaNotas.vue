<template>
    <div class="background my-container">

        <v-custom-title text="Lista Notas"></v-custom-title>

        <v-btn rounded depressed color="blue" class="mt-5 white--text" :to="{ path: `/guardar-recibo?tipo=nota` }">Nuevo</v-btn>

        <loader v-if="isloading"></loader>

        <v-row>
            <v-col cols="12" md="4">
                <v-text-field prepend-icon="mdi-account-search" v-model="search" label="busqueda"></v-text-field>
            </v-col>
        </v-row>

        <v-data-table :headers="headers" :items="notas" :search="search" disable-pagination hide-default-footer item-key="id" class="elevation-1">

            <template v-slot:item.archivo="{ item }">

                <a v-if="item.nombre_nota != null" target="_blank" :href="item.nota_path">
                    <v-icon medium color="blue" class="mr-2">
                        mdi-file-pdf-outline
                    </v-icon>
                </a>

            </template>

            <template v-slot:item.action="{ item }">

                <router-link :to="{ path: `/guardar-recibo?id=${item.id}&tipo=nota` }" class="action-buttons">
                    <v-icon small class="mr-2" color="blue">
                        mdi-pencil
                    </v-icon>
                </router-link>

                <v-icon @click="deleteNota(item)" small class="mr-2" color="red">
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
                notas: [],
                headers: [{
                        text: 'Nro.Nota',
                        align: 'left',
                        value: 'nro_nota',
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
            this.getNotas()
        },
        methods: {
            getNotas() {
                axios.get(`api/get-notas/${localStorage.getItem('user_id')}`).then(res => {
                    this.notas = res.data
                }, err => {
                    this.$toast.error('Error consultando notas')
                })
            },
            deleteNota(item) {
                axios.get(`api/delete-nota/${item.id}`).then(res => {
                    this.notas.splice(this.notas.indexOf(item), 1)
                    this.$toast.sucs('Nota eliminada')
                }, err => {
                    this.$toast.error('Error eliminando nota')
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