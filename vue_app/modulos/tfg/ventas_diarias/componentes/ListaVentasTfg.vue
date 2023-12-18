<template>
    <v-card class="pa-3 ma-3">
        <v-toolbar flat color="#1d2735" dark>
            <v-icon class="white--text" style="font-size: 45px"
                >mdi-account-supervisor-circle</v-icon
            >
            <pre><v-toolbar-title><h2>Lista Ventas</h2></v-toolbar-title></pre>
        </v-toolbar>
        <v-tooltip right>
            <template v-slot:activator="{ on, attrs }">
                <v-btn
                    fab
                    @click="
                        form_dialog = true;
                        venta = {};
                    "
                    :loading="isLoading"
                    :disabled="isLoading"
                    color="orange darken-1"
                    class="mt-2"
                    v-bind="attrs"
                    v-on="on"
                >
                    <v-icon class="white--text"
                        >mdi-account-plus-outline</v-icon
                    >
                </v-btn>
            </template>
            <span>Nuevo Venta</span>
        </v-tooltip>

        <v-row>
            <v-col cols="12">
                <FilterComponentVue
                    :headers="filter_headers"
                    v-model="filtros_prueba"
                ></FilterComponentVue>
            </v-col>
            <v-col
                cols="12"
                md="12"
                v-if="totales != null"
                style="margin-bottom: 1rem"
            >
                <div class="d-flex justify-end">
                    <div class="d-flex justify-end">
                        <v-card
                            elevation="2"
                            shaped
                            color="#666666"
                            width="120"
                            height="70"
                            class="my-0 mx-2"
                            v-if="totales.total_precio"
                        >
                            <v-card-text class="text-center white--text">
                                <p
                                    class="font-weight-bold"
                                    style="margin-bottom: -4px"
                                >
                                    Precio
                                </p>
                                {{ totales.total_precio | decimalComma }}
                            </v-card-text>
                        </v-card>
                        <v-card
                            elevation="2"
                            shaped
                            color="#666666"
                            width="150"
                            height="70"
                            class="my-0 mx-2"
                            v-if="totales.total_precio_no_iva"
                        >
                            <v-card-text class="text-center white--text">
                                <p
                                    class="font-weight-bold"
                                    style="margin-bottom: -4px"
                                >
                                    Precio sin iva
                                </p>
                                {{ totales.total_precio_no_iva | decimalComma }}
                            </v-card-text>
                        </v-card>
                        <v-card
                            elevation="2"
                            shaped
                            color="#666666"
                            width="150"
                            height="70"
                            class="my-0 mx-2"
                            v-if="totales.total_pagado"
                        >
                            <v-card-text class="text-center white--text">
                                <p
                                    class="font-weight-bold"
                                    style="margin-bottom: -4px"
                                >
                                    Pagado
                                </p>
                                {{ totales.total_pagado | decimalComma }}
                            </v-card-text>
                        </v-card>
                        <v-card
                            elevation="2"
                            shaped
                            color="#666666"
                            width="150"
                            height="70"
                            class="my-0 mx-2"
                            v-if="totales.total_presupuesto"
                        >
                            <v-card-text class="text-center white--text">
                                <p
                                    class="font-weight-bold"
                                    style="margin-bottom: -4px"
                                >
                                    Presupuesto
                                </p>
                                {{ totales.total_presupuesto | decimalComma }}
                            </v-card-text>
                        </v-card>
                        <v-card
                            elevation="2"
                            shaped
                            color="#666666"
                            width="150"
                            height="70"
                            class="my-0 mx-2"
                            v-if="totales.total_gasto"
                        >
                            <v-card-text class="text-center white--text">
                                <p
                                    class="font-weight-bold"
                                    style="margin-bottom: -4px"
                                >
                                    Pagado Docente
                                </p>
                                {{ totales.total_gasto | decimalComma }}
                            </v-card-text>
                        </v-card>
                        <v-card
                            elevation="2"
                            shaped
                            color="#666666"
                            width="150"
                            height="70"
                            class="my-0 mx-2"
                            v-if="totales.total_gasto"
                        >
                            <v-card-text class="text-center white--text">
                                <p
                                    class="font-weight-bold"
                                    style="margin-bottom: -4px"
                                >
                                    Beneficio
                                </p>
                                {{
                                    (
                                        totales.total_precio / 1.21 -
                                        totales.total_gasto
                                    ).toFixed(2) | decimalComma
                                }}
                            </v-card-text>
                        </v-card>
                    </div>
                </div>
            </v-col>
        </v-row>
        <v-data-table
            :server-items-length="totalItems"
            dense
            :headers="
                user.rol_tfg == 1
                    ? headers_admin
                    : user.rol_tfg == 4
                    ? headers_secretaria
                    : headers
            "
            :items="ventas"
            :search="filtros_prueba.search"
            :items-per-page="15"
            item-key="id"
            class="elevation-1"
            :sort-by="['nombre']"
            :sort-desc="[false]"
            @update:options="loadItems"
        >
            <template v-slot:item.precio="{ item }">
                {{ item.precio }} €
            </template>
            <template v-slot:item.precio_no_iva="{ item }">
                {{ (item.precio / 1.21).toFixed(2) }} €
            </template>
            <template v-slot:item.presupuesto="{ item }">
                {{ item.presupuesto }} €
            </template>
            <template v-slot:item.gasto="{ item }">
                {{ item.gasto }} €
            </template>
            <template v-slot:item.beneficio="{ item }">
                {{ (item.precio / 1.21 - item.gasto).toFixed(2) }} €
            </template>
            <template v-slot:item.action="{ item }">
                <v-icon
                    @click="
                        () => {
                            venta = item;
                            form_dialog = true;
                        }
                    "
                    small
                    class="mr-2"
                    color="#1d2735"
                    style="font-size: 25px"
                    title="EDITAR"
                    >mdi-pencil-outline</v-icon
                >

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
        <Resumen v-if="user.rol_tfg == 1"></Resumen>
        <v-dialog v-model="form_dialog" max-width="500px"
            ><FormVentasTfg
                @getTipos="getTipos"
                :tipos="tipos"
                @close_modal="
                    () => {
                        form_dialog = false;
                        getVentas();
                    }
                "
                v-model="venta"
            ></FormVentasTfg>
        </v-dialog>
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
                    <v-btn color="success" large @click="deleteUser()"
                        >Confirmar</v-btn
                    >
                    <v-spacer></v-spacer>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-card>
