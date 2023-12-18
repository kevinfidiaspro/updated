<template>
    <v-container>
        <loader v-if="isloading"></loader>
        <v-form>
            <v-card class="my-0 py-0">
                <v-toolbar flat color="#1d2735" dark>
                    <v-toolbar-title>Guardar / Editar Usuario</v-toolbar-title>
                </v-toolbar>
                <loader v-if="isloading"></loader>
                <v-tabs>
                    <v-tab>Datos</v-tab>
                    <v-tab v-if="usuario.role == 3 || usuario.role == 1"
                        >Vacaciones</v-tab
                    >
                    <v-tab-item>
                        <v-row>
                            <v-row cols="2" md="6">
                                <color-picker
                                    v-model="usuario.color"
                                ></color-picker>
                            </v-row>
                            <v-col cols="10" md="6" align="right">
                                <v-card
                                    class="mt-3 mr-4 mb-3"
                                    style="
                                        max-height: 100px !important;
                                        max-width: 250px !important;
                                        margin-bottom: 25px !important;
                                    "
                                    elevation="3"
                                >
                                    <v-img
                                        v-if="usuario.avatar"
                                        :src="usuario.avatar"
                                        contain
                                        aspect-ratio="3"
                                    ></v-img>
                                    <v-img
                                        v-if="!usuario.avatar"
                                        src="/default.png"
                                        contain
                                        aspect-ratio="3"
                                    ></v-img>
                                    <v-btn
                                        v-if="usuario.avatar"
                                        fab
                                        x-small
                                        color="error"
                                        @click="defaultAvatar()"
                                    >
                                        <v-icon class="white--text"
                                            >mdi-close-circle-outline</v-icon
                                        >
                                    </v-btn>
                                </v-card>
                            </v-col>
                            <v-col
                                cols="12"
                                md="5"
                                class="mt-6"
                                style="max-width: 360px !important"
                                align="center"
                            >
                                <v-file-component
                                    v-on:file_changed="fileChanged"
                                ></v-file-component>
                                <!--<v-file-component label="Cambia Imagen" v-on:file_changed="fileChanged"></v-file-component>-->
                            </v-col>
                        </v-row>
                        <v-tabs horizontal>
                            <v-tab>
                                <v-icon left
                                    >mdi-account-supervisor-circle</v-icon
                                >Datos del Usuario
                            </v-tab>
                            <v-tab-item class="pa-2 my-0">
                                <v-card flat>
                                    <v-row dense>
                                        <v-col cols="12" md="4">
                                            <v-text-field
                                                dense
                                                outlined
                                                :error-messages="
                                                    errors.errors['nombre']
                                                        ? errors.errors[
                                                            'nombre'
                                                          ][0]
                                                        : null
                                                "
                                                v-model="usuario.nombre"
                                                label="Nombre"
                                                required
                                            >
                                            </v-text-field>
                                        </v-col>
                                        <v-col cols="12" md="4">
                                            <v-text-field
                                                dense
                                                outlined
                                                v-model="usuario.nombre_fiscal"
                                                label="Nombre Fiscal"
                                            >
                                            </v-text-field>
                                        </v-col>
                                        <v-col cols="12" md="4">
                                            <v-text-field
                                                dense
                                                outlined
                                                v-model="
                                                    usuario.nombre_comercial
                                                "
                                                label="Nombre Comercial"
                                            >
                                            </v-text-field>
                                        </v-col>
                                        <v-col cols="12" md="4">
                                            <v-text-field
                                                dense
                                                outlined
                                                counter
                                                maxlength="9"
                                                minlength="9"
                                                v-model="usuario.cif"
                                                label="CIF - DNI"
                                            >
                                            </v-text-field>
                                        </v-col>
                                    </v-row>
                                    <v-row dense>
                                        <v-col cols="12" md="4">
                                            <v-text-field
                                                dense
                                                outlined
                                                :error-messages="
                                                    errors.errors['telefono']
                                                        ? errors.errors[
                                                              'telefono'
                                                          ][0]
                                                        : null
                                                "
                                                v-model="usuario.telefono"
                                                label="Teléfono"
                                                required
                                                :rules="[rules.number_rule]"
                                                counter
                                                maxlength="9"
                                            ></v-text-field>
                                        </v-col>
                                        <v-col cols="12" md="4">
                                            <v-text-field
                                                dense
                                                outlined
                                                :error-messages="
                                                    errors.errors['email']
                                                        ? errors.errors[
                                                              'email'
                                                          ][0]
                                                        : null
                                                "
                                                v-model="usuario.email"
                                                label="Email"
                                                required
                                            ></v-text-field>
                                        </v-col>
                                        <v-col cols="12" md="4">
                                            <v-select
                                                dense
                                                outlined
                                                :error-messages="
                                                    errors.errors['role']
                                                        ? errors.errors[
                                                              'role'
                                                          ][0]
                                                        : null
                                                "
                                                :items="roles"
                                                item-value="id"
                                                item-text="role"
                                                label="Seleccione un Perfil"
                                                v-model="usuario.role"
                                            >
                                            </v-select>
                                        </v-col>
                                        <v-col
                                            cols="12"
                                            md="4"
                                            v-if="
                                                usuario.role == 3 ||
                                                usuario.role == 1 ||
                                                usuario.role == 5
                                            "
                                        >
                                            <v-autocomplete
                                                dense
                                                outlined
                                                :error-messages="
                                                    errors.errors['role']
                                                        ? errors.errors[
                                                              'role'
                                                          ][0]
                                                        : null
                                                "
                                                :items="roles_tfg"
                                                item-value="id"
                                                item-text="nombre"
                                                label="Seleccione un rol TFG"
                                                v-model="usuario.rol_tfg"
                                            >
                                            </v-autocomplete>
                                        </v-col>
                                    </v-row>
                                </v-card>
                            </v-tab-item>
                        </v-tabs>
                        <v-tabs horizontal>
                            <v-tab>
                                <v-icon left>mdi-home-account</v-icon>Domicilio
                                del Usuario
                            </v-tab>
                            <v-tab-item class="pa-2 my-0">
                                <v-card flat>
                                    <v-row dense>
                                        <v-col cols="12" md="4">
                                            <v-text-field
                                                dense
                                                outlined
                                                label="Direccion"
                                                v-model="usuario.direccion"
                                            ></v-text-field>
                                        </v-col>
                                        <v-col cols="12" md="2">
                                            <v-text-field
                                                dense
                                                outlined
                                                :error-messages="
                                                    errors.errors[
                                                        'codigo_postal'
                                                    ]
                                                        ? errors.errors[
                                                              'codigo_postal'
                                                          ][0]
                                                        : null
                                                "
                                                v-model="usuario.codigo_postal"
                                                label="Codigo Postal"
                                                :rules="[
                                                    rules.number_rule,
                                                    rules.required,
                                                ]"
                                                counter
                                                maxlength="5"
                                                required
                                            ></v-text-field>
                                        </v-col>
                                        <v-col cols="12" md="3">
                                            <v-text-field
                                                dense
                                                outlined
                                                :error-messages="
                                                    errors.errors['localidad']
                                                        ? errors.errors[
                                                              'localidad'
                                                          ][0]
                                                        : null
                                                "
                                                v-model="usuario.localidad"
                                                label="Localidad"
                                                :counter="60"
                                                required
                                            ></v-text-field>
                                        </v-col>
                                        <v-col cols="12" md="3">
                                            <v-select
                                                dense
                                                outlined
                                                :error-messages="
                                                    errors.errors[
                                                        'provincia_id'
                                                    ]
                                                        ? errors.errors[
                                                              'provincia_id'
                                                          ][0]
                                                        : null
                                                "
                                                v-model="usuario.provincia_id"
                                                :items="provincias"
                                                item-text="nombre"
                                                item-value="id"
                                                label="Provincia"
                                            ></v-select>
                                        </v-col>
                                    </v-row>
                                </v-card>
                            </v-tab-item>
                        </v-tabs>
                        <v-tabs horizontal>
                            <v-tab>
                                <v-icon left
                                    >mdi-text-box-multiple-outline</v-icon
                                >Datos Adicionales
                            </v-tab>
                            <v-tab-item class="pa-2 my-0">
                                <v-card flat>
                                    <v-row dense>
                                        <v-col cols="12" md="4" class="mt-2">
                                            <v-text-field
                                                dense
                                                outlined
                                                v-model="usuario.cuenta"
                                                label="Cuenta Bancaria"
                                                counter
                                                maxlength="20"
                                                minlength="20"
                                                required
                                            ></v-text-field>
                                        </v-col>
                                        <v-col cols="12" md="4">
                                            <date-select
                                                label="Fecha Alta"
                                                v-model="usuario.fecha_alta"
                                            ></date-select>
                                        </v-col>
                                        <v-col cols="12" md="4" class="mt-2">
                                            <v-text-field
                                                dense
                                                outlined
                                                v-model="usuario.naf"
                                                label="NAF"
                                                required
                                            ></v-text-field>
                                        </v-col>
                                    </v-row>
                                    <v-row dense>
                                        <v-col cols="12" md="12" class="mb-3">
                                            <!-- Descripcion -->
                                            <small
                                                ><strong
                                                    >Observaciones</strong
                                                ></small
                                            >
                                            <!-- Editor -->
                                            <ckeditor
                                                style="cursor: none"
                                                :editor="editor"
                                                v-model="usuario.observaciones"
                                                :config="editorConfig"
                                            ></ckeditor>
                                        </v-col>
                                    </v-row>
                                </v-card>
                            </v-tab-item>
                        </v-tabs>
                    </v-tab-item>
                    <v-tab-item
                        v-if="(usuario.role != 2) & (usuario.role != 4)"
                    >
                        <Vacaciones :user="usuario"></Vacaciones>
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
                    <v-tooltip top v-if="this.editMode == true">
                        <template v-slot:activator="{ on, attrs }">
                            <v-btn
                                fab
                                @click="updateUsuario"
                                :loading="isloading"
                                :disabled="isloading"
                                color="success"
                                class="mx-2"
                                v-bind="attrs"
                                v-on="on"
                            >
                                <v-icon class="white--text"
                                    >mdi-account-convert</v-icon
                                >
                            </v-btn>
                        </template>
                        <span>Actualiza Usuario</span>
                    </v-tooltip>
                    <v-tooltip top v-if="this.editMode == false">
                        <template v-slot:activator="{ on, attrs }">
                            <v-btn
                                fab
                                @click="saveUsuario"
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
                        <span>Guarda Usuario</span>
                    </v-tooltip>
                </v-col>
            </v-row>
        </v-form>
    </v-container>
