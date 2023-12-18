<template>
    <v-container>
        <template v-if="user.role == 1">
            <v-tabs>
                <v-tab>TFG</v-tab>
                <v-tab>Fidias</v-tab>
                <v-tab-item key="1">
                    <v-row>
                        <v-col cols="12">
                            <ListaResumenVentasTfg></ListaResumenVentasTfg>
                        </v-col>
                        <v-col cols="12"
                            ><ResumenPotenciales></ResumenPotenciales
                        ></v-col>
                        <v-col cols="12">
                            <GastosCaja
                                :gastos="ventas"
                                icon="mdi-point-of-sale"
                                title="Ventas"
                            ></GastosCaja>
                        </v-col>
                        <v-col cols="12" md="12">
                            <listaMarketingTFG></listaMarketingTFG>
                        </v-col>
                        <v-col cols="12">
                            <v-card>
                                <v-card-title> V-G Anual </v-card-title>
                                <v-row
                                    ><v-col cols="6" md="3">
                                        <date-select
                                            type="month"
                                            label="Desde"
                                            v-model="desde"
                                        ></date-select></v-col
                                    ><v-col cols="6" md="3"
                                        ><date-select
                                            type="month"
                                            label="Hasta"
                                            v-model="hasta"
                                        ></date-select></v-col
                                ></v-row>
                                <apexchart
                                    height="230"
                                    width="99%"
                                    type="line"
                                    :options="ChartOptions(this.labels)"
                                    :series="formattedData"
                                />
                            </v-card>
                        </v-col>
                        <v-col cols="12" md="6">
                            <v-card>
                                <v-card-title>
                                    Ventas Últimos 3 Años
                                </v-card-title>

                                <apexchart
                                    height="230"
                                    width="99%"
                                    type="line"
                                    :options="ChartOptions(this.meses)"
                                    :series="
                                        formattedDataYear(esta_gastos_year)
                                    "
                                />
                            </v-card>
                        </v-col>
                        <v-col cols="12" md="6">
                            <v-card>
                                <v-card-title>
                                    Gastos Últimos 3 Años
                                </v-card-title>

                                <apexchart
                                    height="230"
                                    width="99%"
                                    type="line"
                                    :options="ChartOptions(this.meses)"
                                    :series="
                                        formattedDataYear(esta_ventas_gastos)
                                    "
                                />
                            </v-card>
                        </v-col>

                        <v-col cols="12" md="4"></v-col>
                    </v-row>
                </v-tab-item>
                <v-tab-item key="2">
                    <v-row>
                        <v-col cols="12" md="4">
                            <ListaResumenVentas></ListaResumenVentas>
                        </v-col>
                        <v-col cols="12" md="4">
                            <ListaResumenGastos></ListaResumenGastos>
                        </v-col>
                        <v-col cols="12" md="12">
                            <ListaSeguimiento></ListaSeguimiento>
                        </v-col>
                        <v-col cols="12">
                            <v-card>
                                <v-card-title> V-G Anual </v-card-title>
                                <v-row
                                    ><v-col cols="6" md="3">
                                        <date-select
                                            type="month"
                                            label="Desde"
                                            v-model="desde"
                                        ></date-select></v-col
                                    ><v-col cols="6" md="3"
                                        ><date-select
                                            type="month"
                                            label="Hasta"
                                            v-model="hasta"
                                        ></date-select></v-col
                                ></v-row>
                                <apexchart
                                    height="230"
                                    width="99%"
                                    type="line"
                                    :options="ChartOptions(this.labels)"
                                    :series="formattedDataN"
                                />
                            </v-card>
                        </v-col>
                        <v-col cols="12" md="6">
                            <v-card>
                                <v-card-title>
                                    Ventas Últimos 3 Años
                                </v-card-title>
                                <apexchart
                                    height="230"
                                    width="99%"
                                    type="line"
                                    :options="ChartOptions(this.meses)"
                                    :series="
                                        formattedDataYear(esta_gastos_year_n)
                                    "
                                />
                            </v-card>
                        </v-col>
                        <v-col cols="12" md="6">
                            <v-card>
                                <v-card-title>
                                    Gastos Últimos 3 Años
                                </v-card-title>

                                <apexchart
                                    height="230"
                                    width="99%"
                                    type="line"
                                    :options="ChartOptions(this.meses)"
                                    :series="
                                        formattedDataYear(esta_ventas_gastos_n)
                                    "
                                />
                            </v-card>
                        </v-col>
                        <v-col cols="12" md="4"></v-col>
                        <v-col cols="12" md="12">
                            <listaMarketing></listaMarketing>
                        </v-col>

                        <v-col cols="12" md="4"></v-col>
                    </v-row>
                </v-tab-item>
            </v-tabs>
        </template>
        <Inicio v-else> </Inicio>
    </v-container>
</template>

