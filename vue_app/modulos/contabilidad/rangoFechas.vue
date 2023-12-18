<template>
    <v-row dense align="center">
        <v-col cols="12" md="2">
            <v-menu
                ref="desde"
                v-model="desde"
                :close-on-content-click="false"
                transition="scale-transition"
                offset-y
                min-width="290px"
            >
                <template v-slot:activator="{ on, attrs }">
                    <v-text-field
                        v-model="rango.desde"
                        label="Desde"
                        append-icon="mdi-calendar"
                        readonly
                        v-bind="attrs"
                        v-on="on"
                    >
                    </v-text-field>
                </template>

                <v-date-picker v-model="rango.desde" no-title scrollable>
                    <v-spacer></v-spacer>

                    <v-btn text color="primary" @click="desde = false">
                        Cancel
                    </v-btn>

                    <v-btn
                        text
                        color="primary"
                        @click="$refs.desde.save(rango.desde)"
                    >
                        OK
                    </v-btn>
                </v-date-picker>
            </v-menu>
        </v-col>

        <v-col cols="12" md="2">
            <v-menu
                ref="hasta"
                v-model="hasta"
                :close-on-content-click="false"
                transition="scale-transition"
                offset-y
                min-width="290px"
            >
                <template v-slot:activator="{ on, attrs }">
                    <v-text-field
                        v-model="rango.hasta"
                        label="Hasta"
                        append-icon="mdi-calendar"
                        readonly
                        v-bind="attrs"
                        v-on="on"
                    >
                    </v-text-field>
                </template>

                <v-date-picker v-model="rango.hasta" no-title scrollable>
                    <v-spacer></v-spacer>

                    <v-btn text color="primary" @click="hasta = false">
                        Cancel
                    </v-btn>

                    <v-btn
                        text
                        color="primary"
                        @click="$refs.hasta.save(rango.hasta)"
                    >
                        OK
                    </v-btn>
                </v-date-picker>
            </v-menu>
        </v-col>

        <v-col v-if="has_tipo == 'true'" cols="12" md="2">
            <v-select
                :items="tipos"
                item-value="value"
                item-text="label"
                v-model="tipo"
                label="Tipo"
            ></v-select>
        </v-col>

        <v-col cols="12" md="4">
            <v-tooltip top>
                <template v-slot:activator="{ on, attrs }">
                    <v-btn
                        fab
                        @click="buscarRango"
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
                        @click="defaultQuery"
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
            <!--<v-btn fab @click="buscarRango" color="success" class="mx-2">buscar</v-btn>-->
            <!--<v-btn @click="defaultQuery" outlined depressed rounded color="green" class="white--text">reiniciar</v-btn>-->
        </v-col>
    </v-row>
</template>

<script>
export default {
    props: ["has_tipo", "url", "modelFactura"],

    data() {
        return {
            desde: false,
            hasta: false,
            rango: {
                desde: moment().startOf("month").format("Y-MM-DD"),
                hasta: moment().add(1, "days").format("Y-MM-DD"),
            },
            tipos: [
                {
                    label: "",
                    value: "",
                },
                {
                    label: "Internos",
                    value: "internos",
                },
                {
                    label: "Generales",
                    value: "generales",
                },
            ],
            tipo: "generales",
        };
    },

    mounted() {
        this.$parent.$on("hacer_busqueda", this.buscarRango);
    },

    methods: {
        buscarRango() {
            if (this.rango.desde == null || this.rango.hasta == null) {
                this.$toast.warn("Formato de fecha es incorrecto");
                return null;
            }

            let url = `${this.url}?desde=${this.rango.desde}&hasta=${this.rango.hasta}&tipo=${this.modelFactura.tipo}`;

            if (this.has_tipo == "true") {
                url = `${url}&tipo=${this.tipo}`;
            }
            const self = this;
            axios.get(url).then(
                (res) => {
                    self.$emit("success_query", res.data);
                },
                (res) => {
                    this.$toast.error("Error obteniendo registros");
                }
            );
        },

        defaultQuery() {
            this.$emit("default_query");
        },
    },
    computed: {
        isloading: function () {
            return this.$store.getters.getloading;
        },
    },
};
</script>
