<template>
    <v-card class="pa-3 ma-3">
        <v-col cols="12" md="12">
        <v-toolbar flat color="#1d2735" dark>
            <v-icon class="white--text" style="font-size: 45px">
                mdi-chart-line-stacked
            </v-icon>
            <pre><v-toolbar-title><h2>Estadísticas Proyectos</h2></v-toolbar-title></pre>
        </v-toolbar>
        </v-col>
        <loader v-if="isloading"></loader>
        <div class="background my-container">
            <v-row class="mb-2" style="margin: 0 auto; background-color: white; border-radius:30px;" >
                <v-col>
                    <div id="chart" class="ml-4">
                        <apexchart height="230" width="99%" type="line" :options="chartOptions" :series="formattedData"/>
                    </div>
                </v-col>
            </v-row>
            <v-row dense>
                <v-col cols="12" md="7">
                    <v-data-table dense :headers="headers_resumen" disable-pagination hide-default-footer mobile-breakpoint="0"
                    :items="proyectosTotalesData" item-key="id" class="elevation-1 pa-0 mb-0 mr-4">
                        <!-- Total proyectos -->
                        <template v-slot:item.ProyectosTotales="{ item }">
                            <span>{{item.ProyectosTotales}}</span>
                        </template>
                        <!-- Total gastos -->
                        <template v-slot:item.GastosTotales="{ item }">
                            <span>{{item.GastosTotales | fixed_n}}</span> €
                        </template>
                        <!-- Total ingresos -->
                        <template v-slot:item.IngresosTotales="{ item }">
                            <span>{{item.IngresosTotales | fixed_n}}</span> €
                        </template>
                        <!-- Total -->
                        <template v-slot:item.Totaltodo="{ item }">
                            <span>{{item.Totaltodo | fixed_n}}</span> €
                        </template>
                        <template v-slot:body.append>
                            <tr>
                                <th style="text-align: start;"><strong>Total Año</strong></th>
                                <th style="text-align: center;"><strong>{{ sumField('ProyectosTotales') }}</strong></th>
                                <th style="text-align: center;"><strong>{{ sumField('IngresosTotales') | fixed_n}} €</strong></th>
                                <th style="text-align: center;"><strong>{{ sumField('GastosTotales') | fixed_n}} €</strong></th>
                                <th style="text-align: center;"><strong>{{ sumField('Totaltodo') | fixed_n}} €</strong></th>
                            </tr>
                        </template>
                    </v-data-table>
                </v-col>
                <v-col cols="12" md="5">
                    <v-row dense>
                        <v-col cols="6" md="4" class="pt-3 pl-0 pb-0">
                                <v-menu
                                    ref="menu"
                                    v-model="menu"
                                    :close-on-content-click="false"
                                    :return-value.sync="desde"
                                    transition="scale-transition"
                                    offset-y
                                    min-width="280px"
                                >
                                    <template v-slot:activator="{ on, attrs }">
                                        <v-text-field
                                            v-model="desde"
                                            label="Fecha Desde"
                                            append-icon="mdi-calendar"
                                            v-bind="attrs"
                                            v-on="on"
                                            >
                                        </v-text-field>
                                    </template>
                                    <v-date-picker
                                        color="#1d2735"
                                        first-day-of-week="1"
                                        v-model="desde"
                                        no-title
                                        scrollable
                                    >
                                        <v-spacer></v-spacer>
                                        <v-btn text color="red" @click="menu = false">
                                            <strong>Cancelar</strong>
                                        </v-btn>
                                        <v-btn
                                            text
                                            color="success"
                                            @click="$refs.menu.save(desde)"
                                        ><strong>OK</strong></v-btn>
                                    </v-date-picker>
                                </v-menu>
                        </v-col>
                        <v-col cols="6" md="4" class="pt-3 pl-0 pb-0">
                                <v-menu
                                    ref="menu2"
                                    v-model="menu2"
                                    :close-on-content-click="false"
                                    :return-value.sync="hasta"
                                    transition="scale-transition"
                                    offset-y
                                    min-width="290px"
                                >
                                    <template v-slot:activator="{ on, attrs }">
                                        <v-text-field
                                            v-model="hasta"
                                            label="Fecha Hasta"
                                            append-icon="mdi-calendar"
                                            v-bind="attrs"
                                            v-on="on"
                                            >
                                        </v-text-field>
                                    </template>
                                    <v-date-picker
                                        color="#1d2735"
                                        first-day-of-week="1"
                                        v-model="hasta"
                                        no-title
                                        scrollable
                                    >
                                        <v-spacer></v-spacer>
                                        <v-btn text color="red" @click="menu2 = false">
                                            <strong>Cancelar</strong>
                                        </v-btn>
                                        <v-btn
                                            text
                                            color="success"
                                            @click="$refs.menu2.save(hasta)"
                                        ><strong>OK</strong></v-btn>
                                    </v-date-picker>
                                </v-menu>
                        </v-col>
                    </v-row>
                    <v-row dense>
                        <v-col cols="12" md="8" class="pt-3 pl-0 pb-0">
                                <v-autocomplete
                                    dense
                                    outlined
                                    prepend-icon="mdi-account-search-outline"
                                    v-model="servicio"
                                    return-object
                                    :items="servicios"
                                    item-value="id"
                                    item-text="nombre"
                                    label="Seleccione un tipo de proyecto"
                                    >
                                </v-autocomplete>
                        </v-col>
                    </v-row>
                    <v-row dense>
                        <v-col cols="12" md="6" class="pt-3 pl-0 pb-0 ml-4">
                                <v-tooltip top>
                                    <template v-slot:activator="{ on, attrs }">
                                        <v-btn
                                            fab
                                            @click="searchProyecto"
                                            :loading="isloading"
                                            :disabled="isloading"
                                            color="success"
                                            class="mx-2"
                                            v-bind="attrs"
                                            v-on="on"
                                            readonly
                                        >
                                            <v-icon class="white--text">mdi-magnify</v-icon>
                                        </v-btn>
                                    </template>
                                    <span>Buscar</span>
                                </v-tooltip>
                                <v-tooltip top>
                                    <template v-slot:activator="{ on, attrs }">
                                        <v-btn
                                            fab
                                            @click="clean"
                                            :loading="isloading"
                                            :disabled="isloading"
                                            color="blue"
                                            class="mx-2"
                                            v-bind="attrs"
                                            v-on="on"
                                        >
                                            <v-icon class="white--text">mdi-refresh</v-icon>
                                        </v-btn>
                                    </template>
                                    <span>Limpiar</span>
                                </v-tooltip>
                        </v-col>
                    </v-row>

                <v-col cols="12" md="12">
                    <br/>
                    <v-data-table dense :headers="headers_detalle" :items-per-page="6" mobile-breakpoint="0"
                    :items="proyectosDetalleData" item-key="id" class="elevation-1 pa-0 mb-0 d-block">
                        <!-- Fecha -->
                        <template v-slot:item.fecha_alta="{ item }">
                            <span>{{item.fecha_alta}}</span>
                        </template>
                        <!-- Tipo Proyecto -->
                        <template v-slot:item.servicio.nombre="{ item }">
                            <span>{{item.servicio.nombre}}</span>
                        </template>
                        <!-- Total Ingresos -->
                        <template v-slot:item.pvp="{ item }">
                            <span>{{item.pvp | fixed_n}}</span> €
                        </template>
                        <!-- Total Gastos -->
                        <template v-slot:item.pvp_gasto="{ item }">
                            <span>{{item.pvp_gasto | fixed_n}}</span> €
                        </template>
                    </v-data-table>
                </v-col>
                </v-col>
            </v-row>
        </div>
    </v-card>
