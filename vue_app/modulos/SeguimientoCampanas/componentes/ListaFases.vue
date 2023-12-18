<template>
    <v-card class="pa-3 ma-3">
        <v-toolbar flat color="#1d2735" dark>
            <v-icon class="white--text" style="font-size: 45px">
                mdi-bullhorn
            </v-icon>
            <pre><v-toolbar-title><h2>Listado de Fases de seguimiento</h2></v-toolbar-title></pre>
        </v-toolbar>
        <loader v-if="isloading"></loader>
        <v-tooltip right>
            <template v-slot:activator="{ on, attrs }">
                <v-btn
                    fab
                    to="lista-seguimiento-campana"
                    :loading="isloading"
                    :disabled="isloading"
                    color="blue"
                    class="mx-3 mt-2"
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
                    :to="{
                        path: `/guardar-fases-campana?page_id=${$route.query.id}`,
                    }"
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
            <span>Nueva Fase</span>
        </v-tooltip>
        <v-row>
            <v-col cols="12" md="4">
                <v-text-field
                    prepend-icon="mdi-account-search"
                    v-model="search"
                    label="busqueda"
                ></v-text-field>
            </v-col>
        </v-row>
        <v-data-table
            :headers="headers"
            :search="search"
            :items-per-page="10"
            :items="fases"
            item-key="id"
            class="elevation-1"
            disable-pagination
            hide-default-footer
        >
            <template v-slot:item.active="{ item }">
                <v-chip
                    v-if="user.role == 1"
                    @click="changeActive(item)"
                    class="ma-2 white--text"
                    :color="item.active ? 'green' : 'red'"
                >
                    {{ item.active ? "activo" : "inactivo" }}
                </v-chip>
                <v-chip
                    v-if="user.role == 2"
                    class="ma-2 white--text"
                    :color="item.active ? 'green' : 'red'"
                >
                    {{ item.active ? "activo" : "inactivo" }}
                </v-chip>
            </template>
            <template v-slot:item.total="{ item }">
                <template v-if="item.total != 0">
                    {{ item.total }}
                </template>
            </template>
            <template v-slot:item.tasa_precio="{ item }">
                <template v-if="item.tasa_precio != 0">
                    {{ item.tasa_precio }}
                </template>
            </template>
            <template v-slot:item.tasa_obj_per="{ item }">
                <template v-if="item.tasa_obj_per">
                    {{ item.tasa_obj_per }}%
                </template>
            </template>
            <template v-slot:item.tasa_per_med="{ item }">
                <template v-if="item.tasa_per_med">
                    {{ item.tasa_per_med }}%
                </template>
            </template>
            <template v-slot:item.action="{ item }">
                <router-link
                    v-if="user.role == 1 || user.role == 2"
                    :to="{
                        path: `/guardar-fases-campana?id=${item.id}&page_id=${item.seguimiento_id}`,
                    }"
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
                <router-link
                    :to="{
                        path: `/lista-leads-facebook?id=${item.id}&page_id=${item.page_id}`,
                    }"
                    class="action-buttons"
                >
                    <v-icon
                        small
                        class="mr-2"
                        color="#1d2735"
                        style="font-size: 25px"
                        title="Ver"
                        >mdi-eye-outline</v-icon
                    >
                </router-link>
            </template>
            <template v-slot:item.file_name="{ item }">
                <img
                    :src="item.path"
                    style="width: 150px; height: 40px"
                    class="mt-1"
                />
            </template>
            <template v-slot:item.url="{ item }">
                <!--<span v-if="item.url != 'undefined'"> <a :href="'https://www.'+item.url" target="_blank">{{item.url}}</a></span>-->
                <span v-if="item.url != 'undefined'">
                    <a :href="item.url" target="_blank">{{ item.url }}</a></span
                >
                <span v-else>No Asignada</span>
            </template>
        </v-data-table>
        <v-row style="margin-top: 2rem">
            <v-col col="12" md="8">
                <v-data-table
                    :headers="headers_ventas"
                    :items="seguimiento.ventas"
                >
                    <template v-slot:item.cliente="{ item }">
                        <template
                            v-if="item.sin_factura && item.cliente != null"
                            >{{ item.cliente.nombre }}</template
                        >
                        <template v-else-if="item.factura != null">
                            <template
                                v-if="item.factura.cliente_real != null"
                                >{{
                                    item.factura.cliente_real.nombre
                                }}</template
                            >
                        </template>
                    </template>
                    <template v-slot:item.factura.tipo_nro="{ item }">
                        <template
                            v-if="!item.sin_factura && item.factura != null"
                            >{{ item.factura.tipo_nro }}</template
                        >
                        <template v-else>SF</template>
                    </template>
                    <template v-slot:item.total="{ item }">
                        <template v-if="item.factura != null"
                            >{{ item.factura.total }} €</template
                        >
                        <template v-else-if="item.sin_factura"
                            >{{ item.total_sf }} €</template
                        >
                    </template>
                    <template v-slot:item.total_sin_iva="{ item }">
                        <template v-if="item.factura != null">
                            {{ (item.factura.total / 1.21).toFixed(2) }} €
                        </template>
                        <template v-else-if="item.sin_factura">
                            {{ (item.total_sf / 1.21).toFixed(2) }} €</template
                        >
                    </template>
                    <template v-slot:item.factura.fecha="{ item }">
                        <template v-if="item.factura != null">
                            {{ item.factura.fecha | format_date }}
                        </template>
                        <template v-else-if="item.dia != null">
                            {{ item.dia | format_date }}
                        </template>
                    </template>
                    <template v-slot:item.gasto="{ item }">
                        {{ item.gasto }} €
                    </template>
                    <template v-slot:item.beneficio="{ item }">
                        {{ (item.precio / 1.21 - item.gasto).toFixed(2) }} €
                    </template>
                </v-data-table>
            </v-col>
            <v-col col="12" md="4">
                <v-card>
                    <div
                        style="
                            display: flex;
                            align-items: center;
                            height: 3rem;
                            width: 100%;
                            color: white;
                            background-color: #1e1e1e;
                        "
                    >
                        Ciclo de Vida
                    </div>
                    <table>
                        <tbody>
                            <tr>
                                <td>Ventas Totales:</td>
                                <td>
                                    {{ total }}
                                </td>
                            </tr>
                            <tr>
                                <td>Gastos Totales:</td>
                                <td>
                                    <v-text-field
                                        type="number"
                                        v-model="seguimiento.gastos_totales"
                                        @change="guardarSeguimiento"
                                    ></v-text-field>
                                </td>
                            </tr>
                            <tr>
                                <td>Beneficios:</td>
                                <td>
                                    {{ total - seguimiento.gastos_totales }}
                                </td>
                            </tr>
                            <tr>
                                <td>CdV VENTAS:</td>
                                <td>
                                    {{
                                        fases[0].tasa_med > 0
                                            ? total / fases[0].tasa_med
                                            : 0
                                    }}
                                </td>
                            </tr>
                            <tr>
                                <td>CdV BENEFICIOS:</td>
                                <td>
                                    {{
                                        fases[0].tasa_med > 0
                                            ? (total -
                                                  seguimiento.gastos_totales) /
                                              fases[0].tasa_med
                                            : 0
                                    }}
                                </td>
                            </tr>
                            <tr>
                                <td>Venta x € Invertido</td>
                                <td>
                                    {{
                                        (
                                            total /
                                            (total - seguimiento.gastos_totales)
                                        ).toFixed(2)
                                    }}
                                </td>
                            </tr>
                            <tr>
                                <td>% Ventas</td>
                                <td>{{ perVentas() }}</td>
                            </tr>
                        </tbody>
                    </table>
                </v-card>
            </v-col>
        </v-row>
    </v-card>
