<template>
    <v-container>
        <loader v-if="isloading"></loader>
        <v-form>
            <v-card flat>
                <v-toolbar flat color="#1d2735" dark>
                    <v-toolbar-title>Editar Fase</v-toolbar-title>
                </v-toolbar>
                <v-card-text>
                    <v-row>
                        <v-col cols="12" md="4">
                            <v-text-field
                                disabled
                                v-model="fase.fase"
                                label="Fase"
                            ></v-text-field>
                        </v-col>
                        <v-col cols="12" md="4">
                            <v-text-field
                                v-model="fase.objetivo"
                                label="Objetivo"
                            ></v-text-field>
                        </v-col>
                        <v-col cols="12" md="4">
                            <v-text-field
                                v-model="fase.criterio"
                                label="Criterio"
                            ></v-text-field>
                        </v-col>
                    </v-row>
                    <h3>TASA DE ÉXITO</h3>
                    <v-row>
                        <!--v-col  cols="12" md="6">
                            <v-text-field 
                             v-model="fase.tasa_obj" type="number" label="Objetivo"></v-text-field>

                        </!--v-col>
                        <v-col--  cols="12" md="6">
                            <v-text-field 
                             v-model="fase.tasa_obj_per" type="number" label="Objetivo %"></v-text-field>

                        </v-col-->
                        <v-col cols="12" md="6">
                            <v-text-field
                                v-model="fase.tasa_med"
                                type="number"
                                label="Medicion"
                            ></v-text-field>
                        </v-col>
                        <v-col
                            cols="12"
                            md="6"
                            v-if="!fases_noPrecio.includes(fase.fase)"
                        >
                            <v-text-field
                                v-model="fase.tasa_precio"
                                type="number"
                                label="Precio"
                            ></v-text-field>
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
                                    $router.push(
                                        `/lista-fases-campana?id=${fase.id_seguimiento}`
                                    )
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
                                @click="guardarFase"
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
                        <span>Guardar Fase</span>
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
            fases_noPrecio: ["1", "2", "3", "6", "7", "8", "9", "10"],
            fase: {
                fase: null,
                objetivo: null,
                criterio: null,
                tasa_obj: null,
                tasa_obj_per: null,
                tasa_med: null,
                tasa_precio: null,
                id_seguimiento: this.$route.query.page_id,
            },
            form_fields: [],
            pages_sending_blue: [],
        };
    },
    created() {
        if (this.$route.query.id) {
            this.getFase(this.$route.query.id);
        }
    },
    methods: {
        getFase(id) {
            axios.get(`api/get-fase-seguimiento/${id}`).then(
                (res) => {
                    this.fase = res.data;
                },
                (res) => {
                    this.$toast.error("Algo ha salido mal");
                }
            );
        },

        guardarFase() {
            if (this.fase.id == null) {
                this.fase.id_seguimiento = this.$route.query.page_id;
            }
            axios.post("api/save-fase-seguimiento", this.fase).then(
                (res) => {
                    this.$toast.sucs("Fase Actualizada");
                    this.$router.push(
                        `/lista-fases-campana?id=${this.fase.id_seguimiento}`
                    );
                },
                (res) => {
                    this.$toast.error("Algo ha salido mal");
                }
            );
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
