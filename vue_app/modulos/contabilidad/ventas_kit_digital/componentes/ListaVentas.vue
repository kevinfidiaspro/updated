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
            <template v-slot:item.pagado="{ item }">
                <div
                    @click.stop="openModalPagada(item)"
                    :style="`background-color:${
                        item.pagado == 1
                            ? 'green'
                            : item.pagado == 0
                            ? 'red'
                            : 'orange'
                    }`"
                    class="circle"
                ></div>
            </template>
            <template v-slot:item.importe="{ item }">
                {{ item.importe }} €
            </template>
            <template v-slot:item.cobro_1="{ item }">
                <template v-if="item.cobro_1"> {{ item.cobro_1 }} € </template>
            </template>
            <template v-slot:item.cobro_2="{ item }">
                <template v-if="item.cobro_2"> {{ item.cobro_2 }} € </template>
            </template>
            <template v-slot:item.fecha_firma="{ item }">
                {{ item.fecha_firma | format_date }}
            </template>
            <template v-slot:item.fecha_iva="{ item }">
                {{ item.fecha_iva | format_date }}
            </template>
            <template v-slot:item.justificacion_1="{ item }">
                <div
                    style="
                        background-color: rgba(0, 128, 0, 0.144);
                        text-align: center;
                        padding: 1rem;
                    "
                    v-if="item.justificacion_1 != null"
                >
                    {{ item.justificacion_1 | format_date }}
                </div>
            </template>
            <template v-slot:item.justificacion_2="{ item }">
                <div
                    style="
                        background-color: rgba(0, 128, 0, 0.144);
                        text-align: center;
                        padding: 1rem;
                    "
                    v-if="item.justificacion_2 != null"
                >
                    {{ item.justificacion_2 | format_date }}
                </div>
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
                @getCategorias="getCategorias"
                :categorias="categorias"
                :usuarios="usuarios"
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
        <v-dialog v-model="dialog_pagada" max-width="500px">
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
                    <h2>
                        ¿Estás seguro que deseas marcar la venta como
                        {{ selectedItem.pagada ? "no" : "" }} pagada?
                    </h2>
                </v-card-text>
                <v-card-actions class="pt-3">
                    <v-spacer></v-spacer>
                    <v-btn
                        color="error"
                        large
                        @click="
                            dialog_pagada = false;
                            selectedItem = {};
                        "
                        >Cancelar</v-btn
                    >
                    <v-btn color="success" large @click="pagada()"
                        >Confirmar</v-btn
                    >
                    <v-spacer></v-spacer>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-card>
</template>
<style>
.circle {
    height: 1rem;
    width: 1rem;
    border-radius: 50%;
    display: inline-block;
}
</style>
<script>
import debounce from "lodash/debounce";

import FormVentas from "./FormVentas.vue";
import FilterComponentVue from "../../../../components/general/FilterComponent.vue";
import Resumen from "./ListaResumenVentas.vue";
export default {
    components: { FormVentas, FilterComponentVue, Resumen },
    data() {
        return {
            dialog_pagada: false,
            totalItems: 0,
            search: "",
            filtros_prueba: {
                search: "",
                empresa: {},
                dia: {},
            },
            filter_headers: [
                {
                    title: "Categoria",
                    type: "select",
                    active: false,
                    model: "categoria",
                    item_text: "nombre",
                    item_value: "id",
                    items: [],
                },
                {
                    title: "Cliente",
                    type: "select",
                    active: false,
                    model: "cliente",
                    item_text: "nombres",
                    item_value: "id",
                    items: [],
                },
                {
                    title: "Fecha Firma",
                    type: "date",
                    active: true,
                    model: "dia",
                },
            ],
            headers_admin: [
                { text: "Núm Acuerdo", value: "num_acuerdo", sortable: false },
                { text: "Importe", value: "importe", sortable: false },
                {
                    text: "Razón Social",
                    value: "cliente.nombre_fiscal",
                    sortable: false,
                },
                {
                    text: "Categoría",
                    value: "categoria.nombre",
                    sortable: false,
                },

                { text: "Action", value: "action", sortable: false },
            ],
            headers: [
                { text: "Núm Acuerdo", value: "num_acuerdo", sortable: false },
                {
                    text: "Bono Digital",
                    value: "bono_digital",
                    sortable: false,
                },

                { text: "Importe", value: "importe", sortable: false },
                {
                    text: "Razón Social",
                    value: "cliente.nombres",
                    sortable: false,
                },
                {
                    text: "Categoría",
                    value: "categoria.nombre",
                    sortable: false,
                },
                {
                    text: "Fecha Firma",
                    value: "fecha_firma",
                    sortable: false,
                },
                {
                    text: "Fecha IVA Pagado",
                    value: "fecha_iva",
                    sortable: false,
                },
                {
                    text: "Justificación 1",
                    value: "justificacion_1",
                    sortable: false,
                },
                {
                    text: "Cobro 1",
                    value: "cobro_1",
                    sortable: false,
                },
                {
                    text: "Justificación 2",
                    value: "justificacion_2",
                    sortable: false,
                },
                {
                    text: "Cobro 2",
                    value: "cobro_2",
                    sortable: false,
                },
                {
                    text: "Pagado",
                    value: "pagado",
                    sortable: false,
                },
                { text: "Action", value: "action", sortable: false },
            ],
            ///'num_acuerdo','importe','id_cliente','id_categoria','fecha_firma','fecha_iva','justificacion_1','cobro_1','justificacion_2','cobro_2'
            form_dialog: false,
            venta: {},
            ventas: [],
            selectedItem: 0,
            dialog: false,
            categorias: [],
            usuarios: [],
            page: 1,
            itemsPerPage: 15,
        };
    },
    created() {
        this.getVentas();
        this.getCategorias();
        this.getUsuarios();
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
        pagada() {
            axios.get(`api/pagar-ventas-kit/${this.selectedItem.id}`).then(
                (res) => {
                    this.getVentas();
                    this.dialog_pagada = false;
                },
                (err) => {
                    this.$toast.error("Error consultando clientes");
                }
            );
        },
        loadItems({ page, itemsPerPage, sortBy }) {
            this.page = page;
            this.itemsPerPage = itemsPerPage;
            this.getVentas();
        },
        getUsuarios() {
            axios.get(`api/get-usuarios`).then(
                (res) => {
                    this.usuarios = res.data.users;
                    this.filter_headers[1].items = res.data.users;
                },
                (err) => {
                    this.$toast.error("Error consultando clientes");
                }
            );
        },
        getCategorias() {
            const self = this;

            axios
                .get(`api/get-categorias-kit-digital`)
                .then(function (response) {
                    self.categorias = response.data;
                    self.filter_headers[0].items = response.data;
                });
        },
        generateYears() {
            const currentYear = new Date().getFullYear();
            const startYear = 2022;
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
            if (filtros_prueba.categoria)
                if (filtros_prueba.categoria.value) {
                    search_str +=
                        "&id_categoria=" + filtros_prueba.categoria.value;
                }
            if (filtros_prueba.cliente)
                if (filtros_prueba.cliente.value) {
                    search_str += "&id_cliente=" + filtros_prueba.cliente.value;
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
                    `api/get-ventas-kit?amount=${this.itemsPerPage}&page=${this.page}${search_str}`
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
        openModalPagada(item) {
            this.selectedItem = item;
            this.dialog_pagada = true;
        },
        deleteUser() {
            console.log(this.selectedItem);
            axios
                .post("api/delete-venta-kit", {
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