</template>

<script>
export default {
    data() {
        return {
            headers_ventas: [
                { text: "Dia", value: "factura.fecha", sortable: false },
                { text: "Cliente", value: "cliente", sortable: false },
                { text: "Factura", value: "factura.tipo_nro", sortable: false },
                { text: "Total", value: "total", sortable: false },
                {
                    text: "Total sin IVA",
                    value: "total_sin_iva",
                    sortable: false,
                },

                { text: "Action", value: "action", sortable: false },
            ],
            search: "",
            fases: [],
            selectedItem: 0,
            dialog: false,
            ventas: [],
            seguimiento: {
                gastos_totales: 0,
                nombre: null,
                producto: null,
                desde: null,
                hasta: null,
            },
            // headers_ventas: [{ text: "Fase", value: "fase" }],
            headers: [
                { text: "Fase", value: "fase" },
                { text: "Objetivo", value: "objetivo" },
                //{text: 'Objetivo', value: 'tasa_obj'},
                //{text: 'Objetivo %', value: 'tasa_obj_per'},
                { text: "Medicion", value: "tasa_med" },
                { text: "Medicion %", value: "tasa_per_med" },
                { text: "Precio", value: "tasa_precio" },
                { text: "Total", value: "total" },
                { text: "Acciones", value: "action", sortable: false },
            ],
        };
    },
    mounted() {
        this.getAllFases();
        this.getVentas();
        this.getSeguimiento(this.$route.query.id);
    },
    methods: {
        getVentas() {
            axios.get(`api/get-ventas-seguimientos`).then((res) => {
                this.ventas = res.data;
            });
        },
        perVentas() {
            let total_sum = 0;
            this.fases.forEach((fase) => {
                if (fase.fase == "5" || fase.fase.includes("4")) {
                    total_sum += fase.tasa_med;
                }
            });
            return total_sum / this.fases[2].tasa_med;
        },
        guardarSeguimiento() {
            axios.post("api/save-seguimiento", this.seguimiento).then(
                (res) => {
                    this.$toast.sucs("Seguimiento Actualizada");
                },
                (res) => {
                    this.$toast.error("Algo ha salido mal");
                }
            );
        },
        getSeguimiento(id) {
            axios.get(`api/get-seguimiento/${id}`).then(
                (res) => {
                    this.seguimiento = res.data;
                },
                (res) => {
                    this.$toast.error("Algo ha salido mal");
                }
            );
        },
        getAllFases() {
            axios.get(`api/get-fases-seguimiento/${this.$route.query.id}`).then(
                (res) => {
                    this.fases = res.data;
                },
                (err) => {
                    this.$toast.error("Error consultando datos");
                }
            );
        },

        openModal(item) {
            this.selectedItem = this.fases.indexOf(item);
            this.dialog = true;
        },
    },
    computed: {
        total: function () {
            let res = 0;
            this.fases.forEach((element) => {
                res += parseFloat(element.total);
            });
            return res;
        },
        isloading: function () {
            return this.$store.getters.getloading;
        },
        user: function () {
            return this.$store.getters.getuser;
        },
    },
};
</script>