<script>
import Inicio from "./Inicio.vue";
import ListaResumenVentas from "./Components/ListaResumenVentas.vue";
import ListaResumenGastos from "./Components/ListaResumenGastos.vue";
import ListaSeguimiento from "./Components/ListaSeguimiento.vue";
import listaMarketing from "./Components/ListaMarketing.vue";
import ListaResumenVentasTfg from "./ComponentsTfg/ListaResumenVentas.vue";
import ResumenPotenciales from "./ComponentsTfg/ResumenPotencialesTfg.vue";
import GastosCaja from "./ComponentsTfg/GastosCaja.vue";
import ListaSeguimientoTFG from "./ComponentsTfg/ListaSeguimiento.vue";
import listaMarketingTFG from "./ComponentsTfg/ListaMarketing.vue";
import { apexchart } from "vue-apexcharts";
export default {
    data() {
        return {
            gastos_netos: [],
            gastos_caja: [],
            ventas: [],
            labels: [],
            esta_gastos: [],
            esta_ventas: [],
            esta_gastos_year: [],
            esta_ventas_gastos: [],
            esta_ventas_no_iva: [],
            gastos_netos_n: [],
            gastos_caja_n: [],
            ventas_n: [],
            labels_n: [],
            esta_gastos_n: [],
            esta_ventas_n: [],
            esta_gastos_year_n: [],
            esta_ventas_gastos_n: [],
            esta_ventas_no_iva_n: [],
            desde: null,
            hasta: null,
            meses: [
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
        };
    },
    watch: {
        desde(val) {
            this.getEstadisticas();
        },
        hasta(val) {
            this.getEstadisticas();
        },
    },
    computed: {
        user: function () {
            return this.$store.getters.getuser;
        },
        formattedData() {
            return [
                {
                    name: "Gastos",
                    type: "column",
                    data: this.esta_gastos,
                },
                {
                    name: "Ventas sin Iva",
                    type: "line",
                    data: this.esta_ventas_no_iva,
                },
                {
                    name: "Ventas",
                    type: "line",
                    data: this.esta_ventas,
                },
            ];
        },
        formattedDataN() {
            return [
                {
                    name: "Gastos",
                    type: "column",
                    data: this.esta_gastos_n,
                },
                {
                    name: "Ventas sin Iva",
                    type: "line",
                    data: this.esta_ventas_no_iva_n,
                },
                {
                    name: "Ventas",
                    type: "line",
                    data: this.esta_ventas_n,
                },
            ];
        },
    },
    components: {
        apexchart: apexchart,
        ListaResumenVentas,
        ListaResumenGastos,
        ListaSeguimiento,
        listaMarketing,
        GastosCaja,
        ListaSeguimientoTFG,
        listaMarketingTFG,
        ResumenPotenciales,
        ListaResumenVentasTfg,
        Inicio,
    },
    created() {
        /* axios.get(`api/index-app/${localStorage.getItem("user_id")}`).then(
            (res) => {},
            (err) => {}
        );*/
        this.getGastos();
        this.getVentas();
        this.getEstadisticas();
        this.getEstadisticasYear();
    },
    methods: {
        formattedDataYear(data) {
            let result = [];
            const currentDate = new Date();
            const year = currentDate.getFullYear();
            for (let i = year - data.length + 1; i <= year; i++) {
                const index = i - year - 1 + data.length;
                result.push({
                    name: i.toString(),
                    type: "line",
                    data: data[index],
                });
            }
            console.log(result);
            return result;
        },
        ChartOptions(labels) {
            return {
                // title: { text: "ESTADISTICA VENTAS", align: "center" },
                chart: {
                    animations: { enabled: this.animations },
                    zoom: { autoScaleYaxis: false },
                },
                yaxis: {
                    tooltip: { enabled: false },
                    labels: { formatter: (val) => val },
                },
                markers: ["#000000"],
                colors: ["#9900ff", "#f44336", "#01b301"],
                stroke: { width: [4, 4, 4], curve: "straight" },
                labels: labels,
                plotOptions: {
                    bar: {
                        columnWidth: "40%",
                        borderColor: "#20a020",
                    },
                },

                fill: { opacity: [1, 1, 0.4] },
            };
        },
        getVentas() {
            axios.get(`api/dashboard-ventas-tfg-year`).then(
                (res) => {
                    this.ventas = res.data;
                },
                (err) => {
                    this.$toast.error("Error consultando Gastos");
                }
            );
        },
        getEstadisticas() {
            let params = "";
            if (this.desde != null) {
                params += `&start=${this.desde}-01`;
            }
            if (this.hasta != null) {
                params += `&end=${this.hasta}-01`;
            }
            axios.get(`api/get-stats-ventas-tfg?id=null${params}`).then(
                (res) => {
                    this.esta_ventas_no_iva = res.data.sin_iva;
                    this.esta_ventas = res.data.data;
                    this.esta_gastos = res.data.gastos;
                    this.labels = res.data.labels;
                },
                (err) => {
                    this.$toast.error("Error consultando Gastos");
                }
            );
            axios
                .get(`api/get-estadisticas-ventas-diarias?id=null${params}`)
                .then(
                    (res) => {
                        this.esta_ventas_no_iva_n = res.data.sin_iva;
                        this.esta_ventas_n = res.data.data;
                        this.esta_gastos_n = res.data.gastos;
                        this.labels_n = res.data.labels;
                    },
                    (err) => {
                        this.$toast.error("Error consultando Gastos");
                    }
                );
        },
        getEstadisticasYear() {
            axios.get(`api/get-stats-years-ventas-tfg`).then(
                (res) => {
                    this.esta_gastos_year = res.data.ventas;
                    this.esta_ventas_gastos = res.data.gastos;
                },
                (err) => {
                    this.$toast.error("Error consultando Gastos");
                }
            );
            axios.get(`api/get-stats-years-ventas`).then(
                (res) => {
                    this.esta_gastos_year_n = res.data.ventas;
                    this.esta_ventas_gastos_n = res.data.gastos;
                },
                (err) => {
                    this.$toast.error("Error consultando Gastos");
                }
            );
        },
        getGastos() {
            axios.get(`api/get-dashboard-tfg-gastos-neto`).then(
                (res) => {
                    this.gastos_netos = res.data.gastos_netos;
                    this.gastos_caja = res.data.gastos_caja;
                    console.log(this.gastos_netos);
                },
                (err) => {
                    this.$toast.error("Error consultando Gastos");
                }
            );
        },
    },
};
</script>

<style></style>
