<template>
    <v-container>
        <loader v-if="isloading"></loader>
        <v-form>
            <v-card flat>
                <v-toolbar flat color="#1d2735" dark>
                    <v-toolbar-title>Guardar Seguimiento</v-toolbar-title>
                </v-toolbar>
                <v-card-text>
                    <v-row>
                        <v-col  cols="12" md="4">
                            <v-text-field 
                             v-model="seguimiento.nombre" label="Nombre"></v-text-field>

                        </v-col>
                        <v-col  cols="12" md="4">
                            <v-text-field 
                             v-model="seguimiento.producto" label="Producto Estrella"></v-text-field>

                        </v-col>
                        <v-col cols="12" md="4">
                            <date-select v-model="seguimiento.desde" label="Fecha Desde">

</date-select>

                        </v-col>
                        <v-col cols="12" md="4">
                            <date-select v-model="seguimiento.hasta" label="Fecha Hasta">

</date-select>

                        </v-col>
                    </v-row>
                </v-card-text>
            </v-card>
            <v-row class="mt-3">
                <!-- Botones Navegacion -->
                <v-col cols="12">l
                    <v-tooltip top>
                        <template v-slot:activator="{ on, attrs }">
                            <v-btn
                                fab
                                @click="
                                    $router.push('/lista-seguimiento-campana')
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
                                @click="guardarSeguimiento"
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
            seguimiento:{
                nombre:null,
                producto:null,
                desde:null,
                hasta:null
            }
        };
    },
    created() {
        if (this.$route.query.id) {
            this.getSeguimiento(this.$route.query.id);
          
        }
       
    },
    methods: {
    
        getSeguimiento(id) {
            axios.get(`api/get-seguimiento/${id}`).then(
                res => {
                    this.seguimiento = res.data;
                },
                res => {
                    this.$toast.error("Algo ha salido mal");
                }
            );
        },
        guardarSeguimiento() {
            axios.post("api/save-seguimiento", this.seguimiento).then(
                res => {
                    this.$toast.sucs("Seguimiento Actualizada");
                    this.$router.push("/lista-seguimiento-campana");
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