</template>

<script>
import VueApexCharts from 'vue-apexcharts';
import { servicios_mixin } from "../../../global_mixins/servicios_mixin";
    export default {
        components: {
            apexchart: VueApexCharts,
        },

        props: {
            chartData: { type: Array, default: () => [] },
            animations: { type: Boolean, default: true }
        },

        mixins: [servicios_mixin],

        data() {
            return {
                proyectosTotalesData: [],
                proyectosDetalleData: [],
                desde: moment().startOf('year').format('Y-MM-DD'),
                hasta: moment().endOf('year').format('Y-MM-DD'),
                servicio: null,
                graph_labels: [],
                menu: false,
                menu: "",
                menu2: false,
                menu2: "",
                mobileBreakpoint: 600,
                headers_resumen:
                [
                    { text: 'Mes', align: 'left', value: 'Mes', sortable: false },
                    { text: 'Proyectos Totales', align: 'center', value: 'ProyectosTotales', sortable: false },
                    { text: 'Ingresos Totales', align: 'center', value: 'IngresosTotales', sortable: false },
                    { text: 'Gastos Totales', align: 'center', value: 'GastosTotales', sortable: false },
                    { text: 'TOTAL Mes', align: 'center', value: 'Totaltodo', sortable: false },
                ],
                headers_detalle:
                [
                    { text: 'Fecha', align: 'left', value: 'fecha_alta', sortable: false },
                    { text: 'Tipo Proyecto', align: 'center', value: 'servicio.nombre', sortable: false },
                    { text: 'Ingresos Totales', align: 'center', value: 'pvp', sortable: false },
                    { text: 'Gastos Totales', align: 'center', value: 'pvp_gasto', sortable: false },
                    { text: 'Total', align: 'center', value: '0', sortable: false },
                ],
            }
        },
        created() {
            this.proyectosTotales();
        },
        methods: {
            searchProyecto() {
                let desde = this.desde
                let hasta = this.hasta
                let servicio_id = this.servicio != null ? this.servicio.id : null
                axios.get(`api/detalle-data/${desde}/${hasta}/${servicio_id}`)
                    .then(res => {
                        this.proyectosDetalleData = res.data;
                        this.proyectosTotales();
                    }, err => {
                        this.$toast.error('Error al obtener Detalle')
                    })
            },

            clean() {
                this.desde = moment().startOf('year').format('Y-MM-DD')
                this.hasta = moment().endOf('year').format('Y-MM-DD')
                this.servicio = null
                this.proyectosDetalleData = []
                this.proyectosTotales();
            },

            proyectosTotales() {
                axios.get(`api/get-proyectos-data/${this.desde}/${this.hasta}`)
                .then(res => {
                    this.proyectosTotalesData = Object.values(res.data);
                    this.graph_labels = []
                    for (let index = 0; index < this.proyectosTotalesData.length; index++) {
                        const element = this.proyectosTotalesData[index].Mes;
                        this.graph_labels.push(element)
                    }
                }, err => {
                    this.$toast.error('Error al obtener Datos')
                })
            },

            sumField(key) {
                // suma totales por columnas
                return this.proyectosTotalesData.reduce((a, b) => a + (b[key] || 0), 0);
            },

        },

        filters: {
            fixed_n(n) {
                return parseFloat(n).toFixed(2)
            }
        },

        computed: {

            chartOptions() {
                return {
                    // title: { text: "ESTADISTICA VENTAS", align: "center" },
                    chart: { animations: { enabled: this.animations }, zoom: { autoScaleYaxis: false } },
                    yaxis: { tooltip: { enabled: false }, labels: { formatter: val => val } },
                    markers: ['#000000'],
                    colors: ['#9900ff', '#f44336', '#01b301'],
                    stroke: { width: [4, 4, 0], curve: 'smooth' },
                    labels: this.graph_labels,
                    plotOptions: { bar: { borderRadius: 10, columnWidth: '40%', borderColor: '#20a020'} },
                    fill: { opacity: [1, 1, 0.4] },
                }
            },

            formattedData() {
                const totalProyectos = this.proyectosTotalesData.map(d => d.ProyectosTotales);
                const totalGastos = this.proyectosTotalesData.map(d => d.GastosTotales);
                const totalIngresos = this.proyectosTotalesData.map(d => d.IngresosTotales);
                const total = this.proyectosTotalesData.map(d => d.Totaltodo);
                return [
                    { name: "TotalProyectos", type: "line", data: totalProyectos },
                    { name: "TotalGastos", type: "line", data: totalGastos },
                    { name: "TotalIngresos", type: "line", data: totalIngresos },
                    { name: "Total", type: "bar", data: total },
                ];
            },

            isloading() {
                return this.$store.getters.getloading;
            },

            errors() {
                return this.$store.getters.geterrors;
            },
        }
    }

</script>

<style >

</style>
