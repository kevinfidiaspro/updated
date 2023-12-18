<template>
    <v-card class="pa-3 ma-3">
        <v-toolbar flat color="#1d2735" dark>
            <v-icon class="white--text" style="font-size: 45px">
                mdi-cash-minus
            </v-icon>
            <pre><v-toolbar-title><h2> Lista Gastos</h2></v-toolbar-title></pre>
        </v-toolbar>
        <loader v-if="isloading"></loader>

        <v-tooltip right>
            <template v-slot:activator="{ on, attrs }">
                <v-btn
                    fab
                    @click="
                        gasto = {};
                        form_dialog = true;
                    "
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
            <span>Nuevo Gasto</span>
        </v-tooltip>
        <v-row>
            <v-col cols="12" md="3"
                ><date-select
                    v-model="fecha_inicio"
                    label="Fecha de inicio"
                ></date-select
            ></v-col>
            <v-col cols="12" md="3"
                ><date-select
                    v-model="fecha_fin"
                    label="Fecha Fin"
                ></date-select
            ></v-col>
            <v-col cols="12" md="3">
                <v-text-field
                    label="Total Mensual"
                    v-model="total"
                    readonly
                    outlined
                ></v-text-field>
            </v-col>
            <v-col cols="12" md="3">
                <v-text-field
                    label="Total Anual"
                    v-model="total_anual"
                    readonly
                    outlined
                ></v-text-field>
            </v-col>
            <v-col cols="12" md="4">
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
            :headers="headers"
            :items="gastos"
            :items-per-page="10"
            item-key="id"
            class="elevation-1"
            :item-class="
                () => {
                    return 'pointer';
                }
            "
            @click:row="
                (item) => {
                    this.gasto = JSON.parse(JSON.stringify(item));
                    this.form_dialog = true;
                    //$router.push(`/update-gasto/` + item.id);
                }
            "
        >
            <template v-slot:item.descripcion="{ item }">
                <template v-if="item.descripcion != null">
                    {{
                        item.descripcion.length > 20
                            ? `${item.descripcion.substr(0, 20)}...`
                            : item.descripcion
                    }}
                </template>
            </template>
            <template v-slot:item.created_at="{ item }">
                {{ item.created_at.substr(0, 10) }}
            </template>
            <template v-slot:item.fecha="{ item }">
                {{ item.fecha | format_date }}
            </template>
            <template v-slot:item.action="{ item }">
                <a
                    v-if="item.archivo != null"
                    target="_blank"
                    @click.stop="down(item)"
                >
                    <v-icon medium color="orange" class="mr-2">
                        mdi-cloud-download
                    </v-icon>
                </a>

                <v-icon
                    @click.stop="openModal(item)"
                    small
                    class="mr-2"
                    color="red"
                    style="font-size: 25px"
                    title="BORRAR"
                >
                    mdi-trash-can
                </v-icon>
            </template>
        </v-data-table>
        <ListaResumenGastos></ListaResumenGastos>
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
                    <v-btn color="success" large @click="deleteGasto()"
                        >Confirmar</v-btn
                    >
                    <v-spacer></v-spacer>
                </v-card-actions>
            </v-card>
        </v-dialog>
        <v-dialog v-model="form_dialog" max-width="700px">
            <FormGasto @Saved="getGastos()" v-model="gasto"></FormGasto>
        </v-dialog>
    </v-card>
</template>

