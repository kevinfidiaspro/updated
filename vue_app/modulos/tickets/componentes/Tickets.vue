<template>
    <v-card class="pa-3 ma-3">
        <v-toolbar flat color="#1d2735" dark>
            <v-icon class="white--text" style="font-size: 45px"
                >mdi-ticket-account</v-icon
            >
            <pre><v-toolbar-title><h2>Tickets</h2></v-toolbar-title></pre>
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
                    :to="{ path: `/guardar-ticket` }"
                    :loading="isloading"
                    :disabled="isloading"
                    color="orange darken-1"
                    class="mt-2"
                    v-bind="attrs"
                    v-on="on"
                >
                    <v-icon class="white--text"
                        >mdi-plus-box</v-icon
                    >
                </v-btn>
            </template>
            <span>Nuevo Ticket</span>
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
        </v-row>
        <!--  -->
        <v-data-table
            :item-class="row_classes"
            @click:row="
                (item) => {
                    $router.push(`/editar-ticket?id=${item.id}`);
                }
            "
            dense
            :server-items-length="totalItems"
            :loading="loading"
            :headers="headers"
            :items="tickets"
            :search="search"
            :items-per-page="15"
            item-key="id"
            class="elevation-1"
            :sort-by="['nombre']"
            :sort-desc="[true]"
            @update:options="loadItems"
        >
            <template v-slot:item.action="{ item }">
                <v-icon
                    @click.stop="openModal(item)"
                    small
                    class="mr-2"
                    :color="item.id_urgencia == 1 || item.fecha_finalizacion == fecha_hoy ? 'white' : 'red'"
                    style="font-size: 25px; background-color: transparent !important;"
                    title="BORRAR"
                >
                    mdi-trash-can
                </v-icon>
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
                    <v-btn color="success" large @click="deleteTicket">Confirmar</v-btn>
                    <v-spacer></v-spacer>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-card>
</template>
<script>
import debounce from "lodash/debounce";
import FilterComponentVue from "../../../components/general/FilterComponent.vue";
// import { servicios_mixin } from "../../../global_mixins/servicios_mixin";

