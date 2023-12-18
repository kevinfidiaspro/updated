<template>
    <v-card class="pa-3 ma-3">
        <v-toolbar flat color="#1d2735" dark>
            <v-icon class="white--text" style="font-size: 45px"
                >mdi-file-powerpoint</v-icon
            >
            <pre><v-toolbar-title><h2>Lista Potenciales</h2></v-toolbar-title></pre>
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
                    :to="{ path: `/guardar-potencial` }"
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
            <span>Nuevo Potencial</span>
        </v-tooltip>
        <v-row>
            <v-col cols="6" md="2" class="m-0 p-0">
                <date-select v-model="filtro.datestart" label="Desde">
                </date-select>
            </v-col>
            <v-col cols="6" md="2" class="m-0 p-0">
                <date-select
                    v-model="filtro.dateend"
                    label="Hasta"
                ></date-select>
            </v-col>
            <v-col cols="6" md="2" class="m-0 p-0">
                <v-autocomplete
                    outlined
                    v-model="filtro.estado"
                    :items="estados"
                    item-value="id"
                    item-text="nombre"
                    label="Estado"
                >
                </v-autocomplete>
            </v-col>
            <v-col cols="6" md="2" class="m-0 p-0">
                <v-autocomplete
                    outlined
                    v-model="filtro.campain"
                    :items="campains"
                    item-value="id"
                    item-text="name"
                    label="Campaña"
                >
                </v-autocomplete>
            </v-col>
            <v-col cols="12" md="2">
                <v-tooltip right>
                    <template v-slot:activator="{ on, attrs }">
                        <v-btn
                            fab
                            @click="getAllPotenciales"
                            :loading="isloading"
                            :disabled="isloading"
                            color="orange darken-1"
                            class="mt-2"
                            v-bind="attrs"
                            v-on="on"
                        >
                            <v-icon class="white--text">mdi-filter</v-icon>
                        </v-btn>
                    </template>
                    <span>Filtrar</span>
                </v-tooltip>
            </v-col>
        </v-row>
        <v-row>
            <v-col cols="12" md="3">
                <v-text-field
                    prepend-icon="mdi-account-search"
                    v-model="search"
                    label="Buscar"
                ></v-text-field>
            </v-col>
        </v-row>
        <v-data-table
            :server-items-length="totalItems"
            @update:options="loadItems"
            :loading="loading"
            dense
            :headers="headers"
            :items="potenciales"
            :search="search"
            :items-per-page="15"
            item-key="id"
            class="elevation-1"
            :item-class="row_classes"
            :footer-props="{ 'items-per-page-options': [20, 50, 100] }"
        >
            <template v-slot:item.es_kit="{ item }">
                <span v-if="item.es_kit" style="color: rgb(17, 143, 0)">
                    Si
                </span>
                <span v-else style="color: red"> No</span>
            </template>
            <template v-slot:item.ultimo_seguimiento="{ item }">
                <template v-if="item.proyecto_tareas.length > 0">
                    {{
                        item.proyecto_tareas[item.proyecto_tareas.length - 1]
                            .fecha | format_date
                    }}
                </template>
            </template>
            <template v-slot:item.estado_potencial="{ item }">
                <span
                    v-if="
                        item.estado_potencial.nombre.includes('Pdte') ||
                        item.estado_potencial.nombre.includes('Incompleto')
                    "
                    style="color: #ffd600 !important"
                >
                    <strong>{{ item.estado_potencial.nombre }}</strong>
                </span>
                <span
                    v-else-if="
                        item.estado_potencial.nombre.includes('No') ||
                        item.estado_potencial.nombre.includes('Falso') ||
                        item.estado_potencial.nombre.includes('Infieles')
                    "
                    style="color: red !important"
                >
                    <strong>{{ item.estado_potencial.nombre }}</strong>
                </span>
                <span v-else>
                    <strong style="color: green !important">{{
                        item.estado_potencial.nombre
                    }}</strong>
                </span>
            </template>
            <template v-slot:item.campana="{ item }">
                <span v-if="item.lead_form">{{ item.lead_form.name }}</span>
                <span v-else style="color: red !important"
                    ><strong>No Tiene</strong></span
                >
            </template>
            <template v-slot:item.usuario.nombre="{ item }">
                <span v-if="item.usuario">{{ item.usuario.nombre }}</span>
                <span v-else style="color: red !important"
                    ><strong>Cliente Eliminado</strong></span
                >
            </template>
            <template v-slot:item.action="{ item }">
                <router-link
                    v-if="role.toString() != '6'"
                    :to="{ path: `/guardar-potencial?id=${item.id}` }"
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
                <router-link
                    :to="{
                        path: `/registrar-presupuesto?id_proyecto=${item.id}${
                            item.presupuesto == null ? '' : `&id=${item.id}`
                        }`,
                    }"
                >
                    <v-icon
                        small
                        class="mr-2"
                        :color="
                            item.presupuesto == null
                                ? 'orange darken-1'
                                : 'success'
                        "
                        style="font-size: 25px"
                        title="Presupuesto"
                    >
                        mdi-card-text-outline
                    </v-icon>
                </router-link>
                <v-icon
                    @click="
                        potencial = item;
                        dialog_whatsapp = true;
                    "
                    small
                    class="mr-2"
                    color="success"
                    style="font-size: 25px"
                    title="Whatsapp"
                    >mdi-whatsapp</v-icon
                >
            </template>
        </v-data-table>
        <v-dialog v-model="dialog" max-width="500px">
            <v-card>
                <v-card-title
                    class="text-h5 aviso"
                    style="
                        justify-content: center;

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
                    <v-btn color="success" large @click="deletePotencial()"
                        >Confirmar</v-btn
                    >
                    <v-spacer></v-spacer>
                </v-card-actions>
            </v-card>
        </v-dialog>
        <WhatsAppDialog
            :name="potencial.usuario.nombre"
            v-model="dialog_whatsapp"
            :to="potencial.usuario.telefono"
        ></WhatsAppDialog>
    </v-card>
