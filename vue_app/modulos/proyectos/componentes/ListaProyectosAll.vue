<template>
    <v-card class="pa-3 ma-3">
        <v-toolbar flat color="#1d2735" dark>
            <v-icon class="white--text" style="font-size: 45px"
                >mdi-account-supervisor-circle</v-icon
            >
            <pre><v-toolbar-title><h2>Lista Proyecto Cliente</h2></v-toolbar-title></pre>
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
                    :to="{ path: `/guardar-cliente` }"
                    :loading="isloading"
                    :disabled="isloading"
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
            <span>Nuevo Cliente</span>
        </v-tooltip>
        <v-row class="mx-2 my-2">
            <v-row>
                <v-col cols="6" justify="center">
                    <FilterComponentVue
                        :headers="filter_headers"
                        v-model="filtros_prueba"
                    ></FilterComponentVue>
                </v-col>
            </v-row>
            <!--  -->
            <v-row align-md="end" justify="end" class="mr-4 mb-2">
                <v-btn-toggle v-model="toggle_exclusive" rounded>
                    <v-btn
                        ><v-icon>mdi-filter-plus-outline</v-icon
                        ><strong>
                            <pre style="color: green"> Proy. Activos</pre>
                        </strong></v-btn
                    >
                    <v-btn
                        ><v-icon>mdi-filter-minus-outline</v-icon
                        ><strong>
                            <pre style="color: red"> Proy. Inactivos</pre>
                        </strong></v-btn
                    >
                    <v-btn
                        ><v-icon>mdi-filter</v-icon
                        ><strong>
                            <pre style="color: blue"> Todos</pre>
                        </strong></v-btn
                    >
                </v-btn-toggle>
            </v-row>
        </v-row>
        <!--  -->
        <v-data-table
            :item-class="
                () => {
                    return 'pointer';
                }
            "
            @click:row="
                (item) => {
                    $router.push(`/editar-proyecto?id=${item.id}`);
                }
            "
            dense
            :server-items-length="totalItems"
            :loading="loading"
            :headers="headers"
            :items="usuarios"
            :search="search"
            :items-per-page="15"
            item-key="id"
            class="elevation-1"
            :sort-by="['nombre']"
            :sort-desc="[false]"
            @update:options="loadItems"
        >
            <template v-slot:item.tipo="{ item }">
                <span v-if="item.semanal == 0">Único</span>
                <span v-if="item.semanal == 1">Semanal</span>
                <span v-if="item.semanal == 2">Mensual</span>
            </template>
            <template v-slot:item.last_active_state.estado.nombre="{ item }">
                <template v-if="item.last_active_state != null">
                    <span v-if="item.last_active_state.estado != null">{{
                        item.last_active_state.estado.nombre
                    }}</span>
                    <span v-else>{{ item.last_active_state.descripcion }}</span>
                </template>
            </template>
            <template v-slot:item.fecha_alta="{ item }">
                <span>{{ item.fecha_alta }}</span>
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
import FilterComponentVue from "../../../components/general/FilterComponent.vue";
import { servicios_mixin } from "../../../global_mixins/servicios_mixin";