export default {
    mixins: [ ],

    components: {
        FilterComponentVue,
    },
    data() {
        return {
            filtros_prueba: {
                search: "",
                estado: {},
                departamento: {},
                urgencia: {},
                cliente: {},
                responsable: {},
            },
            filter_headers: [
                {
                    title: "Fecha de creación",
                    type: "date",
                    active: true,
                    model: "fecha"
                },
                {
                    title: "Fecha de finalización",
                    type: "date",
                    active: true,
                    model: "fecha_fianlizacion"
                },
                {
                    title: "Estado",
                    type: "select",
                    active: false,
                    model: "estado",
                    item_text: "descripcion",
                    item_value: "id",
                    items: [],
                },
                {
                    title: "Departamento",
                    type: "select",
                    active: false,
                    model: "departamento",
                    item_text: "descripcion",
                    item_value: "id",
                    items: [],
                },
                {
                    title: "Urgencia",
                    type: "select",
                    active: false,
                    model: "urgencia",
                    item_text: "descripcion",
                    item_value: "id",
                    items: [],
                },
                {
                    title: "Cliente",
                    type: "select",
                    active: false,
                    model: "cliente",
                    item_text: "nombre",
                    item_value: "id",
                    items: [],
                }
            ],
            search: "",
            loading: false,
            headers: [
                { text: "Ticket", value: "id", sortable: false },
                { text: "Cliente", value: "cliente", sortable: false },
                { text: "Descripción", value: "descripcion", sortable: false },
                { text: "Departamento", value: "departamento", sortable: false, },
                { text: "Estado", value: "estado", sortable: false, },
                { text: "Responsable", value: "responsable", sortable: false, },
                { text: "Fecha de creación", value: "created_at", sortable: false },
                { text: "Fecha de finalización", value: "fecha_finalizacion", sortable: false },
                { text: "Urgenica", value: "urgencia", sortable: false },
                { text: "Acciones", value: "action", sortable: false },
            ],
            tickets: [],
            selectedItem: 0,
            totalItems: 67,
            page: 1,
            itemsPerPage: 15,
            dialog: false,
            toggle_exclusive: 0,
            fecha_hoy: null,
        };
    },
    created() {
        this.getEstados();
        this.getDepartamentos();
        this.getUrgencia();
        this.getClientes();
        this.getAllTickets();
        this.fecha_hoy = this.formatDate(new Date());

        if(localStorage.role == 1){
            this.filter_headers.push(
                {
                    title: "Responsable",
                    type: "select",
                    active: false,
                    model: "responsable",
                    item_text: "nombre",
                    item_value: "id",
                    items: [],
                }
            )
            this.getResponsables();
        }
    },
    mounted() {
        // 
    },
    watch: {
        search: debounce(function (n) {
            this.getAllTickets();
        }, 500),

        filtros_prueba: {
            deep: true,
            handler: debounce(function (n) {
                this.getAllTickets();
            }, 500),
        }
    },
    methods: {
        getEstados() {
            axios.get("api/get-estado-tickets").then((res) => {
                this.filter_headers[2].items = res.data.success;
            });
        },
        getDepartamentos() {
            axios.get("api/get-departamentos").then((res) => {
                this.filter_headers[3].items = res.data.success;
            });
        },
        getUrgencia() {
            axios.get("api/get-urgencia").then((res) => {
                this.filter_headers[4].items = res.data.success;
            });
        },
        getResponsables() {
            axios.post("api/get-usuarios-empleados-all").then((res) => {
                this.filter_headers[6].items = res.data.users;
            });
        },
        getClientes() {
            axios.get("api/get-all-clientes").then((res) => {
                this.filter_headers[5].items = res.data.users;
            });
        },

        loadItems({ page, itemsPerPage, sortBy }) {
            this.page = page;
            this.itemsPerPage = itemsPerPage;
            this.getAllTickets();
        },
        // getTickets() {
        //     axios.get(`api/get-tickets`).then(
        //         (res) => {
        //             this.tickets = res.data.success;
        //             console.log('tickets')
        //             console.log(res)
        //             // for (let i = 0; i < this.usuarios.length; i++) {
        //             //     const element = this.usuarios[i];
        //             //     element.created_at = new Date(
        //             //         element.created_at
        //             //     ).toLocaleDateString();
        //             // }
        //         },
        //         (err) => {
        //             this.$toast.error("Error consultando Usuario");
        //         }
        //     );
        // },
        getAllTickets() {
            let params = "";

            if (this.filtros_prueba.fecha) {
                if (this.filtros_prueba.fecha.start) {
                    params += `&fecha_inicio=${this.filtros_prueba.fecha.start}`;
                }
                if (this.filtros_prueba.fecha.end) {
                    params += `&fecha_fin=${this.filtros_prueba.fecha.end}`;
                }
            }

            if (this.filtros_prueba.fecha_fianlizacion) {
                if (this.filtros_prueba.fecha_fianlizacion.start) {
                    params += `&fecha_fianlizacion_inicio=${this.filtros_prueba.fecha_fianlizacion.start}`;
                }
                if (this.filtros_prueba.fecha_fianlizacion.end) {
                    params += `&fecha_fianlizacion_fin=${this.filtros_prueba.fecha_fianlizacion.end}`;
                }
            }

            if (
                this.filtros_prueba.estado.value != null &&
                this.filtros_prueba.estado.value != ""
            ) {
                params += `&estado=${this.filtros_prueba.estado.value}`;
            }

            if (
                this.filtros_prueba.departamento.value != null &&
                this.filtros_prueba.departamento.value != ""
            ) {
                params += `&departamento=${this.filtros_prueba.departamento.value}`;
            }

            if (
                this.filtros_prueba.urgencia.value != null &&
                this.filtros_prueba.urgencia.value != ""
            ) {
                params += `&urgencia=${this.filtros_prueba.urgencia.value}`;
            }

            if (
                this.filtros_prueba.cliente.value != null &&
                this.filtros_prueba.cliente.value != ""
            ) {
                params += `&cliente=${this.filtros_prueba.cliente.value}`;
            }

            if (
                this.filtros_prueba.responsable.value != null &&
                this.filtros_prueba.responsable.value != ""
            ) {
                params += `&responsable=${this.filtros_prueba.responsable.value}`;
            }
            
            if (
                this.filtros_prueba.search != null &&
                this.filtros_prueba.search != ""
            ) {
                params += "&search=" + this.filtros_prueba.search;
            }

            this.loading = true;
            axios
                .get(`api/get-tickets?itemsPerPage=${this.itemsPerPage}&page=${this.page}${params}`)
                .then(
                    (res) => {
                        let response = res.data

                        this.tickets = response.data;
                        this.totalItems = response.meta.total;
                        this.loading = false;
                        // this.fecha_hoy = new Date();
                    },
                    (err) => {
                        this.$toast.error("Error consultando Usuario");
                    }
                );
        },
        openModal(item) {
            this.selectedItem = this.tickets.indexOf(item);
            this.dialog = true;
        },
        deleteTicket() {
            axios
                .get(`api/delete-ticket/${this.tickets[this.selectedItem].id}`)
                .then( 
                    (response)=> {
                        this.$toast.sucs("Ticket eliminado");
                        this.dialog = false;
                        this.getAllTickets();
                    },
                    (err) => {
                        this.$toast.error("Error eliminando Ticket");
                    }
                );
        },

        row_classes(item) {
            if (item.id_urgencia == 1 || item.fecha_finalizacion == this.fecha_hoy) {
                return "red-row pointer";
            }else{
                return "pointer";
            }
        },

        formatDate(date) {
            const day = date.getDate().toString().padStart(2, '0'); // Obtener el día con ceros a la izquierda
            const month = (date.getMonth() + 1).toString().padStart(2, '0'); // Obtener el mes con ceros a la izquierda
            const year = date.getFullYear();

            return `${day}/${month}/${year}`;
        }
    },
    computed: {
        isloading: function () {
            return this.$store.getters.getloading;
        },
    },
};
</script>

<style>
    .red-row {
        background-color: red;
        color: white;
    }
</style>
