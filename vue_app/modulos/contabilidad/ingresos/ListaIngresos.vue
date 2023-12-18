<template>
    <v-card class="pa-3 ma-3">
        <v-row dense>
            <v-col cols="12" md="12">
                <v-toolbar flat color="#1d2735" dark>
                    <v-icon class="white--text" style="font-size: 45px">
                        mdi-cash-plus
                    </v-icon>
                    <pre><v-toolbar-title><h2>Lista Ingresos</h2></v-toolbar-title></pre>
                </v-toolbar>
            </v-col>
        </v-row>
        <loader v-if="isloading"></loader>
        <v-tooltip right>
            <template v-slot:activator="{ on, attrs }">
                <v-btn
                    fab
                    :to="'/contabilidad'"
                    :loading="isloading"
                    :disabled="isloading"
                    color="blue"
                    class="mt-2 mx-3"
                    v-bind="attrs"
                    v-on="on"
                >
                    <v-icon class="white--text"
                        >mdi-arrow-left-bold-outline</v-icon
                    >
                </v-btn>
            </template>
            <span>Volver</span>
        </v-tooltip>
        <v-tooltip right>
            <template v-slot:activator="{ on, attrs }">
                <v-btn
                    fab
                    :to="{ path: `/guardar-ingreso` }"
                    :loading="isloading"
                    :disabled="isloading"
                    color="orange darken-1"
                    class="mt-2"
                    v-bind="attrs"
                    v-on="on"
                >
                    <v-icon class="white--text">mdi-plus-box</v-icon>
                </v-btn>
            </template>
            <span>Nuevo Ingreso</span>
        </v-tooltip>
        <v-row>
            <v-col cols="12" md="4">
                <v-text-field
                    prepend-icon="mdi-account-search"
                    v-model="search"
                    label="busqueda"
                ></v-text-field>
            </v-col>
        </v-row>
        <v-row>
            <v-col cols="12" md="12">
                <rango-fechas
                    :modelFactura="{ tipo: '' }"
                    :url="url"
                    @success_query="setIngresos"
                ></rango-fechas>
            </v-col>
        </v-row>
        <v-data-table
            :headers="headers"
            :items="allIngresos"
            :search="search"
            :items-per-page="10"
            item-key="id"
            class="elevation-1"
        >
            <template v-slot:item.created_at="{ item }">{{
                item.created_at.substr(0, 10)
            }}</template>
            <template v-slot:item.proyecto.nombre="{ item }">
                <span v-if="item.proyecto">{{ item.proyecto.nombre }}</span>
                <span v-else style="color: red !important"
                    ><strong>No Tiene</strong></span
                >
            </template>
            <template v-slot:item.cliente_nombre="{ item }">
                <span v-if="item.cliente_nombre != 'No Tiene'">{{
                    item.cliente_nombre
                }}</span>
                <span v-else style="color: red !important"
                    ><strong>No Tiene</strong></span
                >
            </template>
            <template v-slot:item.factura_id="{ item }">
                <span v-if="item.facturas">{{
                    item.facturas.nro_anio_factura
                }}</span>
                <span v-else style="color: red !important"
                    ><strong>No Tiene</strong></span
                >
            </template>
            <template v-slot:item.descripcion="{ item }">
                <span v-if="item.descripcion">{{ item.descripcion }}</span>
                <span v-else style="color: red !important"
                    ><strong>No Tiene</strong></span
                >
            </template>
            <template v-slot:item.action="{ item }">
                <router-link
                    :to="{ path: `/guardar-ingreso?id=${item.id}` }"
                    class="action-buttons"
                >
                    <v-icon
                        small
                        class="mr-2"
                        color="#1d2735"
                        style="font-size: 25px"
                        title="EDITAR"
                        >mdi-pencil-outline</v-icon
                    >
                </router-link>
                <v-icon
                    @click="openModal(item)"
                    small
                    class="mr-2"
                    color="red"
                    style="font-size: 25px"
                    title="BORRAR"
                    >mdi-trash-can</v-icon
                >
            </template>
        </v-data-table>
        <v-dialog v-model="dialog" max-width="500px">
            <v-card>
                <v-card-title
                    class="text-h5 aviso"
                    style="
                        justify-content: center;
                        background: #1d2735;
                        color: white;
                    "
                >
                    Aviso
                </v-card-title>
                <v-card-text style="text-align: center">
                    <h2>¿Estás seguro que deseas eliminar?</h2>
                </v-card-text>
                <v-card-actions class="pt-3">
                    <v-spacer></v-spacer>
                    <v-btn
                        color="error"
                        large
                        @click="
                            dialog = false;
                            selectedItem = {};
                        "
                        >Cancelar</v-btn
                    >
                    <v-btn color="success" large @click="deleteIngreso()"
                        >Confirmar</v-btn
                    >
                    <v-spacer></v-spacer>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-card>
</template>

<script>
import FormProyectoVue from "../../proyectos/componentes/FormProyecto.vue";
import { date_mixin } from "../mixins/date_mixin";

import rangoFechas from "../rangoFechas";

export default {
    mixins: [date_mixin],

    components: {
        rangoFechas,
    },

    data() {
        return {
            url: `api/get-ingresos/${localStorage.getItem("user_id")} `,
            search: "",
            ingresos: [],
            allIngresos: [],
            selectedItem: 0,
            dialog: false,
            delete_id: null,
            headers: [
                { text: "Cliente", align: "left", value: "cliente.nombre" },
                {
                    text: "Nombre Fiscal",
                    align: "left",
                    value: "cliente.nombre_fiscal",
                },
                { text: "Num. Factura", align: "left", value: "factura_id" },
                { text: "Fecha", value: "created_at" },
                { text: "Importe", value: "importe" },
                { text: "Descripción Ingreso", value: "descripcion" },
                { text: "Acciones", value: "action", sortable: false },
            ],
        };
    },

    created() {
        // this.getIngresos();
        this.getAllingresos();
    },

    methods: {
        getAllingresos() {
            axios.get(`api/getAllingresos`).then(
                (res) => {
                    this.allIngresos = res.data;
                    console.log(this.allIngresos);
                },
                (res) => {
                    this.$toast.error("Error consultando ingresos");
                }
            );
        },
        getIngresos() {
            axios
                .get(`api/get-ingresos/${localStorage.getItem("user_id")} `)
                .then(
                    (res) => {
                        this.ingresos = res.data;
                    },
                    (res) => {
                        this.$toast.error("Error consultando ingresos");
                    }
                );
        },
        setIngresos(data) {
            if (data.length > 0) {
                this.allIngresos = data;
                return;
            }
            this.$toast.sucs("No se encontraron registros");
        },
        deleteIngreso() {
            this.dialog = false;
            axios.get(`api/delete-ingreso/${this.delete_id}`).then(
                (res) => {
                    //this.ingresos.splice(this.selectedItem, 1);
                    this.$toast.sucs("Ingreso eliminado con exito");
                    this.getAllingresos();
                },
                (err) => {
                    this.$toast.error("Error Eliminando ingreso");
                }
            );
        },
        openModal(item) {
            this.selectedItem = this.ingresos.indexOf(item);
            this.delete_id = item.id;
            this.dialog = true;
        },
    },
    computed: {
        isloading: function () {
            return this.$store.getters.getloading;
        },
    },
};
</script>
