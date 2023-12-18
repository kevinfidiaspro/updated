<template>
    <v-container>
        <loader v-if="isloading"></loader>
        <v-card>
            <v-toolbar flat color="#1d2735" dark>
                <v-toolbar-title>Guardar / Editar Presupuesto</v-toolbar-title>
            </v-toolbar>

            <v-card id="card_presupuesto" class="pa-3 ma-3" flat>
                <small class="mb-6"><strong>Contenido Email</strong></small>


                <ckeditor style="cursor: none" :editor="editor" :config="editorConfig" v-model="item.descripcion"></ckeditor>


                <template v-if="!item.proyecto || item.proyecto.estado_id == 2">

                    <v-file-input disabled prepend-icon accept="application/pdf" class="mt-4" ref="file" outlined label="Archivos" @change="fileChange"></v-file-input>

                    <v-row dense>
                        <v-col cols="12" class="mt-4">
                            <a v-if="item.file_name" :href="item.path" target="_blank">{{item.file_name}}</a>
                        </v-col>
                    </v-row>

                </template>

                <template v-else>
                    <v-file-input prepend-icon accept="application/pdf" class="mt-4" ref="file" outlined label="Archivos" @change="fileChange"></v-file-input>

                    <v-row dense>
                        <v-col cols="12" class="mt-4">
                            <a v-if="item.file_name" :href="item.path" target="_blank">{{item.file_name}}</a>
                        </v-col>
                    </v-row>

                    <v-row dense>
                        <v-col cols="12">
                            <v-tooltip bottom>
                                <template v-slot:activator="{ on, attrs }">
                                    <v-btn fab @click="guardarPresupuesto" :loading="isloading" :disabled="isloading" color="success" class="mx-2" v-bind="attrs" v-on="on">
                                        <v-icon class="white--text">mdi-content-save-all</v-icon>
                                    </v-btn>
                                </template>
                                <span>Guardar Presupuesto</span>
                            </v-tooltip>

                            <v-tooltip bottom v-if="showButtonEmail">
                                <template v-slot:activator="{ on, attrs }">
                                    <v-btn fab @click="enviarMail" :loading="isloading" :disabled="isloading" color="amber accent-3" class="mx-2" v-bind="attrs" v-on="on" readonly>
                                        <v-icon class="white--text">mdi-at</v-icon>
                                    </v-btn>
                                </template>
                                <span>Enviar Mail</span>
                            </v-tooltip>

                        </v-col>
                    </v-row>
                </template>


                <v-dialog v-model="showPopUp" persistent max-width="400">
                    <v-card>
                        <v-toolbar color="primary" class="white--text">
                            Enviar email
                        </v-toolbar>
                        <v-card-text class="mt-5">
                            <v-text-field label="Email" append-icon="mdi-pencil" v-model="email_user"></v-text-field>
                        </v-card-text>
                        <v-card-actions>
                            <v-btn color="primary" @click="correo">Aceptar</v-btn>
                            <v-spacer></v-spacer>
                            <v-btn @click="showPopUp = false" text color="red">Cerrar</v-btn>
                        </v-card-actions>
                    </v-card>
                </v-dialog>
            </v-card>
        </v-card>
    </v-container>
</template>

<script>
    import ClassicEditor from "@ckeditor/ckeditor5-build-classic";
    export default {
        data() {
            return {
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

                item: {
                    id: null,
                    id_proyecto: null,
                    descripcion: '<p>Escriba Aqui Observaciones o contenido.</p>',
                    file: null,
                    file_name: null,
                    proyecto: {
                        id: null,
                        estado_id: null
                    }
                },

                request: new FormData(),

                // USER DATOS
                showButtonEmail: false,
                showPopUp: false,
                email_user: "",
                presupuesto_id: "",
            };
        },

        created() {
            this.$route.query.id ? this.showPresupuesto(this.$route.query.id) : null
        },


        methods: {
            async showPresupuesto(id) {
                try {
                    const response = await axios.get(`api/show-presupuestos/${id}`);
                    this.item = response.data
                } catch (error) {
                    console.log(error);
                    this.$toast.error("Error consultando Presupuesto");
                }
            },

            guardarPresupuesto() {
                const form_data = new FormData()

                for (var key in this.item) {
                    this.item[key] ? form_data.append(key, this.item[key]) : null
                }

                axios.post(`api/store-presupuestos/${this.$route.query.id}`, form_data).then(res => {
                    this.item = res.data
                    this.showButtonEmail = true
                }, res => {
                    this.$toast.error("Error guardando Proyecto, Compruebe todos los campos requeridos");
                })
            },

            enviarMail() {
                console.log("aui");
                this.showPopUp = true;
                this.email_user = this.auth_user.email;
            },

            fileChange(file) {
                this.item.file = file
            },

            async correo() {
                this.showPopUp = true;
                this.email_user = this.auth_user.email;
                try {
                    const request = {
                        email: this.email_user,
                    };
                    await axios.post(
                        "api/send-mail-presupuesto/" + this.presupuesto_id,
                        request
                    );
                    this.showPopUp = false;
                } catch (error) {
                    // const errorData = this.errors;
                    // this.fileError = errorData.error.archivo[0];
                    this.$toast.error("Error enviando email");
                }
                //   console.log("aui");
                //   this.showPopUp = true;
                //   this.email_user = this.auth_user.email;
            },
        },

        computed: {
            isloading() {
                return this.$store.getters.getloading;
            },
            errors() {
                return this.$store.getters["geterrors"];
            },
            auth_user() {
                return this.$store.getters["getuser"];
            },
        },
    }
</script>