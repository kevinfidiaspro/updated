<template>
    <v-card class="pa-3 ma-3">
        <v-toolbar flat color="#1d2735" dark>
            <v-icon class="white--text" style="font-size: 45px">
                mdi-format-list-bulleted-type
            </v-icon>
            <pre><v-toolbar-title><h2>Tipos de Gasto</h2></v-toolbar-title></pre>
        </v-toolbar>
        <loader v-if="isloading"></loader>
        <v-container fluid>
            <v-row>
                <v-col cols="12" md="4">
                    <v-text-field v-model="tipo_gasto.nombre" label="Nombre" required></v-text-field>
                </v-col>

                <v-col cols="12" md="4">
                    <v-tooltip top>
                        <template v-slot:activator="{ on, attrs }">
                            <v-btn
                                fab
                                @click="saveTiposGasto()"
                                :loading="isloading"
                                :disabled="isloading"
                                color="success"
                                class="mx-2"
                                v-bind="attrs"
                                v-on="on"
                            >
                                <v-icon class="white--text">mdi-content-save-all</v-icon>
                            </v-btn>
                        </template>
                        <span>Guardar Tipo de Gasto</span>
                    </v-tooltip>
                </v-col>
            </v-row>
        </v-container>
        <v-row>
            <v-col cols="12" md="4">
                <v-text-field prepend-icon="mdi-account-search" v-model="search" label="Codigo / Nombre"></v-text-field>
            </v-col>
        </v-row>
        <v-data-table
            :headers="headers"
            :items="tipos_gasto"
            :search="search"
            :items-per-page="10"
            item-key="id"
            class="elevation-1">
            <template v-slot:item.action="{ item }">
                <v-icon
                    small
                    class="mr-2"
                    @click="setTiposGasto(item)"
                    color="#1d2735"
                    style="font-size: 25px"
                    title="EDITAR"
                >mdi-pencil-outline</v-icon>
                <v-icon @click="openModal(item)" small class="mr-2" color="red" style="font-size: 25px;" title="BORRAR">
                    mdi-trash-can
                </v-icon>
            </template>
        </v-data-table>
        <v-dialog v-model="dialog" max-width="500px">
            <v-card>
                <v-card-title class="text-h5 aviso" style="justify-content: center; background:#1d2735; color:white">
                    Aviso
                </v-card-title>
                <v-card-text style="text-align:center">
                    <h2>¿Estás seguro que deseas eliminar?</h2>
                </v-card-text>
                <v-card-actions class="pt-3">
                    <v-spacer></v-spacer>
                    <v-btn
                        color="error"
                        large
                        @click="dialog = false;selectedItem={}">Cancelar</v-btn>
                    <v-btn
                        color="success"
                        large
                        @click="deleteTiposGasto()">Confirmar</v-btn>
                    <v-spacer></v-spacer>
                </v-card-actions>
            </v-card>
        </v-dialog>
        <v-tooltip right>
            <template v-slot:activator="{ on, attrs }">
                <v-btn fab :to="'/contabilidad'" :loading="isloading" :disabled="isloading" color="blue" class="mx-3 mt-2" v-bind="attrs" v-on="on">
                    <v-icon class="white--text">mdi-arrow-left-bold-outline</v-icon>
                </v-btn>
            </template>
            <span>Volver</span>
        </v-tooltip>
    </v-card>
    <!--<div class="background my-container">

        <v-custom-title text="Tipos de Gasto"></v-custom-title>

        <v-container fluid>
            <v-row>
                <v-col cols="12" md="4">
                    <v-text-field v-model="tipo_gasto.nombre" label="Nombre" required></v-text-field>
                </v-col>

                <v-col cols="12" md="4">
                    <v-btn rounded depressed color="blue" class="mt-5 white--text" @click="saveTiposGasto()">Añadir</v-btn>
                </v-col>
            </v-row>
        </v-container>

        <loader v-if="isloading"></loader>

         <v-row>
            <v-col cols="12" md="4">
                <v-text-field prepend-icon="mdi-account-search" v-model="search" label="Codigo / Nombre"></v-text-field>
            </v-col>
         </v-row>

        <v-data-table
            :headers="headers"
            :items="tipos_gasto"
            :search="search"
            :items-per-page="10"
            item-key="id"
            class="elevation-1">

            <template v-slot:item.action="{ item }">
                <v-icon @click="setTiposGasto(item)" small class="mr-2" color="blue">
                    mdi-pencil
                </v-icon>
                <v-icon @click="deleteTiposGasto(item)" small class="mr-2" color="red">
                    mdi-trash-can
                </v-icon>
            </template>
        </v-data-table>
    </div> -->
</template>

<script>
    export default {
        data() {
            return {
                search: '',
                tipo_gasto:{
                    id:'',
                    nombre:'',
                    user_id: localStorage.getItem('user_id')
                },
                tipos_gasto: [],
                selectedItem: 0,
                dialog: false,
                headers: [{
                        text: 'Código',
                        align: 'left',
                        value: 'id',
                    },
                    {
                        text: 'Nombre',
                        value: 'nombre',
                        filterable: false,
                    },
                    {
                        text: 'Acciones',
                        value: 'action',
                        sortable: false,
                    },
                ],
            }
        },
        mounted() {
             this.getTiposGasto()

        },
        methods: {

            getTiposGasto(){
                axios.get(`api/get-tipos-gasto/${localStorage.getItem('user_id')}`).then(res => {
                    this.tipos_gasto = res.data

                }, res => {
                    this.$toast.error('Error consultando tipos de gasto')
                })
            },
            saveTiposGasto(){
                 axios.post('api/save-tipos-gasto', this.tipo_gasto).then(res => {
                    this.tipo_gasto={
                        id:'',
                        nombre:''
                    }
                    this.getTiposGasto()
                }, res => {
                    this.$toast.error('Error Guardando tipo de gasto')
                })
            },
            setTiposGasto(item) {
                this.tipo_gasto = item
            },
            deleteTiposGasto() {
                this.dialog = false
                axios.get(`api/delete-tipos-gasto/${this.tipos_gasto[this.selectedItem].id}`).then(res => {
                    this.tipos_gasto.splice(this.selectedItem, 1);
                    this.$toast.sucs('Gasto eliminado con exito')
                }, err => {
                    this.$toast.error('Error eliminando tipo de gasto')
                })
            },
            openModal(item){
                this.selectedItem = this.tipos_gasto.indexOf(item);
                this.dialog = true;
            },
        },
        computed: {
            isloading: function() {
                return this.$store.getters.getloading
            },
        }

    }
</script>
