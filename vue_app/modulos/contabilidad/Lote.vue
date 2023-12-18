<template>
    <div class="background my-container col-10 offset-1 mt-2">
        <v-container>
            <loader v-if="isloading"></loader>
            <h2 class="mb-3 grey--text text--darken-1">Enviar Facturas</h2>
            <v-row dense>
                <v-col cols="12" sm="12" md="6" lg="6">
                    <v-select
                        label="Seleccione el tipo de Factura"
                        :items="facturas"
                        item-text="tipo"
                        v-model="modelFactura.tipo"
                    ></v-select>
                </v-col>
            </v-row>
            <v-row dense>
                <v-col cols="12">
                    <rango-fechas
                        :url="url"
                        :modelFactura="modelFactura"
                        v-on:success_query="setIngresoBruto"
                    ></rango-fechas>
                </v-col>

                <v-col cols="12">
                    <v-btn
                        :disabled="isloading"
                        v-if="
                            archivo != null &&
                            modelFactura.tipo == 'Facturas Enviadas'
                        "
                        rounded
                        depressed
                        target="_blank"
                        :href="
                            'storage/lotes/userId_' +
                            userId +
                            '/facturas_enviadas.zip'
                        "
                        class="white--text"
                        color="orange"
                        >descargar</v-btn
                    >

                    <v-btn
                        :disabled="isloading"
                        v-if="
                            archivo != null &&
                            modelFactura.tipo == 'Facturas Recibidas'
                        "
                        rounded
                        depressed
                        target="_blank"
                        :href="
                            'storage/lotes/userId_' +
                            userId +
                            '/facturas_recibidas.zip'
                        "
                        class="white--text"
                        color="orange"
                        >descargar</v-btn
                    >

                    <v-btn
                        :disabled="isloading"
                        v-if="
                            archivoTodos.enviadas == null &&
                            archivoTodos.recibidas == null &&
                            modelFactura.tipo == 'Todas'
                        "
                        rounded
                        depressed
                        target="_blank"
                        @click="callDown()"
                        class="white--text"
                        color="orange"
                        >descargar</v-btn
                    >

                    <v-btn
                        :disabled="isloading"
                        v-if="archivo != null"
                        @click="email_dialog = true"
                        rounded
                        depressed
                        class="white--text"
                        color="light-blue"
                        >enviar</v-btn
                    >
                </v-col>
            </v-row>

            <lote-content-dialog
                :modelFactura="modelFactura"
                :isloading="isloading"
                v-on:close_dialog="cerrar"
                :email_dialog="email_dialog"
            ></lote-content-dialog>
        </v-container>
    </div>
</template>

<script>
import loteContentDialog from "./loteContentDialog";
import rangoFechas from "./rangoFechas";

export default {
    components: {
        rangoFechas,
        loteContentDialog,
    },

    data() {
        return {
            email_dialog: false,
            url: `api/get-lote-facturas/${localStorage.getItem("user_id")}`,
            archivo: null,

            archivoTodos: {
                enviadas: false,
                reciidas: false,
            },

            facturas: [
                {
                    id: 1,
                    tipo: "Facturas Recibidas",
                },
                {
                    id: 2,
                    tipo: "Facturas Enviadas",
                },
                {
                    id: 3,
                    tipo: "Todas",
                },
            ],
            modelFactura: {
                tipo: "Todas",
            },
            docs: [],
        };
    },

    mounted() {
        //this.$emit('hacer_busqueda')
    },

    methods: {
        cerrar() {
            this.email_dialog = false;
        },
        callDown() {
            this.docs = ["facturas_enviadas.zip", "facturas_recibidas.zip"];

            let imagenes = this.docs;
            let originaName = window.location.origin + "/";
            let pathServer = "storage/lotes/userId_" + this.userId + "/";
            let pathDoc = "";
            let documentImagen = "";
            for (var r = 0; r < imagenes.length; r++) {
                pathDoc = originaName + pathServer + imagenes[r];
                documentImagen = imagenes[r];
                this.downloadFiles(pathDoc, documentImagen);
            }
        },

        downloadFiles(url, filename) {
            fetch(url).then(function (t) {
                return t.blob().then((b) => {
                    var a = document.createElement("a");
                    a.href = URL.createObjectURL(b);
                    a.setAttribute("download", filename);
                    a.click();
                });
            });
        },

        setIngresoBruto(data) {
            this.archivo = data;
            if (this.modelFactura.tipo == "Todas") {
                this.archivoTodos = {
                    enviadas: null,
                    reciidas: null,
                };
            }
        },
    },

    filters: {
        format_currency(value) {
            return `${parseFloat(value).toFixed(2)}`;
        },
    },

    computed: {
        isloading: function () {
            return this.$store.getters.getloading;
        },
        userId() {
            return localStorage.user_id;
        },
    },
};
</script>
