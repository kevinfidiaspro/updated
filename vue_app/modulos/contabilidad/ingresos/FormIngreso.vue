<template>
    <v-container>
        <loader v-if="isloading"></loader>
        <v-card>
            <v-toolbar flat color="#1d2735" dark>
                <v-toolbar-title>Guardar / Editar Ingreso</v-toolbar-title>
            </v-toolbar>
            <v-card-text>
                <v-row dense>
                    <v-col cols="12" md="4">
                        <v-autocomplete
                            outlined
                            prepend-icon="mdi-account-search-outline"
                            v-model="ingreso.cliente_id"
                            :error-messages="
                                errors.errors['ingreso.cliente_id']
                                    ? errors.errors['ingreso.cliente_id'][0]
                                    : null
                            "
                            :items="usuarios"
                            item-value="id"
                            item-text="nombres"
                            label="Seleccione un Cliente"
                        >
                        </v-autocomplete>
                    </v-col>
                </v-row>
                <v-row dense>
                    <v-col cols="12" md="4">
                        <v-autocomplete
                            outlined
                            prepend-icon="mdi-account-search-outline"
                            v-model="ingreso.factura_id"
                            :items="facturas"
                            item-value="id"
                            item-text="nro_anio_factura"
                            label="Seleccione una Factura"
                            persistent-hint
                            :hint="facturas.total"
                        >
                        </v-autocomplete>
                    </v-col>
                    <v-col cols="12" md="2">
                        <v-autocomplete
                            outlined
                            v-model="ingreso.factura_id"
                            :items="facturas"
                            item-value="id"
                            label="Total"
                            item-text="total"
                            append-icon="mdi-currency-eur"
                        >
                        </v-autocomplete>
                        <!-- <v-text-field class="my-input" filled :error-messages="errors.errors.codigo ? errors.errors.codigo[0] : null" v-model="ingreso.codigo" label="Codigo" required></v-text-field> -->
                    </v-col>
                    <v-col cols="12" md="2">
                        <date-select
                            label="Fecha"
                            v-model="ingreso.created_at"
                            :outlined="true"
                        ></date-select>
                    </v-col>
                </v-row>
                <v-col cols="12" md="2">
                    <v-text-field
                        outlined
                        filled
                        append-icon="mdi-currency-eur"
                        :error-messages="
                            errors.errors['ingreso.importe']
                                ? errors.errors['ingreso.importe'][0]
                                : null
                        "
                        v-model="ingreso.importe"
                        label="Importe del ingreso"
                        required
                    ></v-text-field>
                </v-col>

                <v-row dense>
                    <v-col cols="12" md="6">
                        <v-textarea
                            outlined
                            v-model="ingreso.descripcion"
                            label="Descripción del ingreso"
                        ></v-textarea>
                    </v-col>
                </v-row>
            </v-card-text>
        </v-card>
        <v-row class="mt-3">
            <!-- Botones Navegacion -->
            <v-col cols="12">
                <v-tooltip top>
                    <template v-slot:activator="{ on, attrs }">
                        <v-btn
                            fab
                            @click="$router.push('/lista-ingresos')"
                            :loading="isloading"
                            :disabled="isloading"
                            color="blue"
                            class="mx-2"
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
                <v-tooltip top>
                    <template v-slot:activator="{ on, attrs }">
                        <v-btn
                            fab
                            @click="saveIngreso"
                            :loading="isloading"
                            :disabled="isloading"
                            color="success"
                            class="mx-2"
                            v-bind="attrs"
                            v-on="on"
                        >
                            <v-icon class="white--text"
                                >mdi-content-save-all</v-icon
                            >
                        </v-btn>
                    </template>
                    <span>Guardar Ingreso</span>
                </v-tooltip>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
export default {
    data() {
        return {
            ingreso: {
                id: "",
                codigo: "",
                importe: "",
                descripcion: "",
                user_id: localStorage.getItem("user_id"),
                proyecto_id: "",
                factura_id: "",
            },
            usuarios: [],
            facturas: [],
            proyectos: [],
        };
    },
    watch: {
        "ingreso.cliente_id": function (val) {
            this.getFacturasByProyecto(val);
        },
    },
    created() {
        //this.getAllProyectos();
        this.getActiveClientes();
        //si editamos un ingreso
        if (this.$route.query.id) {
            this.getIngresoById(this.$route.query.id);
        } else {
            this.getRandomCode();
        }
        //si añadimos un ingreso desde el enlace de listado de pendientes de pago
        if (this.$route.query.identi && this.$route.query.tipo) {
            this.ingreso.codigo =
                this.$route.query.tipo.substring(0, 3) +
                this.$route.query.identi;
        }
    },

    methods: {
        getActiveClientes() {
            axios.get(`api/get-all-clientes-active-proyectos`).then(
                (res) => {
                    this.usuarios = res.data.users;
                    for (let i = 0; i < this.usuarios.length; i++) {
                        const element = this.usuarios[i];
                        element.created_at = new Date(
                            element.created_at
                        ).toLocaleDateString();
                    }
                },
                (err) => {
                    this.$toast.error("Error consultando Usuario");
                }
            );
        },
        getIngresoById(ingreso_id) {
            axios.get(`api/get-ingreso-by-id/${ingreso_id}`).then(
                (res) => {
                    this.ingreso = res.data;
                    if (this.ingreso.cliente_id == null) {
                        this.ingreso.cliente_id = res.data.proyecto.usuario_id;
                        this.getFacturasByProyecto(this.ingreso.cliente_id);
                    }
                },
                (res) => {
                    this.$toast.error("Error consultando Ingreso");
                }
            );
        },
        getFacturasByProyecto(id) {
            axios.get(`api/get-facturas-by-cliente/${id}`).then(
                (res) => {
                    this.facturas = res.data;
                    console.log(this.facturas);
                },
                (res) => {
                    this.$toast.error("Error consultando Ingreso");
                }
            );
        },

        getAllProyectos() {
            // axios.get(`api/get-all-proyectos`).then(
            axios.get(`api/get-proyectos-activos`).then(
                (res) => {
                    this.proyectos = res.data;
                },
                (res) => {
                    this.$toast.error("Error consultando proyectos");
                }
            );
        },

        saveIngreso() {
            console.log(this.ingreso);
            axios.post("api/save-ingreso", this.ingreso).then(
                (res) => {
                    if (res.data.error != null) {
                        this.$toast.error(res.data.error);
                    } else {
                        this.$toast.sucs("Ingreso guardado con exito");
                    }
                    this.$router.push("/lista-ingresos");
                },
                (res) => {
                    this.$toast.error("Error guardando ingreso");
                }
            );
        },

        getRandomCode() {
            let randomChars =
                "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
            let result = "";
            for (var i = 0; i < 5; i++) {
                result += randomChars.charAt(
                    Math.floor(Math.random() * randomChars.length)
                );
            }
            this.ingreso.codigo = result;
        },
    },
    computed: {
        isloading() {
            return this.$store.getters.getloading;
        },

        errors() {
            return this.$store.getters.geterrors;
        },
    },
};
</script>

<style media="screen">
.my-input input {
    text-transform: uppercase;
}
</style>
