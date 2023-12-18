<template>
    <v-card class="pa-3 ma-3">
    <v-toolbar flat color="#1d2735" dark>
      <v-icon class="white--text" style="font-size: 45px"
        >mdi-calendar-clock</v-icon
      >
      <pre><v-toolbar-title><h2>Reservas de Sala</h2></v-toolbar-title></pre>
    </v-toolbar>
    <loader v-if="isloading"></loader>
    <v-card flat>
        <v-row dense>
            <v-col cols="3" md="3" class="pt-3 pl-0 pb-0">
                <v-select
                  dense
                  outlined
                  v-model="reserva.desde"
                  prepend-icon="mdi-calendar"
                  :items="desde_horas"
                  item-text="hora"
                  item-value="id"
                  label="Desde"
                  @change="getHastaHoras(reserva.desde)"
                >
                </v-select>
            </v-col>
            <v-col cols="3" md="3" class="pt-3 pl-0 pb-0">
                <v-select
                  dense
                  outlined
                  v-model="reserva.hasta"
                  prepend-icon="mdi-calendar"
                  :items="hasta_horas"
                  item-text="hora"
                  item-value="id"
                  label="Hasta"
                >
                </v-select>
            </v-col>
            <v-col cols="3" md="3" class="pt-3 pl-0 pb-0">
                <v-menu
                    ref="menu"
                    v-model="menu"
                    :close-on-content-click="false"
                    :return-value.sync="reserva.fecha"
                    transition="scale-transition"
                    offset-y
                    min-width="290px"
                >
                    <template v-slot:activator="{ on, attrs }">
                        <v-text-field
                            v-model="reserva.fecha"
                            label="Fecha de la reserva"
                            append-icon="mdi-calendar"
                            v-bind="attrs"
                            v-on="on"
                        >
                        </v-text-field>
                    </template>
                    <v-date-picker
                        color="#1d2735"
                        first-day-of-week="1"
                        v-model="reserva.fecha"
                        no-title
                        scrollable
                    >
                        <v-spacer></v-spacer>
                        <v-btn text color="red" @click="menu = false">
                            <strong>Cancelar</strong>
                        </v-btn>
                        <v-btn
                            text
                            color="success"
                            @click="$refs.menu.save(reserva.fecha)"
                        >
                            <strong>OK</strong>
                        </v-btn>
                    </v-date-picker>
                </v-menu>
            </v-col>
        </v-row>
        <v-row dense>
            <v-col cols="3" md="3" class="pt-3 pl-0 pb-0">
                <v-text-field v-model="reserva.motivo" label="Motivo" required></v-text-field>
            </v-col>
            <v-col cols="3" md="3" class="pt-3 pl-0 pb-0">
                <v-select
                  dense
                  outlined
                  v-model="reserva.user_id"
                  prepend-icon="mdi-calendar"
                  :items="usuarios"
                  item-text="nombre"
                  item-value="id"
                  label="Reservado por:"
                >
                </v-select>
            </v-col>
            <v-col cols="3">
                <v-tooltip top>
                    <template v-slot:activator="{ on, attrs }">
                        <v-btn
                            fab
                            @click="saveReserva"
                            :loading="isloading"
                            :disabled="isloading"
                            color="orange darken-1"
                            class="mt-2"
                            v-bind="attrs"
                            v-on="on"
                        >
                            <v-icon class="white--text">mdi-content-save-all</v-icon>
                        </v-btn>
                    </template>
                    <span>Guardar Reserva</span>
                </v-tooltip>
            </v-col>
        </v-row>
    </v-card>
    <v-row>
      <v-col cols="12" md="4">
        <v-text-field
          prepend-icon="mdi-account-search"
          v-model="search"
          label="Buscar"
        ></v-text-field>
      </v-col>
    </v-row>
        <v-data-table
        dense
        :headers="headers"
        :items="reservas"
        :search="search"
        :items-per-page="10"
        item-key="id"
        class="elevation-0"
        :sort-by="['created_at']"
        :sort-desc="[true]"
        >
            <template v-slot:item.user.nombre="{ item }">
                <span v-if="item.user">{{ item.user.nombre }}</span>
                <span v-else style="color: red !important"
                ><strong>Cliente Eliminado</strong></span
                >
            </template>
            <template v-slot:item.action="{ item }">
                <v-icon @click="setReserva(item)" small class="mr-2" color="blue">
                    mdi-pencil
                </v-icon>
                <v-icon @click="openModal(item)" small class="mr-2" color="red">
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
                        @click="deleteReserva()">Confirmar</v-btn>
                    <v-spacer></v-spacer>
                </v-card-actions>
            </v-card>
        </v-dialog>
        <v-tooltip right>
            <template v-slot:activator="{ on, attrs }">
                <v-btn fab :to="'/'" :loading="isloading" :disabled="isloading" color="blue" class="mt-3 mx-3" v-bind="attrs" v-on="on">
                    <v-icon class="white--text">mdi-arrow-left-bold-outline</v-icon>
                </v-btn>
            </template>
            <span>Volver</span>
        </v-tooltip>
    </v-card>