</template>
<script>
import WhatsAppDialog from "../../potenciales/componentes/WhatsAppDialog.vue";
import debounce from "lodash/debounce";

export default {
    components: {
        WhatsAppDialog,
    },
    watch: {
        filtros: {
            deep: true,
            handler(val) {
                this.$store.dispatch("setFiltrosPotenciales", val);
            },
        },
        search: debounce(function (n) {
            this.getAllPotenciales();
        }, 200),
    },
    data() {
        return {
            totalItems: 67,
            page: 1,
            itemsPerPage: 15,
            estados: [],
            campains: [],
            menuStart: false,
            menuEnd: false,
            filtro: null,
            search: "",
            potenciales: [],
            selectedItem: 0,
            dialog: false,
            potencial: {
                usuario: {
                    telefono: null,
                },
            },
            dialog_whatsapp: false,
        };
    },
    created() {
        this.filtro = this.$store.getters.get_filtros_potenciales;

        this.getAllPotenciales();
        this.getEstados();
        this.getCampains();
    },
    methods: {
        loadItems({ page, itemsPerPage, sortBy, sortDesc }) {
            console.log(sortBy);
            console.log;
            if (this.filtro == null) {
                this.filtro = this.$store.getters.get_filtros_potenciales;
            }
            try {
                this.filtro.sortBy = sortBy[0];
                this.filtro.sortDesc = sortDesc[0];
            } catch (ex) {}

            this.filtro.page = page;
            this.filtro.itemsPerPage = itemsPerPage;
            this.getAllPotenciales();
        },
        getCampains() {
            axios.get(`webhook/get-all-formularios?id=104140814678268`).then(
                (res) => {
                    this.campains = res.data;
                },
                (err) => {
                    this.$toast.error("Error consultando campañas");
                }
            );
        },
        getEstados() {
            axios.get(`api/get-all-estados-potencial`).then(
                (res) => {
                    this.estados = [
                        {
                            id: null,
                            nombre: "Todos",
                            created_at: null,
                            updated_at: "2022-12-26T23:00:00.000000Z",
                        },

                        ...res.data,
                    ];
                },
                (err) => {
                    this.$toast.error("Error consultando estados");
                }
            );
        },

        row_classes(item) {
            if (item.id_lead_form != null) {
                return "no-lead";
            } else {
                return "no-lead";
            }
        },
        getAllPotenciales() {
            this.loading = true;
            this.filtro.search = this.search;
            axios.post(`api/filtro-get-all-potenciales`, this.filtro).then(
                (res) => {
                    this.loading = false;

                    this.potenciales = res.data.data;
                    this.totalItems = res.data.total;
                },
                (err) => {
                    this.$toast.error("Error consultando potenciales");
                }
            );
        },
        deletePotencial() {
            this.dialog = false;
            axios
                .get(
                    `api/delete-potencial/${
                        this.potenciales[this.selectedItem].id
                    }`
                )
                .then(
                    (res) => {
                        this.potenciales.splice(this.selectedItem, 1);
                        this.$toast.sucs("Potencial eliminado");
                    },
                    (err) => {
                        this.$toast.error("Error eliminando Potencial");
                    }
                );
        },
        openModal(item) {
            this.selectedItem = this.potenciales.indexOf(item);
            this.dialog = true;
        },
    },
    computed: {
        headers: function () {
            let headers = [
                {
                    text: "#",
                    value: "usuario.id",
                    sortable: false,
                },
                {
                    text: "Cliente",
                    value: "usuario.nombre",
                    sortable: false,
                },
                {
                    text: "Email",
                    value: "usuario.email",
                    sortable: false,
                },
                {
                    text: "Estado",
                    value: "estado_potencial",
                    sortable: true,
                },
                {
                    text: "Teléfono",
                    value: "usuario.telefono",
                    sortable: false,
                },
                {
                    text: "Es Kit",
                    value: "es_kit",
                    sortable: true,
                },
                {
                    text: "Vendedor",
                    value: "usuario.vendedor.nombre",
                    sortable: false,
                },
                {
                    text: "Campaña",
                    value: "campana",
                    sortable: false,
                },
                {
                    text: "Fecha Alta",
                    value: "fecha_alta",
                    sortable: false,
                },
                {
                    text: "Ultimo Seguimiento",
                    value: "ultimo_seguimiento",
                    sortable: false,
                },
                {
                    text: "Acciones",
                    value: "action",
                    sortable: false,
                },
            ];

            if (this.role.toString() != "1" && this.role.toString() != "5") {
                headers.splice(6, 1);
                if (this.role.toString() == "6") {
                    headers = [
                        {
                            text: "#",
                            value: "usuario.id",
                            sortable: true,
                        },
                        {
                            text: "Cliente",
                            value: "usuario.nombre",
                            sortable: true,
                        },
                        {
                            text: "Email",
                            value: "usuario.email",
                            sortable: false,
                        },
                        {
                            text: "Es Kit",
                            value: "es_kit",
                            sortable: true,
                        },

                        {
                            text: "Campaña",
                            value: "campana",
                            sortable: true,
                        },
                        {
                            text: "Fecha Alta",
                            value: "fecha_alta",
                            sortable: true,
                        },
                    ];
                }
            }

            /*headers_6: ,*/
            return headers;
        },
        isloading: function () {
            return this.$store.getters.getloading;
        },
        role: function () {
            return localStorage.getItem("role");
        },
    },
};
</script>
<style>
.lead {
    background-color: rgb(255, 205, 205);
}
</style>
