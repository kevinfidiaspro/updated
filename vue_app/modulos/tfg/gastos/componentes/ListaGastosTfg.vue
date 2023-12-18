<template>
    <v-card class="pa-3 ma-3">
        <v-toolbar flat color="#1d2735" dark>
            <v-icon class="white--text" style="font-size: 45px"
                >mdi-account-supervisor-circle</v-icon
            >
            <pre><v-toolbar-title><h2>Lista Gastos</h2></v-toolbar-title></pre>
        </v-toolbar>
        <v-tooltip right>
            <template v-slot:activator="{ on, attrs }">
                <v-btn
                    fab
                    @click="
                        form_dialog = true;
                        gasto = {};
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
            <span>Nuevo Gasto</span>
        </v-tooltip>

        <v-row>
            <v-col cols="12" md="8">
                <FilterComponentVue
                    :headers="filter_headers"
                    v-model="filtros_prueba"
                ></FilterComponentVue>
            </v-col>
            <v-col cols="12" md="4">
                <v-text-field
                    label="Total Mensual"
                    v-model="total"
                    readonly
                    outlined
                ></v-text-field>
                <v-text-field
                    label="Total Anual"
                    v-model="total_anual"
                    readonly
                    outlined
                ></v-text-field>
            </v-col>
        </v-row>
        <v-data-table
            dense
            :headers="headers"
            :items="gastos"
            :search="filtros_prueba.search"
            :items-per-page="15"
            item-key="id"
            class="elevation-1"
            :sort-by="['nombre']"
            :sort-desc="[false]"
        >
            <template v-slot:item.precio="{ item }">
                {{ item.precio }} €
            </template>

            <template v-slot:item.action="{ item }">
                <v-icon
                    @click="
                        () => {
                            gasto = item;
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
            ><FormGastosTfg
                @getTipos="getTipos"
                :tipos="tipos"
                @close_modal="
                    () => {
                        form_dialog = false;
                        getGastos();
                    }
                "
                v-model="gasto"
            ></FormGastosTfg
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
import FormGastosTfg from "./FormGastosTfg.vue";
import FilterComponentVue from "../../../../components/general/FilterComponent.vue";

export default {
    components: { FormGastosTfg, FilterComponentVue },
    data() {
        return {
            total_anual: 0,
            search: "",
            filtros_prueba: {
                search: "",
                tipos: {},
            },
            filter_headers: [
                {
                    title: "Tipo ",
                    type: "select",
                    active: false,
                    model: "tipos",
                    item_text: "nombre",
                    item_value: "id",
                    items: [],
                },
                {
                    title: "Fecha",
                    type: "date",
                    kind: "month",
                    active: true,
                    model: "mes",
                },
            ],
            headers: [
                { text: "Descripcion", value: "descripcion", sortable: false },
                { text: "Tipo", value: "tipo.nombre", sortable: false },
                { text: "Fecha", value: "mes", sortable: false },
                { text: "Euros", value: "euros", sortable: false },
                { text: "Acciones", value: "action", sortable: false },
            ],
            form_dialog: false,
            gasto: {},
            gastos: [],
            selectedItem: 0,
            dialog: false,
            tipos: [],
        };
    },
    created() {
        this.getGastos();
        this.getTipos();
    },
    watch: {
        "filtros_prueba.tipos": {
            deep: true,
            handler(val) {
                this.getGastos();
            },
        },
        "filtros_prueba.mes": {
            deep: true,
            handler(val) {
                this.getGastos();
            },
        },
    },
    methods: {
        getTipos() {
            const self = this;
            axios.get(`api/get-tfg-gastos-tipos`).then(function (response) {
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
        getGastos() {
            const filtros_prueba = this.filtros_prueba;
            const self = this;
            console.log(filtros_prueba);
            let search_str = "";
            if (filtros_prueba.tipos)
                if (filtros_prueba.tipos.value) {
                    search_str += "&id_tipo=" + filtros_prueba.tipos.value;
                }
            if (filtros_prueba.mes) {
                if (filtros_prueba.mes.start) {
                    search_str += "&inicio=" + filtros_prueba.mes.start;
                }
                if (filtros_prueba.mes.end) {
                    search_str += "&fin=" + filtros_prueba.mes.end;
                }
            }

            axios.get(`api/get-tfg-gastos?template=1${search_str}`).then(
                (res) => {
                    self.gastos = res.data.data;
                    self.total = res.data.total;
                    self.total_anual = res.data.total_anual;
                },
                (err) => {
                    this.$toast.error("Error consultando Gastos");
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
                .post("api/delete-tfg-gasto", {
                    id: this.selectedItem.id,
                })
                .then(
                    (res) => {
                        this.$toast.sucs("Gasto eliminado");
                        this.dialog = false;
                        this.getGastos();
                    },
                    (err) => {
                        this.$toast.error("Error eliminando Gasto");
                    }
                );
        },
    },
    computed: {
        isLoading: function () {
            return this.$store.getters.getloading;
        },
    },
};
</script>
