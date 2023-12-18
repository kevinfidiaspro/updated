<template>
    <v-card style="margin-top: 2rem">
        <v-toolbar flat color="#1d2735" dark>
            <v-icon class="white--text" style="font-size: 45px">{{
                icon
            }}</v-icon>
            <pre><v-toolbar-title><h2>Resumen {{ title }}</h2></v-toolbar-title></pre>
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
            <template v-slot:item.enero="{ item }"
                ><div :style="CalculateColor(item, 0)">
                    {{ item.enero }}
                </div></template
            >
            <template v-slot:item.febrero="{ item }"
                ><div :style="CalculateColor(item, 1)">
                    {{ item.febrero }}
                </div></template
            >
            <template v-slot:item.marzo="{ item }"
                ><div :style="CalculateColor(item, 2)">
                    {{ item.marzo }}
                </div></template
            >
            <template v-slot:item.abril="{ item }"
                ><div :style="CalculateColor(item, 3)">
                    {{ item.abril }}
                </div></template
            >
            <template v-slot:item.mayo="{ item }"
                ><div :style="CalculateColor(item, 4)">
                    {{ item.mayo }}
                </div></template
            >
            <template v-slot:item.junio="{ item }"
                ><div :style="CalculateColor(item, 5)">
                    {{ item.junio }}
                </div></template
            >
            <template v-slot:item.julio="{ item }"
                ><div :style="CalculateColor(item, 6)">
                    {{ item.julio }}
                </div></template
            >
            <template v-slot:item.agosto="{ item }"
                ><div :style="CalculateColor(item, 7)">
                    {{ item.agosto }}
                </div></template
            >
            <template v-slot:item.septiembre="{ item }"
                ><div :style="CalculateColor(item, 8)">
                    {{ item.septiembre }}
                </div></template
            >
            <template v-slot:item.octubre="{ item }"
                ><div :style="CalculateColor(item, 9)">
                    {{ item.octubre }}
                </div></template
            >
            <template v-slot:item.noviembre="{ item }"
                ><div :style="CalculateColor(item, 10)">
                    {{ item.noviembre }}
                </div></template
            >
            <template v-slot:item.diciembre="{ item }"
                ><div :style="CalculateColor(item, 11)">
                    {{ item.diciembre }}
                </div></template
            >

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
    props: ["gastos", "title", "icon"],
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
                { text: "Año", value: "year", sortable: false },
                { text: "ENERO", value: "enero", sortable: false },
                { text: "FEBRERO", value: "febrero", sortable: false },
                { text: "MARZO", value: "marzo", sortable: false },
                { text: "ABRIL", value: "abril", sortable: false },
                { text: "MAYO", value: "mayo", sortable: false },
                { text: "JUNIO", value: "junio", sortable: false },
                { text: "JULIO", value: "julio", sortable: false },
                { text: "AGOSTO", value: "agosto", sortable: false },
                { text: "SEPTIEMBRE", value: "septiembre", sortable: false },
                { text: "OCTUBRE", value: "octubre", sortable: false },
                { text: "NOVIEMBRE", value: "noviembre", sortable: false },
                { text: "DICIEMBRE", value: "diciembre", sortable: false },
            ],
            form_dialog: false,
            gasto: {},
            //gastos: [],
            selectedItem: 0,
            dialog: false,
            tipos: [],
        };
    },
    created() {
        //this.getGastos();
        //this.generateYears();
    },
    watch: {
        year: {
            handler(val) {
                this.getGastos();
            },
        },
    },
    methods: {
        CalculateColor(item, monthIndex) {
            let result = `padding:1rem;`;
            const currentDate = new Date();
            const currentYear = currentDate.getFullYear();
            // Check if the year is different
            const index = this.gastos.indexOf(item);
            console.log(index);
            if (item.year !== currentYear) {
                result += "background-color:transparent;color:black";
                return result;
            }
            const spanishMonths = [
                "enero",
                "febrero",
                "marzo",
                "abril",
                "mayo",
                "junio",
                "julio",
                "agosto",
                "septiembre",
                "octubre",
                "noviembre",
                "diciembre",
            ];

            let currentMonthValue = 0;
            if (item[spanishMonths[monthIndex]] != null) {
                currentMonthValue = parseInt(
                    item[spanishMonths[monthIndex]].toString()
                );
            }

            // If the current month is January, there is no previous month in the current year

            let previousMonthValue = 0;

            if (index - 1 >= 0) {
                if (this.gastos[index - 1][spanishMonths[monthIndex]] != null) {
                    previousMonthValue = parseInt(
                        this.gastos[index - 1][
                            spanishMonths[monthIndex]
                        ].toString()
                    );
                }

                console.log(
                    `month=${spanishMonths[monthIndex]},current=${currentMonthValue}, other=${previousMonthValue}`
                );
            }
            if (currentMonthValue == 0) {
            } else if (currentMonthValue > previousMonthValue) {
                result += "background-color:green;color:white";
            } else if (currentMonthValue < previousMonthValue) {
                result += "background-color:red;color:white";
            } else {
                result += "background-color:transparent;color:black"; // Default if values are equal or if there's another condition you want
            }
            return result;
        },
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
