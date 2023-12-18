<template>
    <v-container>
        <loader v-if="isloading"></loader>
        <v-form>
            <v-card flat>
                <v-toolbar flat color="#1d2735" dark>
                    <v-toolbar-title>Guardar Pagina</v-toolbar-title>
                </v-toolbar>
                <v-card-text>
                    <v-row>
                        <v-col  cols="12" md="4">
                            <v-text-field 
                             v-model="pagina.id" label="Page ID"></v-text-field>

                        </v-col>
                        <v-col cols="12" md="4">
                            <v-text-field 
                             v-model="pagina.name" label="Nombre"></v-text-field>

                        </v-col>
                        <v-col cols="12" md="4">
                            <v-text-field 
                             v-model="pagina.sendinblue_key" label="SendinBlueKey"></v-text-field>

                        </v-col>
                        <v-col cols="12" md="4">
                            <v-text-field 
                             v-model="pagina.token" label="Page Token"></v-text-field>

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
                                    $router.push('/lista-paginas-facebook')
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
            pagina: {
                page_id: null,
                sendinblue_key: null,
                name: null,
                token: null,
            },
        };
    },
    created() {
        if (this.$route.query.id) {
            this.getFormulario(this.$route.query.id);
          
        }
       
    },
    methods: {
    
        getFormulario(id) {
            axios.get(`webhook/get-page/${id}`).then(
                res => {
                    this.pagina = res.data;
                },
                res => {
                    this.$toast.error("Algo ha salido mal");
                }
            );
        },
        guardarPromocion() {
            axios.post("webhook/save-page", this.pagina).then(
                res => {
                    this.$toast.sucs("Pagina Actualizada");
                    this.$router.push("/lista-paginas-facebook");
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