<script>
import { date_mixin } from "../mixins/date_mixin";
import ListaResumenGastos from "./ListaResumenGastos.vue";
import rangoFechas from "../rangoFechas";
import FormGasto from "./FormGasto.vue";
export default {
    mixins: [date_mixin],

    components: {
        rangoFechas,
        ListaResumenGastos,
        FormGasto,
    },

    data() {
        return {
            totalItems: 0,
            gasto: {},
            form_dialog: false,
            total: 0,
            total_anual: 0,
            fecha_inicio: null,
            fecha_fin: null,
            url: `api/get-gastos`,
            search: "",
            gastos: [],
            selectedItem: 0,
            dialog: false,
            filtro: { page: 1, itemsPerPage: 15 },
            headers: [
                {
                    text: "Descripcion",
                    align: "left",
                    value: "descripcion",
                },
                {
                    text: "Fecha",
                    value: "fecha",
                    filterable: false,
                },
                {
                    text: "Importe",
                    value: "importe",
                },
                {
                    text: "Tipo",
                    value: "tiposgasto.nombre",
                },
                {
                    text: "Acciones",
                    value: "action",
                    sortable: false,
                },
            ],
        };
    },
    created() {
        // Get the current date
        const currentDate = new Date();

        // Get the year and month of the current date
        const year = currentDate.getFullYear();
        const month = currentDate.getMonth() + 1;

        // Create a new Date object for the first day of the month
        const first = new Date(year, month, 1);

        // Create a new Date object for the last day of the month
        const lastDayOfMonth = new Date(year, month, 0, 23, 59, 59);
        //console.log(`${first.getFullYear()}-${first.getMonth()}-1`);
        // Format the first and last day as strings (e.g., "YYYY-MM-DD")
        this.fecha_inicio = `${year}-${first
            .getMonth()
            .toString()
            .padStart(2, "0")}-01`;
        this.fecha_fin = lastDayOfMonth.toISOString().split("T")[0];
    },
    mounted() {
        // this.$emit('hacer_busqueda')
        this.getGastos();
    },
    watch: {
        fecha_inicio(val) {
            this.getGastos();
        },
        fecha_fin(val) {
            this.getGastos();
        },
    },
    methods: {
        loadItems({ page, itemsPerPage, sortBy }) {
            if (this.filtro == null) {
                this.filtro = this.$store.getters.get_filtros_potenciales;
            }
            this.filtro.page = page;
            this.filtro.itemsPerPage = itemsPerPage;
            this.getGastos();
        },
        getGastos() {
            axios
                .get(
                    this.url +
                        `?page=${this.filtro.page}&amount=${this.filtro.itemsPerPage}&fecha_inicio=${this.fecha_inicio}&fecha_fin=${this.fecha_fin}`
                )
                .then(
                    (res) => {
                        this.gastos = res.data.gastos.data;
                        this.total = res.data.total_mes;
                        this.total_anual = res.data.total_year;
                        this.totalItems = res.data.gastos.total;
                        //console.log(this.gastos);
                    },
                    (res) => {
                        this.$toast.error("Error consultando Gasto");
                    }
                );
        },

        setGastos(data) {
            if (data.length > 0) {
                this.gastos = data;
                return;
            }
            this.gastos = [];
            this.$toast.sucs("No se encontraron registros");
        },

        deleteGasto() {
            this.dialog = false;
            axios
                .get(`api/delete-gasto/${this.gastos[this.selectedItem].id}`)
                .then(
                    (res) => {
                        this.gastos.splice(this.selectedItem, 1);
                        this.$toast.sucs("Gasto eliminado con exito");
                    },
                    (err) => {
                        this.$toast.error("Error Eliminando gasto");
                    }
                );
        },

        down(item) {
            let downloadPath =
                "/storage/documentos/userId_" +
                localStorage.user_id +
                "/factura_recibidas/" +
                item.archivo;
            this.downloadFiles(downloadPath, item.archivo);
        },
        downloadFiles(url, filename) {
            fetch(url).then(function (t) {
                return t.blob().then((b) => {
                    var a = document.createElement("a");
                    a.href = URL.createObjectURL(b);
                    a.setAttribute("download", filename);
                    a.click();
                });
            });
        },
        openModal(item) {
            this.selectedItem = this.gastos.indexOf(item);
            this.dialog = true;
        },
    },
    computed: {
        isloading: function () {
            return this.$store.getters.getloading;
        },
        /* total: function () {
            let total = this.gastos.reduce((acc, gasto) => {
                return acc + gasto.importe;
            }, 0);

            return parseFloat(total).toFixed(2);
        },*/
        userId() {
            return localStorage.getItem("user_id");
        },
    },
};
</script>
