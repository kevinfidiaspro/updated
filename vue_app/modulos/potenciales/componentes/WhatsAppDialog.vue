<template>
    <v-dialog v-model="show" max-width="800px">
        <v-card>
            <v-card-title
                class="text-h5 aviso"
                style="
                    justify-content: center;
                    background: #1d2735;
                    color: white;
                "
            >
                Enviar Notificacion Whatsapp
            </v-card-title>
            <v-card-text style="text-align: center">
                <v-row>
                    <v-col cols="6">
                        <v-row>
                            <v-col cols="12">
                                <v-autocomplete
                                    v-model="plantilla"
                                    label="Plantilla"
                                    :items="plantillas"
                                    item-text="nombre"
                                    item-value="nombre"
                                ></v-autocomplete>
                            </v-col>
                            <v-col cols="12">
                                <v-text-field
                                    v-model="nombre"
                                    label="Nombre Cliente"
                                ></v-text-field>
                            </v-col>
                            <v-col cols="12">
                                <v-text-field
                                    v-model="nombre_usuario"
                                    label="Nombre Usuario"
                                ></v-text-field>
                            </v-col>
                            <v-col cols="12">
                                <v-autocomplete
                                    v-model="producto"
                                    label="Producto"
                                    :items="productos"
                                    item-text="nombre"
                                    item-value="nombre"
                                ></v-autocomplete>
                            </v-col>
                        </v-row>
                    </v-col>
                    <v-col cols="6">
                        <h5>Resultado</h5>
                        <p>{{ result }}</p>
                    </v-col>
                </v-row>
            </v-card-text>
            <v-card-actions class="pt-3">
                <v-spacer></v-spacer>

                <v-btn color="error" large @click="show = false"
                    >Cancelar</v-btn
                >
                <v-btn color="success" large @click="SendMessage()"
                    >Confirmar</v-btn
                >
                <v-spacer></v-spacer>
            </v-card-actions> </v-card
    ></v-dialog>
</template>
<script>
export default {
    props: ["value", "to", "name"],
    data() {
        return {
            show: false,
            plantillas: [],
            productos: ["el Kit digital"],
            nombre: "",
            nombre_usuario: localStorage.user_name,
            plantilla: "",
            producto: "",
        };
    },
    watch: {
        name(val) {
            this.nombre = val;
        },
        value(val) {
            this.show = val;
        },
        show(val) {
            this.$emit("input", val);
        },
    },
    created() {
        this.getProductos();
        this.getPlantillas();
        this.getUser();
    },
    computed: {
        result() {
            return this.plantilla
                .replace("{{1}}", this.nombre)
                .replace("{{2}}", this.nombre_usuario)
                .replace("{{3}}", this.producto);
        },
        resultEncoded() {
            const regex = /([\uD800-\uDBFF][\uDC00-\uDFFF])/g;
            return encodeURIComponent(this.result);
        },
    },
    methods: {
        getUser() {
            if (
                localStorage.user_name == "undefined" ||
                localStorage.user_name == null
            ) {
                axios.get(`api/user`).then(
                    (res) => {
                        localStorage.setItem("user_name", res.data.nombre);
                        this.nombre_usuario = res.data.nombre;
                    },
                    (err) => {
                        this.$toast.error("Error consultando Productos");
                    }
                );
            }
        },
        getProductos() {
            axios.get(`api/get-productos`).then(
                (res) => {
                    this.productos = res.data;
                },
                (err) => {
                    this.$toast.error("Error consultando Productos");
                }
            );
        },
        getPlantillas() {
            axios.get(`api/get-plantillas`).then(
                (res) => {
                    this.plantillas = res.data;
                },
                (err) => {
                    this.$toast.error("Error consultando Plantillas");
                }
            );
        },
        SendMessage() {
            window.open(
                `https://api.whatsapp.com/send/?phone=${this.to}&text=${this.result}`,
                "_blank"
            );
        },
        /*SendMessage() {
            if (this.to) {
                axios.get(`api/test-whatsapp/34604894808`).then(
                    (res) => {
                        this.$toast.sucs("Mensaje Enviado con exito");
                        this.show = false;
                    },
                    (err) => {
                        this.$toast.error("Error enviando Mensaje");
                    }
                );
            }
        },*/
    },
};
</script>