export default {
    mixins: [servicios_mixin],

    components: {
        FilterComponentVue,
    },
    data() {
        return {
            filtros_prueba: {
                search: "",
            },
            filter_headers: [
                {
                    title: "Tipo de Proyecto",
                    type: "select",
                    active: false,
                    model: "semana",
                    item_text: "nombre",
                    item_value: "id",
                    items: [
                        {
                            id: 0,
                            nombre: "Unico",
                        },
                        {
                            id: 1,
                            nombre: "Semanal",
                        },
                        {
                            id: 3,
                            nombre: "Mensual",
                        },
                    ],
                },
                {
                    title: "Producto Contratado",
                    type: "select",
                    active: false,
                    model: "servicio",
                    item_text: "nombre",
                    item_value: "id",
                    items: [],
                },
                {
                    title: "Estado Proyecto",
                    type: "select",
                    active: false,
                    model: "estado",
                    item_text: "nombre",
                    item_value: "id",
                    items: [],
                },
            ],
            search: "",
            loading: false,
            headers: [
                { text: "Proyecto", value: "nombre", sortable: false },

                { text: "Cliente", value: "usuario.nombre", sortable: false },
                {
                    text: "Nombre Fiscal",
                    value: "usuario.nombre_fiscal",
                    sortable: false,
                },
                {
                    text: "Nombre Comercial",
                    value: "usuario.nombre_comercial",
                    sortable: false,
                },
                { text: "Email", value: "usuario.email", sortable: false },
                {
                    text: "Telefono",
                    value: "usuario.telefono",
                    sortable: false,
                },
                {
                    text: "Estado",
                    value: "last_active_state.estado.nombre",
                    sortable: false,
                },
                {
                    text: "Tipo",
                    value: "tipo",
                    sortable: false,
                },
                {
                    text: "Fecha Alta",
                    value: "fecha_alta",
                    sortable: false,
                },
                { text: "Acciones", value: "action", sortable: false },
            ],
            usuarios: [],
            selectedItem: 0,
            totalItems: 67,
            page: 1,
            itemsPerPage: 15,
            dialog: false,
            toggle_exclusive: 0,
        };
    },
    created() {
        this.getEstados();

        this.getActiveClientes();
    },
    mounted() {
        this.filter_headers[1].items = this.servicios;
    },
    watch: {
        servicios(val) {
            this.filter_headers[1].items = val;
        },
        search: debounce(function (n) {
            this.getActiveClientes();
        }, 500),
        filtros_prueba: {
            deep: true,
            handler: debounce(function (n) {
                this.getActiveClientes();
            }, 500),
        },
        toggle_exclusive(n) {
            /*if (this.toggle_exclusive == 0) {
                this.getActiveClientes();
            } else if (this.toggle_exclusive == 1) {
                this.getInactiveClientes();
            } else if (this.toggle_exclusive == 2) {
                this.getAllClientes();
            } else {
                this.usuarios = [];
            }*/
            this.getActiveClientes();
        },
    },
    methods: {
        getEstados() {
            axios.get("api/get-proyecto-estados").then((res) => {
                this.filter_headers[2].items = res.data;
            });
        },
        loadItems({ page, itemsPerPage, sortBy }) {
            this.page = page;
            this.itemsPerPage = itemsPerPage;
            this.getActiveClientes();
        },
        getAllClientes() {
            axios.get(`api/get-all-clientes`).then(
                (res) => {
                    this.usuarios = res.data.users;
                    for (let i = 0; i < this.usuarios.length; i++) {
                        const element = this.usuarios[i];
                        element.created_at = new Date(
                            element.created_at
                        ).toLocaleDateString();
                    }
                },
                (err) => {
                    this.$toast.error("Error consultando Usuario");
                }
            );
        },
        getActiveClientes() {
            console.log(this.filtros_prueba);
            let params = "";
            if (
                (this.filtros_prueba.semana.value != null &&
                    this.filtros_prueba.semana.value != "") ||
                this.filtros_prueba.semana.value == 0
            ) {
                params += `&semana=${this.filtros_prueba.semana.value}`;
            }
            if (
                this.filtros_prueba.servicio.value != null &&
                this.filtros_prueba.servicio.value != ""
            ) {
                params += `&servicio=${this.filtros_prueba.servicio.value}`;
            }
            if (
                this.filtros_prueba.estado.value != null &&
                this.filtros_prueba.estado.value != ""
            ) {
                params += `&estado=${this.filtros_prueba.estado.value}`;
            }
            if (this.toggle_exclusive == 0) {
                params += "&activo=1";
            } else if (this.toggle_exclusive == 1) {
                params += "&activo=0";
            }
            if (
                this.filtros_prueba.search != null &&
                this.filtros_prueba.search != ""
            ) {
                params += "&search=" + this.filtros_prueba.search;
            }

            this.loading = true;
            axios
                .get(
                    `api/get-cliente-proyectos?itemsPerPage=${this.itemsPerPage}&page=${this.page}${params}`
                )
                .then(
                    (res) => {
                        this.usuarios = res.data.data;
                        this.totalItems = res.data.total;
                        console.log(this.totalItems);
                        this.loading = false;

                        for (let i = 0; i < this.usuarios.length; i++) {
                            const element = this.usuarios[i];
                            element.created_at = new Date(
                                element.created_at
                            ).toLocaleDateString();
                        }
                    },
                    (err) => {
                        this.$toast.error("Error consultando Usuario");
                    }
                );
        },
        getInactiveClientes() {
            axios.get(`api/get-all-clientes-inactive-proyectos`).then(
                (res) => {
                    this.usuarios = res.data.users;
                    for (let i = 0; i < this.usuarios.length; i++) {
                        const element = this.usuarios[i];
                        element.created_at = new Date(
                            element.created_at
                        ).toLocaleDateString();
                    }
                },
                (err) => {
                    this.$toast.error("Error consultando Usuario");
                }
            );
        },
        openModal(item) {
            this.selectedItem = this.usuarios.indexOf(item);
            this.dialog = true;
        },
        deleteUser() {
            axios
                .get(
                    `api/delete-proyecto/${this.usuarios[this.selectedItem].id}`
                )
                .then(
                    (res) => {
                        this.$toast.sucs("Usuario eliminado");
                        this.dialog = false;
                        this.getActiveClientes();
                    },
                    (err) => {
                        this.$toast.error("Error eliminando Usuario");
                    }
                );
            this.getUsuarios();
        },
        verProyectos() {
            axios
                .get(
                    `api/delete-usuario/${this.usuarios[this.selectedItem].id}`
                )
                .then(
                    (res) => {
                        this.$toast.sucs("Usuario eliminado");
                        this.dialog = false;
                    },
                    (err) => {
                        this.$toast.error("Error eliminando Usuario");
                    }
                );
            this.getUsuarios();
        },
    },
    computed: {
        isloading: function () {
            return this.$store.getters.getloading;
        },
    },
};
</script>