</template>

<script>
import axios from 'axios'
    export default {
        data() {
            return {
                search: '',
                menu: false,
                menu: "",
                reserva:{
                    id:'',
                    desde: null,
                    hasta: null,
                    created_at: new Date().toISOString().substr(0, 10),
                    fecha: moment().format('YYYY-MM-DD').substring(0, 10),
                    motivo: "",
                    user_id: null,
                },
                reservas: [],
                desde_horas: [],
                hasta_horas: [],
                motivos: [],
                usuarios: [],
                selectedItem: 0,
                dialog: false,
                headers: [{
                        text: 'Fecha',
                        value: 'fecha',
                        sortable: false,
                    },
                    {
                        text: 'Usuario',
                        value: 'user.nombre',
                        sortable: false,
                    },
                    {
                        text: 'Desde',
                        value: 'hora_desde.hora',
                        sortable: false,
                    },
                    {
                        text: 'Hasta',
                        value: 'hora_hasta.hora',
                        sortable: false,
                    },
                    {
                        text: 'Motivo',
                        value: 'motivo',
                        sortable: false,
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
            this.getReservas()
            this.getUsuarios()
            this.getDesdeHoras()
        },
        watch: {
            menu () {
                this.getDesdeHoras()
            }
        },
        methods: {

            getReservas() {
                axios.get(`api/get-all-reservas`).then(res => {
                    this.reservas = res.data
                }, res => {
                    this.$toast.error('Error consultando las reservas de sala')
                })
            },
            getDesdeHoras() {
                axios.get(`api/get-desde-horas/${this.reserva.fecha}`).then(res => {
                    this.desde_horas = res.data
                }, res => {
                    this.$toast.error('Error consultando las horas de inicio')
                })
            },
            getHastaHoras(desdeHora) {
                axios.get(`api/get-hasta-horas/${this.reserva.fecha}/${desdeHora}`).then(res => {
                    this.reserva.desde = desdeHora
                    this.hasta_horas = res.data
                }, res => {
                    this.$toast.error('Error consultando las horas de fin')
                })
            },
            getUsuarios() {
                axios.get(`api/get-usuarios`).then(
                    (res) => {
                        this.usuarios = res.data.users.data;
                    },
                    (err) => {
                        this.$toast.error("Error consultando clientes");
                    }
                );
            },
            saveReserva() {
                console.log(this.reserva);
                axios.post('api/save-reserva', this.reserva).then(res => {
                    this.reserva = {
                        id:'',
                        desde: null,
                        hasta: null,
                        fecha: moment().format('YYYY-MM-DD').substring(0, 10),
                        motivo: "",
                        user_id: null,
                    }
                    this.hasta_horas = []
                    this.getReservas()
                    this.getDesdeHoras()
                }, res => {
                    this.$toast.error('Error Guardando la reserva')
                })
            },
            setReserva(item) {
                this.reserva = item
                //this.reserva.desde = item.desde
                //this.reserva.hasta = item.hasta
            },
            deleteReserva() {
                this.dialog = false
                axios.get(`api/delete-reserva/${this.reservas[this.selectedItem].id}`).then(res => {
                    this.reservas.splice(this.selectedItem, 1);
                    this.$toast.sucs('Reserva eliminada con exito')
                    this.getDesdeHoras()
                }, err => {
                    this.$toast.error('Error eliminando reserva')
                })
            },
            openModal(item){
                this.selectedItem = this.reservas.indexOf(item);
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
