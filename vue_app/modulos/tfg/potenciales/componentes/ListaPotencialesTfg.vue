<template>
    <v-card class="pa-3 ma-3">
        <v-toolbar flat color="#1d2735" dark>
            <v-icon class="white--text" style="font-size: 45px"
                >mdi-account-supervisor-circle</v-icon
            >
            <pre><v-toolbar-title><h2>Lista Potenciales</h2></v-toolbar-title></pre>
        </v-toolbar>

        <v-row>
            <v-col cols="12" md="4">
                <date-select label="Dia" v-model="dia"></date-select>
            </v-col>
        </v-row>
        <v-data-table
            :hide-default-footer="true"
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
            <template
                v-for="(item_h, index) in headers"
                v-slot:[`item.${item_h.text}`]="{ item }"
            >
                <template v-if="item.tipo == 'TOTAL'">
                    {{ item[item_h.text] }}
                </template>
                <v-text-field
                    v-else
                    outlined
                    dense
                    v-model="item[item_h.text].cantidad"
                    type="number"
                    @change="SavePotencial(item[item_h.text])"
                ></v-text-field>
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
        <Resumen v-if="user.rol_tfg == 1"></Resumen>
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
import FilterComponentVue from "../../../../components/general/FilterComponent.vue";
import Resumen from "../componentes/ResumenPotencialesTfg.vue";
export default {
    components: { FilterComponentVue, Resumen },
    data() {
        return {
            dia: new Date().toISOString().substr(0, 10),
            search: "",
            filtros_prueba: {
                search: "",
                tipos: {},
            },
            headers: [],
            form_dialog: false,
            gasto: {},
            gastos: [],
            selectedItem: 0,
            dialog: false,
        };
    },
    created() {
        this.getPotenciales();
        this.getHeaders();
    },
    watch: {
        dia: {
            handler(val) {
                this.getPotenciales();
            },
        },
    },
    methods: {
        SavePotencial(item) {
            const self = this;
            axios
                .post(`api/save-potenciales-tfg-headers`, item)
                .then(function (response) {
                    self.gastos = [];
                    self.gastos = response.data;
                });
        },
        getHeaders() {
            const self = this;
            axios
                .get(`api/get-potenciales-tfg-headers`)
                .then(function (response) {
                    self.headers = response.data;
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
        getPotenciales() {
            axios.get(`api/get-potenciales-tfg-dia/${this.dia}`).then(
                (res) => {
                    this.gastos = res.data;
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
                        this.getPotenciales();
                    },
                    (err) => {
                        this.$toast.error("Error eliminando Gasto");
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
