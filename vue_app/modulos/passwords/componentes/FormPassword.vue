<template>
    <v-container>
        <loader v-if="isloading"></loader>
        <v-card>
            <v-toolbar flat color="#1d2735" dark>
            <v-toolbar-title>Guardar / Editar Contraseña</v-toolbar-title>
            </v-toolbar>
            <v-tabs horizontal>
            <v-tab>
                <v-icon left>mdi-account</v-icon>Datos Contraseña
            </v-tab>
            <v-tab-item class="pa-3 ma-1">
                <v-card flat>
                    <v-row dense>
                        <v-col cols="12" md="6">
                            <v-text-field dense outlined :error-messages="errors.errors.web ? errors.errors.web[0] : null" v-model="password.web" label="Web" required></v-text-field>
                        </v-col>
                        <v-col cols="12" md="6">
                            <v-text-field dense outlined :error-messages="errors.errors.usuario_mail ? errors.errors.usuario_mail[0] : null" v-model="password.usuario_mail" label="Email" required></v-text-field>
                        </v-col>
                        <v-col cols="12" md="6">
                            <v-text-field dense outlined :error-messages="errors.errors.password ? errors.errors.password[0] : null" v-model="password.password" label="Password"></v-text-field>
                        </v-col>
                        <v-col cols="12" md="6">
                            <v-text-field dense outlined :error-messages="errors.errors.tipo ? errors.errors.tipo[0] : null" v-model="password.tipo" label="Tipo"></v-text-field>
                        </v-col>
                    </v-row>
                </v-card>
            </v-tab-item>
            </v-tabs>
            
            <v-tabs horizontal>
            <v-tab>
                <v-icon left>mdi-text-box-multiple-outline</v-icon>Observaciones
            </v-tab>
            <v-tab-item class="pa-3 ma-1">
                <v-card flat>
                    <v-row dense>
                        <v-col cols="12" md="12" class="mb-3">
                            <!-- Descripcion -->
                            <small><strong>Observaciones</strong></small>
                            <!-- Editor -->
                            <ckeditor style="cursor:none" :editor="editor" v-model="password.observaciones" :config="editorConfig"></ckeditor>
                        </v-col>
                    </v-row>
                </v-card>
            </v-tab-item>
            </v-tabs>
        </v-card>

        <v-row class="mt-3"> <!-- Botones Navegacion -->            
            <v-col cols="12">
                <v-tooltip top>
                    <template v-slot:activator="{ on, attrs }">
                        <v-btn fab @click="volver" :loading="isloading" :disabled="isloading" color="blue" class="mx-2" v-bind="attrs" v-on="on">
                            <v-icon class="white--text">mdi-arrow-left-bold-outline</v-icon>
                        </v-btn>
                    </template>
                    <span>Volver</span>
                </v-tooltip>
                <v-tooltip top>
                    <template v-slot:activator="{ on, attrs }">
                        <v-btn fab @click="savePassword" :loading="isloading" :disabled="isloading" color="success" class="mx-2" v-bind="attrs" v-on="on">
                            <v-icon class="white--text">mdi-content-save-all</v-icon>
                        </v-btn>
                    </template>
                    <span>Guardar</span>
                </v-tooltip>                
            </v-col>
        </v-row>
    </v-container>
</template>
<script>
    import { provincias_mixin } from '../../../global_mixins/provincias_mixin';
    import ClassicEditor from '@ckeditor/ckeditor5-build-classic';
    export default {
        mixins: [provincias_mixin],
        data() {
            return {
                menu: false,
                menu: '',
                password: {
                    web: '',
                    usuario_mail: '',
                    password: '',
                    tipo: '',
                    observaciones: '',
                },
                editor: ClassicEditor,
                editorData: '<p>Escriba Aqui Observaciones o contenido.</p>',
                editorConfig: {
                    toolbar: {
                        items: ['heading','bold','italic','bulletedList','numberedList','link','inserttable'],
                    },
                },
            }
        },
        created() {
            if (this.$route.query.id) {
                this.getPasswordById(this.$route.query.id)
            }
        },
        methods: {
            getPasswordById(password_id) {
                axios.get(`api/get-password-by-id/${password_id}`).then(res => {
                    this.password = res.data
                }, res => {
                    this.$toast.error('Error consultando password')
                })
            },
            savePassword() {
                axios.post('api/save-password', this.password).then(res => {
                    this.$toast.sucs('Contraseña guardada con exito')
                    this.$router.push('/lista-passwords')
                }, res => {
                    this.$toast.error('Error guardando contraseña')
                })
            },
            volver(){
                this.$router.push(`/lista-passwords`)
            },
        },
        computed: {
            isloading() {
                return this.$store.getters.getloading
            },
            errors() {
                return this.$store.getters.geterrors
            }
        }
    }
</script>
<style>
    div.v-messages.theme--light
    {
        margin-top: -1px !important;
        margin-bottom: -1px !important;
        padding-top: -1px !important;
        padding-bottom: -1px !important;
    }
    div.v-text-field__details
    {
        margin-top: -1px !important;
        margin-bottom: -1px !important;
        padding-top: -1px !important;
        padding-bottom: -1px !important;
    }
</style>