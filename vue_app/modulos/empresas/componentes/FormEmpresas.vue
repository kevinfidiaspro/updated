<template>
    <v-container>
        <loader v-if="isloading"></loader>
        <v-card>
            <v-toolbar flat color="#1d2735" dark>
                <v-toolbar-title>Guardar / Editar Empresa</v-toolbar-title>
            </v-toolbar>
            <v-tabs horizontal>
                <v-tab> <v-icon left>mdi-account</v-icon>Datos Empresa </v-tab>
                <v-tab-item class="pa-3 ma-1">
                    <v-card flat>
                        <v-row dense>
                            <v-col cols="12" md="4">
                                <v-text-field
                                    dense
                                    outlined
                                    v-model="empresa.nombre"
                                    label="Nombre"
                                    required
                                ></v-text-field>
                            </v-col>
                            <v-col cols="12" md="4">
                                <v-text-field
                                    dense
                                    outlined
                                    v-model="empresa.cif"
                                    label="C.I.F"
                                    required
                                ></v-text-field>
                            </v-col>
                            <v-col cols="12" md="4">
                                <v-text-field
                                    dense
                                    outlined
                                    v-model="empresa.ccc"
                                    label="CCC"
                                    required
                                ></v-text-field>
                            </v-col>
                            <v-col cols="12" md="4">
                                <v-text-field
                                    dense
                                    outlined
                                    v-model="empresa.localidad"
                                    label="Localidad"
                                    required
                                ></v-text-field>
                            </v-col>
                            <v-col cols="12" md="4">
                                <v-text-field
                                    dense
                                    outlined
                                    v-model="empresa.codigo_postal"
                                    label="Codigo Postal"
                                    required
                                ></v-text-field>
                            </v-col>
                            <v-col cols="12" md="4">
                                <v-autocomplete
                                    dense
                                    outlined
                                    v-model="empresa.provincia_id"
                                    :items="provincias"
                                    item-value="id"
                                    item-text="nombre"
                                    label="Provincia"
                                >
                                </v-autocomplete>
                            </v-col>

                            <v-col cols="12" md="12">
                                <v-text-field
                                    dense
                                    outlined
                                    v-model="empresa.direccion"
                                    label="Direccion"
                                    required
                                ></v-text-field>
                            </v-col>
                        </v-row>
                    </v-card>
                </v-tab-item>
            </v-tabs>
        </v-card>

        <v-row class="mt-3">
            <!-- Botones Navegacion -->
            <v-col cols="12">
                <v-tooltip top>
                    <template v-slot:activator="{ on, attrs }">
                        <v-btn
                            fab
                            @click="volver"
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
                            @click="saveEmpresa"
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
                    <span>Guardar</span>
                </v-tooltip>
            </v-col>
        </v-row>
    </v-container>
</template>
<script>
import { provincias_mixin } from "../../../global_mixins/provincias_mixin";
import ClassicEditor from "@ckeditor/ckeditor5-build-classic";
export default {
    mixins: [provincias_mixin],
    data() {
        return {
            menu: false,
            menu: "",
            empresa: {
                web: "",
                usuario_mail: "",
                empresa: "",
                tipo: "",
                observaciones: "",
            },
            editor: ClassicEditor,
            editorData: "<p>Escriba Aqui Observaciones o contenido.</p>",
            editorConfig: {
                toolbar: {
                    items: [
                        "heading",
                        "bold",
                        "italic",
                        "bulletedList",
                        "numberedList",
                        "link",
                        "inserttable",
                    ],
                },
            },
        };
    },
    created() {
        if (this.$route.query.id) {
            this.getEmpresaById(this.$route.query.id);
        }
    },
    methods: {
        getEmpresaById(empresa_id) {
            axios.get(`api/get-empresa/${empresa_id}`).then(
                (res) => {
                    this.empresa = res.data;
                },
                (res) => {
                    this.$toast.error("Error consultando empresa");
                }
            );
        },
        saveEmpresa() {
            axios.post("api/save-empresa", this.empresa).then(
                (res) => {
                    this.$toast.sucs("Empresa guardada con exito");
                    this.$router.push("/lista-empresas");
                },
                (res) => {
                    this.$toast.error("Error guardando empresa");
                }
            );
        },
        volver() {
            this.$router.push(`/lista-empresas`);
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
<style>
div.v-messages.theme--light {
    margin-top: -1px !important;
    margin-bottom: -1px !important;
    padding-top: -1px !important;
    padding-bottom: -1px !important;
}
div.v-text-field__details {
    margin-top: -1px !important;
    margin-bottom: -1px !important;
    padding-top: -1px !important;
    padding-bottom: -1px !important;
}
</style>
