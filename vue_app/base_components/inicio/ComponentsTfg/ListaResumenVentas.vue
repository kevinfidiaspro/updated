<template>
    <v-card style="margin-top: 2rem">
        <v-toolbar flat color="#1d2735" dark>
            <v-icon class="white--text" style="font-size: 45px"
                >mdi-point-of-sale</v-icon
            >
            <pre><v-toolbar-title><h2>Resumen Ventas</h2></v-toolbar-title></pre>
        </v-toolbar>

        <v-data-table
            hide-default-footer
            dense
            :headers="headers"
            :items="gastos"
            :search="search"
            :items-per-page="15"
            item-key="id"
            class="elevation-1"
            :sort-by="['nombre']"
            :sort-desc="[false]"
        >
            <template v-slot:item.precio="{ item }">
                {{ item.precio }} €
            </template>
            <template v-slot:item.precio_no_iva="{ item }">
                {{ (item.precio / 1.21).toFixed(2) }} €
            </template>
            <template v-slot:item.presupuesto="{ item }">
                {{ item.presupuesto }} €
            </template>
            <template v-slot:item.gasto="{ item }">
                {{ item.gasto }} €
            </template>
            <template v-slot:item.beneficio="{ item }">
                {{ (item.precio / 1.21 - item.gasto).toFixed(2) }} €
            </template>
            <template v-slot:item.mes="{ item }">
                {{ meses[item.mes] }} - {{ item.year }}
            </template>
            <template v-slot:item.total_sin_iva="{ item }">
                <template v-if="item.total_sin_iva != null">
                    {{ item.total_sin_iva.toFixed(2) }} €
                </template>
                <template v-else>0 €</template>
            </template>

            <template v-slot:item.total="{ item }">
                <template v-if="item.total != null">
                    {{ item.total.toFixed(2) }} €
                </template>
                <template v-else>0 €</template>
            </template>
            <template v-slot:item.gasto="{ item }">
                {{ item.gasto }} €
            </template>
            <template v-slot:item.beneficio="{ item }">
                {{ item.beneficio.toFixed(2) }} €
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
export default {
    data() {
        return {
            meses: [
                "0",
                "Enero",
                "Febrero",
                "Marzo",
                "Abril",
                "Mayo",
                "Junio",
                "Julio",
                "Agosto",
                "Septiembre",
                "Octubre",
                "Noviembre",
                "Diciembre",
            ],
            search: "",
            year: new Date().getFullYear(),
            years: [],

            headers: [
                { text: "Dia", value: "dia", sortable: false },
                { text: "Código", value: "codigo", sortable: false },

                { text: "Precio", value: "precio", sortable: false },
                {
                    text: "Precio sin IVA",
                    value: "precio_no_iva",
                    sortable: false,
                },
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
        this.generateYears();
    },
    watch: {
        year: {
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
                years.push({ text: year, value: year });
            }
            this.years = years;
        },
        getGastos() {
            const self = this;
            let today = new Date();
            if (today.getDay() == 6) today.setDate(today.getDate() - 1);
            if (today.getDay() == 0) today.setDate(today.getDate() - 2);
            // Adjust to make sure it's not a weekend (Saturday: 6, Sunday: 0)
            const dayOfWeek = today.getDay();
            const daysToSubtract = dayOfWeek >= 3 ? 2 : 4; // If it's Monday, subtract 3 days, otherwise subtract 5 days

            const threeWeekdaysAgo = new Date(today);
            threeWeekdaysAgo.setDate(today.getDate() - daysToSubtract);
            let search_str = "";

            search_str += "&fecha_fin=" + today.toISOString().split("T")[0];

            search_str +=
                "&fecha_inicio=" + threeWeekdaysAgo.toISOString().split("T")[0];

            axios.get(`api/get-ventas-tfg?page=1&amount=-1${search_str}`).then(
                (res) => {
                    self.gastos = res.data.data;
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
                        this.$toast.sucs("Venta eliminada");
                        this.dialog = false;
                        this.getGastos();
                    },
                    (err) => {
                        this.$toast.error("Error eliminando Venta");
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