</template>
<script>
import debounce from "lodash/debounce";

import FormVentasTfg from "./FormVentasTfg.vue";
import FilterComponentVue from "../../../../components/general/FilterComponent.vue";
import Resumen from "../componentes/ListaResumenVentas.vue";

export default {
    components: { FormVentasTfg, FilterComponentVue, Resumen },
    data() {
        return {
            totales: null,

            totalItems: 0,
            search: "",
            filtros_prueba: {
                search: "",
                empresa: {},
                dia: {},
            },
            filter_headers: [
                {
                    title: "Web ",
                    type: "select",
                    active: false,
                    model: "empresa",
                    item_text: "nombre",
                    item_value: "id",
                    items: [],
                },
                {
                    title: "Dia",
                    type: "date",
                    active: true,
                    model: "dia",
                },
            ],
            headers_admin: [
                { text: "Dia", value: "dia", sortable: false },
                { text: "Código", value: "codigo", sortable: false },
                { text: "Web", value: "empresa.nombre", sortable: false },
                { text: "Precio", value: "precio", sortable: false },
                {
                    text: "Precio sin IVA",
                    value: "precio_no_iva",
                    sortable: false,
                },
                { text: "Pagado", value: "pagado", sortable: false },
                { text: "Presupuesto", value: "presupuesto", sortable: false },
                { text: "Pagado Docente", value: "gasto", sortable: false },
                { text: "Beneficio", value: "beneficio", sortable: false },
                { text: "Action", value: "action", sortable: false },
            ],
            headers: [
                { text: "Dia", value: "dia", sortable: false },
                { text: "Código", value: "codigo", sortable: false },
                { text: "Web", value: "empresa.nombre", sortable: false },
                { text: "Precio", value: "precio", sortable: false },

                { text: "Action", value: "action", sortable: false },
            ],
            headers_secretaria: [
                { text: "Dia", value: "dia", sortable: false },
                { text: "Código", value: "codigo", sortable: false },
                { text: "Web", value: "empresa.nombre", sortable: false },
                { text: "Precio", value: "precio", sortable: false },
                { text: "Presupuesto", value: "presupuesto", sortable: false },
                { text: "Gasto", value: "gasto", sortable: false },
                { text: "Action", value: "action", sortable: false },
            ],
            form_dialog: false,
            venta: {},
            ventas: [],
            selectedItem: 0,
            dialog: false,
            tipos: [],
            page: 1,
            itemsPerPage: 15,
        };
    },
    created() {
        this.getVentas();
        this.getTipos();
    },
    watch: {
        filtros_prueba: {
            deep: true,
            handler: debounce(function (n) {
                this.getVentas();
            }, 500),
        },
    },
    methods: {
        loadItems({ page, itemsPerPage, sortBy }) {
            this.page = page;
            this.itemsPerPage = itemsPerPage;
            this.getVentas();
        },
        getTipos() {
            const self = this;
            axios.get(`api/get-empresas-tfg`).then(function (response) {
                self.tipos = response.data;
                self.filter_headers[0].items = response.data;
            });
        },
        generateYears() {
            const currentYear = new Date().getFullYear();
            const startYear = 2020;
            const years = [];

            for (let year = startYear; year <= currentYear; year++) {
                years.push(year);
            }
        },
        getVentas() {
            const filtros_prueba = this.filtros_prueba;
            const self = this;
            console.log(filtros_prueba);
            let search_str = "";
            if (filtros_prueba.search) {
                search_str += "&search=" + filtros_prueba.search;
            }
            if (filtros_prueba.empresa)
                if (filtros_prueba.empresa.value) {
                    search_str += "&id_empresa=" + filtros_prueba.empresa.value;
                }
            if (filtros_prueba.dia) {
                if (filtros_prueba.dia.start) {
                    search_str += "&fecha_inicio=" + filtros_prueba.dia.start;
                }
                if (filtros_prueba.dia.end) {
                    search_str += "&fecha_fin=" + filtros_prueba.dia.end;
                }
            }

            axios
                .get(
                    `api/get-ventas-tfg?page=${this.page}&amount=${this.itemsPerPage}${search_str}`
                )
                .then(
                    (res) => {
                        self.ventas = res.data.data;
                        self.totalItems = res.data.total;
                        self.totales = res.data.totales;
                    },
                    (err) => {
                        this.$toast.error("Error consultando Ventas");
                    }
                );
        },
        openModal(item) {
            this.selectedItem = item;
            this.dialog = true;
        },
        deleteUser() {
            console.log(this.selectedItem);
            axios
                .post("api/delete-venta-tfg", {
                    id: this.selectedItem.id,
                })
                .then(
                    (res) => {
                        this.$toast.sucs("Venta eliminado");
                        this.dialog = false;
                        this.getVentas();
                    },
                    (err) => {
                        this.$toast.error("Error eliminando Venta");
                    }
                );
        },
    },
    computed: {
        user() {
            return this.$store.getters.getuser;
        },
        isLoading: function () {
            return this.$store.getters.getloading;
        },
    },
};
</script>
