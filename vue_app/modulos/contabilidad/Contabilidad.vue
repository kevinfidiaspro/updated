<template>
    <v-container>
        <v-card class="pa-3 ma-3">
            <v-toolbar flat color="#1d2735" dark>
                <v-icon class="white--text" style="font-size: 45px">
                    mdi-calculator-variant
                </v-icon>
                <pre><v-toolbar-title><h2>Contabilidad</h2></v-toolbar-title></pre>
            </v-toolbar>
            <loader v-if="isloading"></loader>
            <br>
            <v-row>
                <v-col cols="12" md="3">
                    <v-card color="green" to="lista-ingresos" dark>
                        <v-card-title>
                            <v-icon large left class="white--text">
                                mdi-arrow-top-right
                            </v-icon>
                            <span class="title font-weight-light">Ingresos</span>
                        </v-card-title>
                    </v-card>
                </v-col>
                <v-col cols="12" md="3">
                    <v-card color="red" to="lista-gastos" dark>
                        <v-card-title>
                            <v-icon large left class="white--text">
                                mdi-arrow-bottom-left
                            </v-icon>
                            <span class="title font-weight-light">Gastos</span>
                        </v-card-title>
                    </v-card>
                </v-col>
            </v-row>
            <v-row dense>
                <v-col cols="3" md="3" class="pt-3 pl-0 pb-0">
                    <h2 class="mb-3">FILTROS</h2>
                </v-col>
            </v-row>
            <v-row dense>
                <v-col cols="3" md="3" class="pt-3 pl-0 pb-0">
                    <v-menu
                        ref="menu"
                        v-model="menu"
                        :close-on-content-click="false"
                        :return-value.sync="desde"
                        transition="scale-transition"
                        offset-y
                        min-width="290px"
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
                <v-col cols="3" md="3" class="pt-3 pl-0 pb-0">
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
                <v-col cols="12" md="6" class="pt-3 pl-0 pb-0">
                    <div v-show="show_chips" class="text-center">
                        <v-chip class="ma-2" color="green" text-color="white">
                            Ingresos de proyecto: {{ingreso_bruto.ingreso_proyecto | format_currency}}
                        </v-chip>
                        <v-chip class="ma-2" color="green" text-color="white">
                            Ingresos adicionales: {{ingreso_bruto.ingreso_adicional | format_currency}}
                        </v-chip>
                        <v-chip class="ma-2" color="orange" text-color="white">
                            Gastos de proyecto: {{ingreso_bruto.gasto_proyecto | format_currency}}
                        </v-chip>
                        <v-chip class="ma-2" color="orange" text-color="white">
                            Gastos adicionales: {{ingreso_bruto.gasto_adicional | format_currency}}
                        </v-chip>
                        <v-chip class="ma-2" color="blue" text-color="white">
                            Total: {{total}}€
                        </v-chip>
                    </div>
                </v-col>
            </v-row>
            <v-row dense>
                <v-col cols="12" md="8">                    
                    <v-tooltip right>
                        <template v-slot:activator="{ on, attrs }">
                            <v-btn fab :to="'/'" :loading="isloading" :disabled="isloading" color="blue" class="mx-3" v-bind="attrs" v-on="on">
                                <v-icon class="white--text">mdi-arrow-left-bold-outline</v-icon>
                            </v-btn>
                        </template>
                        <span>Volver</span>
                    </v-tooltip>
                    <v-tooltip right>
                        <template v-slot:activator="{ on, attrs }">
                            <v-btn fab @click="setIngresoBruto()" :loading="isloading" :disabled="isloading" color="success" class="mx-2" v-bind="attrs" v-on="on" readonly>
                                <v-icon class="white--text">mdi-magnify</v-icon>
                            </v-btn>
                        </template>
                        <span>Buscar</span>
                    </v-tooltip>
                    <v-tooltip right>
                        <template v-slot:activator="{ on, attrs }">
                            <v-btn fab @click="clean()" :loading="isloading" :disabled="isloading" color="purple" class="mx-2" v-bind="attrs" v-on="on">
                                <v-icon class="white--text">mdi-refresh</v-icon>
                            </v-btn>
                        </template>
                        <span>Limpiar</span>
                    </v-tooltip>
                </v-col>
            </v-row>
        </v-card>
    </v-container>
</template>

<script>
    import rangoFechas from './rangoFechas'
    export default {
        components: {
            rangoFechas
        },
        data() {
            return {
                show_chips: true,
                url: `api/get-ingreso-bruto/${localStorage.getItem('user_id')}`,
                menu: false,
                menu: "",
                menu2: false,
                menu2: "",
                desde: moment().startOf('year').format('Y-MM-DD'),
                hasta: moment().endOf('year').format('Y-MM-DD'),
                ingreso_bruto: {
                    gasto_proyecto: 0,
                    gasto_adicional: 0,
                    ingreso_proyecto: 0,
                    ingreso_adicional: 0,
                    gasto_desglosado: [],
                    user_id: localStorage.getItem('user_id')
                }
            }
        },

        mounted() {
            //this.$emit('hacer_busqueda')
            this.setIngresoBruto()
        },

        methods: {
            /*setIngresoBruto(data) {
                this.ingreso_bruto = data
                console.log(this.ingreso_bruto)
            }*/

            setIngresoBruto() {
                axios.get(`api/get-ingreso-bruto/${localStorage.getItem('user_id')}/${this.desde}/${this.hasta}`)
                .then(res => {
                    this.ingreso_bruto = res.data;
                    console.log(this.ingreso_bruto)
                }, err => {
                    this.$toast.error('Error al obtener Datos')
                })
            },
            clean() {
                this.desde = moment().startOf('year').format('Y-MM-DD')
                this.hasta = moment().endOf('year').format('Y-MM-DD')
                this.setIngresoBruto()
            },
        },

        filters: {
            format_currency(value) {
                return `${parseFloat(value).toFixed(2)}`
            }
        },

        computed: {
            isloading: function() {
                return this.$store.getters.getloading
            },

            total() {
                let suma = (this.ingreso_bruto.ingreso_proyecto + this.ingreso_bruto.ingreso_adicional) - (this.ingreso_bruto.gasto_proyecto + this.ingreso_bruto.gasto_adicional)
                return parseFloat(suma).toFixed(2)
            }
        }
    }
</script>
