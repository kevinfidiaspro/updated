<template>
    <v-card class="pa-3 ma-3">
        <v-toolbar flat color="#1d2735" dark>
            <v-icon class="white--text" style="font-size: 45px">
                mdi-file-table-outline
            </v-icon>
            <pre><v-toolbar-title><h2>Lista Facturas {{tipo ==2?'Proforma':''}}</h2></v-toolbar-title></pre>
        </v-toolbar>
        <loader v-if="isloading"></loader>
        <v-tooltip right>
            <template v-slot:activator="{ on, attrs }">
                <v-btn
                    fab
                    :to="'/'"
                    :loading="isloading"
                    :disabled="isloading"
                    color="blue"
                    class="mx-3 mt-2"
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
                    :to="{
                        path: `${
                            tipo == 1
                                ? '/registrr-facturas'
                                : '/registrr-facturas-pro'
                        }`,
                    }"
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
            <span>Nueva Factura</span>
        </v-tooltip>
        <v-tooltip right>
            <template v-slot:activator="{ on, attrs }">
                <v-btn
                    fab
                    @click="CambiarAnioModal()"
                    :loading="isloading"
                    :disabled="isloading"
                    color="blue"
                    class="mx-3 mt-2"
                    v-bind="attrs"
                    v-on="on"
                >
                    <v-icon class="white--text">mdi-calendar</v-icon>
                </v-btn>
            </template>
            <span>Cambiar Año Fiscal</span>
        </v-tooltip>
        <v-row>
            <v-row>
                <v-col cols="6" justify="center">
                    <FilterComponentVue
                        :headers="filter_headers"
                        v-model="filtros_prueba"
                    ></FilterComponentVue>
                </v-col>
            </v-row>
        </v-row>
        <v-data-table
            dense
            :headers="tipo == 1 ? headers : headers_proforma"
            :items="proyectos_filtered"
            :search="filtros_prueba.search"
            :items-per-page="15"
            item-key="id"
            class="elevation-1"
            :sort-by="['nro_anio_factura']"
            :sort-desc="[true]"
            :custom-sort="customSort"
        >
            <template v-slot:item.url="{ item }">
                <a :href="item.url" target="_blank">
                    <v-icon> mdi-file-pdf-box </v-icon>
                </a>
            </template>
            <template v-slot:item.cliente_real.nombre_fiscal="{ item }">
                <template v-if="item.cliente_real.nombre_fiscal">{{
                    item.cliente_real.nombre_fiscal
                }}</template>
                <template v-else>{{ item.cliente_real.nombre }}</template>
            </template>
            <template v-slot:item.total="{ item }">
                {{ item.total }} €
            </template>
            <template v-slot:item.facturada="{ item }">
                <div
                    :style="`background-color:${
                        item.id_factura != null ? 'green' : 'red'
                    }`"
                    class="circle"
                ></div>
            </template>
            <template v-slot:item.pagada="{ item }">
                <div
                    :style="`background-color:${
                        item.esta_pagada == 1
                            ? 'green'
                            : item.esta_pagada == 0
                            ? 'red'
                            : item.esta_pagada == 2
                            ? 'orange'
                            : 'blue'
                    }`"
                    class="circle"
                ></div>
            </template>
            <template v-slot:item.action="{ item }">
                <v-icon
                    v-if="tipo != 2 && !item.esta_pagada"
                    @click="openModalIngreso(item)"
                    small
                    class="mr-2"
                    color="red"
                    style="font-size: 25px"
                    title="Generar Ingreso"
                    >mdi-cash</v-icon
                >

                <v-icon
                    v-else
                    small
                    class="mr-2"
                    style="font-size: 25px; width: 25px"
                    title="Generar Ingreso"
                ></v-icon>
                <v-icon
                    @click="openModalVenta(item)"
                    small
                    class="mr-2"
                    color="red"
                    style="font-size: 25px"
                    title="Generar Venta"
                    >mdi-cash-100</v-icon
                >
                <v-icon
                    v-if="tipo == 2"
                    @click="openModalCambio(item.id)"
                    small
                    class="mr-2"
                    style="font-size: 25px"
                    title="Cambiar Tipo de Factura"
                    >mdi-swap-horizontal</v-icon
                >

                <router-link
                    :to="{
                        path: `${
                            tipo == 1
                                ? '/registrr-facturas'
                                : '/registrr-facturas-pro'
                        }?id=${item.id}`,
                    }"
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
                    @click="openModal(item.id)"
                    small
                    class="mr-2"
                    color="red"
                    style="font-size: 25px"
                    title="BORRAR"
                    >mdi-trash-can</v-icon
                >
            </template>
        </v-data-table>
        <v-dialog v-model="anio_dialog" max-width="500px">
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
                    <h2>¿Estás seguro que deseas cambiar el año fiscal?</h2>
                </v-card-text>
                <v-card-actions class="pt-3">
                    <v-spacer></v-spacer>

                    <v-btn color="error" large @click="anio_dialog = false"
                        >Cancelar</v-btn
                    >
                    <v-btn color="success" large @click="cambiar_anio()"
                        >Confirmar</v-btn
                    >
                    <v-spacer></v-spacer>
                </v-card-actions>
            </v-card>
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
                    <v-btn color="success" large @click="deleteFactura()"
                        >Confirmar</v-btn
                    >
                    <v-spacer></v-spacer>
                </v-card-actions>
            </v-card>
        </v-dialog>
        <v-dialog v-model="dialog_cambio" max-width="500px">
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
                        ¿Estás seguro que deseas Cambiar el tipo de factura?
                    </h2>
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
                    <v-btn color="success" large @click="ChangeFactura()"
                        >Confirmar</v-btn
                    >
                    <v-spacer></v-spacer>
                </v-card-actions>
            </v-card>
        </v-dialog>
        <v-dialog v-model="dialog_ingreso" max-width="500px">
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
                    <h2>¿Estás seguro que deseas generar un ingreso?</h2>
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
                    <v-btn color="success" large @click="saveIngreso()"
                        >Confirmar</v-btn
                    >
                    <v-spacer></v-spacer>
                </v-card-actions>
            </v-card>
        </v-dialog>
        <v-dialog v-model="dialogVenta" max-width="500px">
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
                    <h2>¿Estás seguro que deseas generar una Venta?</h2>
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
                    <v-btn color="success" large @click="saveVenta()"
                        >Confirmar</v-btn
                    >
                    <v-spacer></v-spacer>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-card>
</template>
<script>
import FilterComponentVue from "../../../components/general/FilterComponent.vue";

export default {
    components: { FilterComponentVue },
    data() {
        return {
            dialogVenta: false,
            filtros_prueba: {
                search: "",
                proyecto: { value: null },
            },
            filter_headers: [
                {
                    title: "Proyecto",
                    type: "select",
                    active: false,
                    model: "proyecto",
                    item_text: "nombre",
                    item_value: "id",
                    items: [],
                },
            ],
            dialog_cambio: false,
            dialog: false,
            tipo: 1,
            search: "",
            anio_dialog: false,
            proyectos: [],
            selected_id: null,
            dialog_ingreso: false,
            ingreso: {},
            item_ingreso: {},
            headers: [
                {
                    text: "N° Factura",
                    value: "nro_anio_factura",
                    sortable: true,
                },
                { text: "Fecha", value: "fecha", sortable: false },
                {
                    text: "Cliente",
                    value: "cliente_real.nombre",
                    sortable: false,
                },
                {
                    text: "Nombre fiscal",
                    value: "cliente_real.nombre_fiscal",
                    sortable: false,
                },
                { text: "Total", value: "total", sortable: false },
                { text: "Pagada", value: "pagada", align: "center" },
                { text: "PDF", value: "url", sortable: false },
                {
                    text: "Acciones",
                    value: "action",
                    sortable: false,
                    align: "center",
                },
            ],
            headers_proforma: [
                {
                    text: "N° Factura",
                    value: "nro_anio_factura",
                    sortable: true,
                },
                { text: "Fecha", value: "fecha", sortable: false },
                {
                    text: "Cliente",
                    value: "cliente_real.nombre",
                    sortable: false,
                },
                {
                    text: "Nombre fiscal",
                    value: "cliente_real.nombre_fiscal",
                    sortable: false,
                },
                { text: "Total", value: "total", sortable: false },
                { text: "Facturada", value: "facturada", align: "center" },
                { text: "PDF", value: "url", sortable: false },
                {
                    text: "Acciones",
                    value: "action",
                    sortable: false,
                    align: "center",
                },
            ],
        };
    },
    created() {
        if (this.$route.path == "/lista-facturas-pro") {
            this.tipo = 2;
        }
        this.getAllFacturas();
        this.getProyectos();
    },
    methods: {
        getProyectos() {
            axios.get(`api/get-cliente-proyectos?itemsPerPage=-1`).then(
                (res) => {
                    this.filter_headers[0].items = res.data.data;
                },
                (res) => {}
            );
        },
        ChangeFactura() {
            axios
                .post(`api/change-factura-type`, {
                    id: this.selected_id,
                    tipo: 1,
                })
                .then(
                    (res) => {
                        this.$router.push(`/lista-facturas`);
                    },
                    (res) => {}
                );
        },
        getRandomCode() {
            let randomChars =
                "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
            let result = "";
            for (var i = 0; i < 5; i++) {
                result += randomChars.charAt(
                    Math.floor(Math.random() * randomChars.length)
                );
            }
            this.ingreso.codigo = result;
        },
        saveIngreso() {
            this.ingreso = {
                descripcion: "Ingreso Automatico",
                factura_id: this.item_ingreso.id,
                importe: this.item_ingreso.total,
                user_id: localStorage.getItem("user_id"),
                proyecto_id: this.item_ingreso.id_proyecto,
                cliente_id: this.item_ingreso.id_cliente,
            };
            this.getRandomCode();

            axios.post("api/save-ingreso", this.ingreso).then(
                (res) => {
                    if (res.data.error != null) {
                        this.$toast.error(res.data.error);
                    } else {
                        this.$toast.sucs("Ingreso guardado con exito");
                        this.dialog_ingreso = false;
                    }
                    this.getAllFacturas();
                },
                (res) => {
                    this.$toast.error("Error guardando ingreso");
                }
            );
        },
        saveVenta() {
            const factura = {
                id_factura: 495,
                sin_factura: false,
            };
            this.getRandomCode();

            axios.post("api/save-venta-diaria", factura).then(
                (res) => {
                    if (res.data.error != null) {
                        this.$toast.error(res.data.error);
                    } else {
                        this.$toast.sucs("Venta guardada con exito");
                        this.dialogVenta = false;
                    }
                    this.getAllFacturas();
                },
                (res) => {
                    this.$toast.error("Error guardando venta");
                }
            );
        },
        openModalIngreso(item) {
            this.item_ingreso = item;
            this.dialog_ingreso = true;
        },
        openModalVenta(id) {
            this.selected_id = id;
            this.dialogVenta = true;
        },
        openModalCambio(id) {
            this.selected_id = id;
            this.dialog_cambio = true;
        },
        openModal(id) {
            this.selected_id = id;
            this.dialog = true;
        },
        deleteFactura() {
            axios.get(`api/delete-facturas/${this.selected_id}`).then(
                (res) => {
                    this.$toast.sucs("Factura Eliminada");
                    this.dialog = false;
                    this.getAllFacturas();
                },
                (err) => {
                    this.$toast.error("Error eliminando Factura");
                }
            );
        },
        customSort(items, index, isDesc) {
            items.sort((a, b) => {
                if (index[0] == "nro_anio_factura") {
                    if (a.anio - b.anio == 0) {
                        if (!isDesc[0]) {
                            return a.nro_factura - b.nro_factura;
                        } else {
                            return b.nro_factura - a.nro_factura;
                        }
                    } else {
                        if (!isDesc[0]) {
                            return a.anio - b.anio;
                        } else {
                            return b.anio - a.anio;
                        }
                    }
                }
            });
            return items;
        },
        CambiarAnioModal() {
            this.anio_dialog = true;
        },
        cambiar_anio() {
            axios.get("api/cambiar-anio-fiscal").then((res) => {
                this.anio_dialog = false;
            });
        },
        getAllFacturas() {
            axios.get(`api/index-facturas/${this.tipo}`).then(
                (res) => {
                    this.proyectos = res.data;
                },
                (res) => {
                    this.$toast.error("Error consultando proyectos");
                }
            );
        },

        deleteProyecto(item) {
            axios.get(`api/delete-proyecto/${item.id}`).then(
                (res) => {
                    this.proyectos.splice(this.proyectos.indexOf(item), 1);
                    this.$toast.sucs("Proyecto eliminado");
                },
                (err) => {
                    this.$toast.error("Error eliminando Proyecto");
                }
            );
        },
    },

    computed: {
        proyectos_filtered: function () {
            const id_proyecto = this.filtros_prueba.proyecto.value;
            if (id_proyecto != null) {
                return this.proyectos.filter((factura) => {
                    return factura.id_proyecto == id_proyecto;
                });
            } else {
                return this.proyectos;
            }
        },
        isloading: function () {
            return this.$store.getters.getloading;
        },
    },
};
</script>
<style>
.circle {
    height: 1rem;
    width: 1rem;
    border-radius: 50%;
    display: inline-block;
}
</style>
