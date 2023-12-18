<template>
    <v-container>
        <loader v-if="isloading"></loader>
        <v-form>
            <v-card flat>
                <v-toolbar flat color="#1d2735" dark>
                    <v-toolbar-title>Editar Campaña</v-toolbar-title>
                </v-toolbar>
                <v-card-text>
                    <v-row>
                        <v-col cols="12" md="6">
                            <v-select
                                dense
                                outlined
                                v-model="formulario.id_servicio"
                                :items="servicios"
                                item-text="nombre"
                                item-value="id"
                                label="Servicio Contratado"
                            >
                            </v-select>
                        </v-col>
                        <v-col cols="12" md="6">
                            <v-select
                                dense
                                outlined
                                v-model="formulario.id_page_sendinblue"
                                :items="pages_sending_blue"
                                item-text="name"
                                item-value="id"
                                label="Pagina SendinBlue"
                            >
                            </v-select>
                        </v-col>
                        <v-col cols="12" md="4">
                            <v-select
                                dense
                                outlined
                                v-model="formulario.field_email"
                                :items="form_fields"
                                item-text="name"
                                item-value="name"
                                label="Campo Email"
                            >
                            </v-select>
                        </v-col>
                        <v-col cols="12" md="4">
                            <v-select
                                dense
                                outlined
                                v-model="formulario.field_name"
                                :items="form_fields"
                                item-text="name"
                                item-value="name"
                                label="Campo Nombre"
                            >
                            </v-select>
                        </v-col>
                        <v-col cols="12" md="4">
                            <v-select
                                dense
                                outlined
                                v-model="formulario.field_phone"
                                :items="form_fields"
                                item-text="name"
                                item-value="name"
                                label="Campo Telefono"
                            >
                            </v-select>
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
                                @click="
                                    $router.push(`/lista-formulario-facebook?id=${$route.query.page_id}`)
                                "
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
                                @click="guardarPromocion"
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
                        <span>Guardar Formulario</span>
                    </v-tooltip>
                </v-col>
            </v-row>
        </v-form>
    </v-container>
</template>

<script>
import FileInput from "../../../global_components/FileInput.vue";
import VFileComponent from "../../../global_components/VFileComponent.vue";
import { servicios_mixin } from "../../../global_mixins/servicios_mixin";

export default {
    components: { "file-input": FileInput, VFileComponent },
    mixins: [servicios_mixin],

    data() {
        return {
            formulario: {
                id: null,
                id_servicio: 5,
                id_page_sendinblue: 2,
                field_phone: null,
                field_name: null,
                field_email: null
            },
            form_fields:[],
            pages_sending_blue: []
        };
    },
    created() {
        if (this.$route.query.id) {
            this.getFormulario(this.$route.query.id);
            this.getFormFields(this.$route.query.id);
        }
        if(this.$route.query.page_id){
            this.getPagesSendinblue();

        }
    },
    methods: {
        getFormFields(id) {
            axios.get(`webhook/get-form-fields?id=${id}`).then(
                res => {
                    this.form_fields = res.data;
                },
                res => {
                    this.$toast.error("Algo ha salido mal");
                }
            );
        },
        getFormulario(id) {
            axios.get(`webhook/get-formulario/${id}`).then(
                res => {
                    this.formulario = res.data;

                },
                res => {
                    this.$toast.error("Algo ha salido mal");
                }
            );
        },
        getPagesSendinblue() {
            axios.get(`webhook/get-sending-blue-list/${this.$route.query.page_id}`).then(
                res => {
                    this.pages_sending_blue = res.data.lists;
                },
                res => {
                    this.$toast.error("Algo ha salido mal");
                }
            );
        },

        guardarPromocion() {
            axios.post("webhook/save-formulario", this.formulario).then(
                res => {
                    this.$toast.sucs("Formulario Actualizado");
                    this.$router.push(`/lista-formulario-facebook?id=${this.$route.query.page_id}`);
                },
                res => {
                    this.$toast.error("Algo ha salido mal");
                }
            );
        }
    },

    computed: {
        isloading() {
            return this.$store.getters.getloading;
        },
        errors() {
            return this.$store.getters.geterrors;
        }
    }
};
</script>