</template>

<script>
import Vacaciones from "./Vacaciones.vue";
import FileInput from "../../../global_components/FileInput.vue";
import ClassicEditor from "@ckeditor/ckeditor5-build-classic";
import VFileComponent from "../../../global_components/VFileComponent.vue";
import colorPicker from "../../../components/general/colorPicker.vue";
export default {
    components: {
        "file-input": FileInput,
        VFileComponent,
        Vacaciones,
        colorPicker,
    },
    data() {
        return {
            roles_tfg: [],
            menu: false,
            menu: "",
            editMode: false,
            usuario: {
                id: "",
                user_id: localStorage.getItem("user_id"),
                nombre: "",
                nombre_fiscal: "",
                cif: "",
                telefono: "",
                email: "",
                role: 2,
                direccion: "",
                codigo_postal: "",
                localidad: "",
                provincia_id: 35,
                cuenta: "",
                fecha_alta: new Date().toISOString().substr(0, 10),
                observaciones: null,
                avatar: null,
            },
            rules: {
                number_rule: (value) => /^\d+$/.test(value) || "Campo numérico",
            },
            roles: [
                {
                    id: 1,
                    role: "Administrador",
                },
                {
                    id: 5,
                    role: "Administración",
                },
                {
                    id: 8,
                    role: "Atención al Cliente",
                },
                {
                    id: 6,
                    role: "Marketing",
                },
                {
                    id: 7,
                    role: "Supervisor Marketing",
                },
                {
                    id: 2,
                    role: "Cliente",
                },
                {
                    id: 3,
                    role: "Empleado",
                },
                {
                    id: 4,
                    role: "Potencial",
                },
                {
                    id: 9,
                    role: "Vendedor",
                },
            ],
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
            provincias: [],
            files: [],
            imagePreview: [],
            csrf: document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute("content"),
            user_id: localStorage.getItem("user_id"),
        };
    },

    created() {
        if (this.$route.query.id) {
            this.editMode = true;
            this.getUsuarioById(this.$route.query.id);
        }
        this.getMethodsForm();
        this.getRolesTFG();
    },
    methods: {
        volver() {
            if (this.usuario.role == 4) {
                this.$router.push(`/lista-usuario`);
            } else if (this.usuario.role == 2) {
                this.$router.push(`/lista-clientes`);
            }
        },
        getRolesTFG() {
            axios.get(`api/roles-tfg`).then(
                (res) => {
                    this.roles_tfg = res.data;
                },
                (res) => {
                    this.$toast.error("Error consultando Datos");
                }
            );
        },
        getMethodsForm() {
            axios.get(`api/get-methods-form`).then(
                (res) => {
                    this.provincias = res.data.provincias;
                },
                (res) => {
                    this.$toast.error("Error consultando Datos");
                }
            );
        },
        updateUsuario() {
            let formDataUpdate = new FormData();
            for (let fileSave of this.files) {
                formDataUpdate.append("imagen[]", fileSave, fileSave.name);
            }
            formDataUpdate.append("usuario", JSON.stringify(this.usuario));
            axios
                .post("api/update-usuario/" + this.usuario.id, formDataUpdate)
                .then(
                    (res) => {
                        console.log(res);
                        this.$toast.sucs("Usuario actualizado con éxito");
                    },
                    (res) => {
                        console.log(res);
                        this.$toast.error("Error guardando usuario");
                    }
                );
        },
        saveUsuario() {
            let formDataSave = new FormData();
            for (let fileSave of this.files) {
                formDataSave.append("imagen[]", fileSave, fileSave.name);
            }
            formDataSave.append("usuario", JSON.stringify(this.usuario));
            axios.post("api/save-usuario", formDataSave).then(
                (res) => {
                    this.$toast.sucs("Usuario guardado con éxito");
                    // this.$router.push('/lista-usuario')
                },
                (res) => {
                    this.$toast.error("Error guardando usuario");
                }
            );
        },
        getUsuarioById(usuario_id) {
            axios.get(`api/get-usuario-by-id/${usuario_id}`).then(
                (res) => {
                    this.usuario = res.data.user;
                    this.provincias = res.data.provincias;
                },
                (res) => {
                    this.$toast.error("Error consultando Usuario");
                }
            );
        },
        fileChanged(base_64) {
            this.usuario.avatar = base_64;
        },

        defaultAvatar() {
            this.usuario.avatar = null;
        },
    },
    computed: {
        isloading() {
            return this.$store.getters.getloading;
        },
        errors() {
            return this.$store.getters.geterrors;
        },
        uri() {
            return window.location.origin;
        },
        idUser() {
            return localStorage.user_id;
        },
    },
};
</script>
<style>
.inputFile {
    padding: 100%;
    position: absolute;
    opacity: 0.1;
}
.inputFile[type] {
    cursor: copy;
}
</style>
<style>
/* Oculta el file imput debajo de la foto */
.v-input__slot {
    background-color: transparent !important;
}
.v-file-input__text {
    color: transparent !important;
}
.mdi-close::before {
    color: transparent !important;
}
</style>
