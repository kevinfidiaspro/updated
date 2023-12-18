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
            <span>Nueva Venta</span>
        </v-tooltip>

        <v-row>
            <v-col cols="12">
                <FilterComponentVue
                    :headers="filter_headers"
                    v-model="filtros_prueba"
                ></FilterComponentVue>
            </v-col>
        </v-row>
        <template> </template>
        <v-data-table
            :server-items-length="totalItems"
            dense
            :headers="user.rol_ == 1 ? headers_admin : headers"
            :items="ventas"
            :search="filtros_prueba.search"
            :items-per-page="15"
            item-key="id"
            class="elevation-1"
            :sort-by="['nombre']"
            :sort-desc="[false]"
            :item-class="
                () => {
                    return 'pointer';
                }
            "
            @click:row="
                (item) => {
                    venta = item;
                    form_dialog = true;
                }
            "
            @update:options="loadItems"
        >
            <template v-slot:item.cliente="{ item }">
                <template v-if="item.sin_factura && item.cliente != null">{{
                    item.cliente.nombre
                }}</template>
                <template v-else-if="item.factura != null">
                    <template v-if="item.factura.cliente_real != null">{{
                        item.factura.cliente_real.nombre
                    }}</template>
                </template>
            </template>
            <template v-slot:item.factura.tipo_nro="{ item }">
                <template v-if="!item.sin_factura && item.factura != null">{{
                    item.factura.tipo_nro
                }}</template>
                <template v-else>SF</template>
            </template>
            <template v-slot:item.total="{ item }">
                <template v-if="item.factura != null"
                    >{{ item.factura.total }} €</template
                >
                <template v-else-if="item.sin_factura"
                    >{{ item.total_sf }} €</template
                >
            </template>
            <template v-slot:item.total_sin_iva="{ item }">
                <template v-if="item.factura != null">
                    {{ (item.factura.total / 1.21).toFixed(2) }} €
                </template>
                <template v-else-if="item.sin_factura">
                    {{ (item.total_sf / 1.21).toFixed(2) }} €</template
                >
            </template>
            <template v-slot:item.factura.fecha="{ item }">
                <template v-if="item.factura != null">
                    {{ item.factura.fecha | format_date }}
                </template>
                <template v-else-if="item.dia != null">
                    {{ item.dia | format_date }}
                </template>
            </template>
            <template v-slot:item.gasto="{ item }">
                {{ item.gasto }} €
            </template>
            <template v-slot:item.beneficio="{ item }">
                {{ (item.precio / 1.21 - item.gasto).toFixed(2) }} €
            </template>
            <template v-slot:item.action="{ item }">
                <v-icon
                    @click.stop="openModal(item)"
                    small
                    class="mr-2"
                    color="red"
                    style="font-size: 25px"
                    title="BORRAR"
                    >mdi-trash-can</v-icon
                >
            </template>
        </v-data-table>
        <Resumen v-if="user.role == 1"></Resumen>
        <v-dialog v-model="form_dialog" max-width="500px"
            ><FormVentas
                @getTipos="getTipos"
                :tipos="tipos"
                @close_modal="
                    () => {
                        form_dialog = false;
                        getVentas();
                    }
                "
                v-model="venta"
            ></FormVentas
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

import FormVentas from "./FormVentas.vue";
import FilterComponentVue from "../../../../components/general/FilterComponent.vue";
import Resumen from "./ListaResumenVentas.vue";
export default {
    components: { FormVentas, FilterComponentVue, Resumen },
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
                    title: "Factura",
                    type: "select",
                    active: false,
                    model: "factura",
                    item_text: "tipo_nro",
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
                { text: "Dia", value: "factura.fecha", sortable: false },
                { text: "Cliente", value: "cliente", sortable: false },

                { text: "Factura", value: "factura.tipo_nro", sortable: false },
                { text: "Total", value: "total", sortable: false },
                {
                    text: "Total sin IVA",
                    value: "total_sin_iva",
                    sortable: false,
                },
            ],
            headers: [
                { text: "Dia", value: "factura.fecha", sortable: false },
                { text: "Cliente", value: "cliente", sortable: false },
                { text: "Factura", value: "factura.tipo_nro", sortable: false },
                { text: "Total", value: "total", sortable: false },
                {
                    text: "Total sin IVA",
                    value: "total_sin_iva",
                    sortable: false,
                },

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

            axios
                .get(`api/get-facturas-ventas-diarias`)
                .then(function (response) {
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
            if (filtros_prueba.factura)
                if (filtros_prueba.factura.value) {
                    search_str += "&id_factura=" + filtros_prueba.factura.value;
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
                    `api/get-ventas-diarias?page=${this.page}&amount=${this.itemsPerPage}${search_str}`
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
                .post("api/delete-venta-diaria", {
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
