<template>
    <v-card class="pa-3 ma-3">
        <v-toolbar flat color="#1d2735" dark>
            <v-icon class="white--text" style="font-size: 45px"
                >mdi-account-supervisor-circle</v-icon
            >
            <pre><v-toolbar-title><h2>Lista Marketing</h2></v-toolbar-title></pre>
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
        </v-row>
        <v-data-table
            :server-items-length="totalItems"
            dense
            :headers="user.rol_tfg == 1 ? headers_admin : headers"
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
            <template v-slot:item.gasto="{ item }">
                {{ item.gasto }} €
            </template>
            <template v-slot:item.beneficio="{ item }">
                {{ item.precio - item.gasto }} €
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
            ></FormVentasTfg
        ></v-dialog>
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

import FormVentasTfg from "./FormMarketing.vue";
import FilterComponentVue from "../../../../components/general/FilterComponent.vue";

export default {
    components: { FormVentasTfg, FilterComponentVue },
    data() {
        return {
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
            ],
            headers: [
                { text: "Mes", value: "mes_str", sortable: false },
                { text: "Web", value: "web_str", sortable: false },
                { text: "Invertido", value: "invertido", sortable: false },
                { text: "Clics", value: "clics", sortable: false },
                { text: "Potenciales", value: "potenciales", sortable: false },
                { text: "€/Pot", value: "inv_pot", sortable: false },

                { text: "Acciones", value: "action", sortable: false },
            ],
            headers_admin: [
                { text: "Mes", value: "mes_str", sortable: false },
                { text: "Web", value: "web_str", sortable: false },
                { text: "Invertido", value: "invertido", sortable: false },
                { text: "Clics", value: "clics", sortable: false },
                { text: "Potenciales", value: "potenciales", sortable: false },
                { text: "€/Pot", value: "inv_pot", sortable: false },
                { text: "Cerrados", value: "cerrados", sortable: false },
                { text: "Ventas €", value: "ventas", sortable: false },
                {
                    text: "Venta x € Invertido",
                    value: "venta_inv",
                    sortable: false,
                },
                {
                    text: "% Pres * Visitas",
                    value: "pres_visitas",
                    sortable: false,
                },
                { text: "% Ventas", value: "per_ventas", sortable: false },
                { text: "Precio Medio", value: "prec_medio", sortable: false },
                { text: "Acciones", value: "action", sortable: false },
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
            const startYear = 2023;
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
                    `api/get-marketing-tfg?page=${this.page}&amount=${this.itemsPerPage}${search_str}`
                )
                .then(
                    (res) => {
                        self.ventas = res.data.data;
                        self.totalItems = res.data.total;
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
                .post("api/delete-marketing-tfg", {
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
