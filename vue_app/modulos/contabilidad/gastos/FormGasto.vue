<template>
    <v-card shaped class="pa-4">
        <loader v-if="isloading"></loader>
        <v-card>
            <v-toolbar flat color="#1d2735" dark>
                <v-toolbar-title>Guardar / Editar Gasto</v-toolbar-title>
            </v-toolbar>
            <v-card-text>
                <v-row dense>
                    <v-col cols="12" md="6">
                        <v-text-field
                            outlined
                            dense
                            filled
                            :error-messages="
                                errors.errors.importe
                                    ? errors.errors.importe[0]
                                    : null
                            "
                            v-model="gasto.importe"
                            @change="validaImporte"
                            label="Importe"
                            required
                            prefix=""
                        ></v-text-field>
                        <small v-if="errorImporte">
                            {{ errorImporte }}
                        </small>
                    </v-col>
                    <v-col cols="12" md="6">
                        <dynamic_select
                            :outlined="true"
                            :dense="true"
                            v-model="gasto.tipo_gasto_id"
                            title="Tipo"
                            show="nombre"
                            item-value="id"
                            :estados="tipos"
                            @delete="deleteProyectoEstado"
                            @create="saveproyectoEstado"
                            @clear="save_tipo = {}"
                            @update="saveproyectoEstado"
                            @getEstado="
                                (index) => {
                                    save_tipo = tipos[index];
                                }
                            "
                        >
                            <v-row>
                                <v-col cols="12">
                                    <v-text-field
                                        label="Nombre"
                                        v-model="save_tipo.nombre"
                                    ></v-text-field>
                                </v-col>
                            </v-row>
                        </dynamic_select>
                    </v-col>
                </v-row>
                <v-row>
                    <v-col cols="12" md="6">
                        <v-menu
                            ref="menu"
                            v-model="menu"
                            :close-on-content-click="false"
                            :return-value.sync="gasto.fecha"
                            transition="scale-transition"
                            offset-y
                            min-width="290px"
                        >
                            <template v-slot:activator="{ on, attrs }">
                                <v-text-field
                                    outlined
                                    dense
                                    filled
                                    :error-messages="
                                        errors.errors.fecha
                                            ? errors.errors.fecha[0]
                                            : null
                                    "
                                    v-model="gasto.fecha"
                                    label="Fecha"
                                    append-icon="mdi-calendar"
                                    readonly
                                    v-bind="attrs"
                                    v-on="on"
                                >
                                </v-text-field>
                            </template>
                            <v-date-picker
                                v-model="gasto.fecha"
                                no-title
                                scrollable
                            >
                                <v-spacer></v-spacer>
                                <v-btn
                                    text
                                    color="primary"
                                    @click="menu = false"
                                >
                                    Cancel
                                </v-btn>
                                <v-btn
                                    text
                                    color="primary"
                                    @click="$refs.menu.save(gasto.fecha)"
                                >
                                    OK
                                </v-btn>
                            </v-date-picker>
                        </v-menu>
                    </v-col>
                    <v-col cols="12" md="6">
                        <v-file-input
                            outlined
                            dense
                            filled
                            prepend-icon="mdi-file-image"
                            ref="file"
                            accept=""
                            label="Archivo"
                            v-on:change="handleFileUpload"
                        ></v-file-input>
                        <v-col v-if="isloading" cols="12" md="5">
                            <v-progress-linear
                                v-model="uploadPercentage"
                                height="25"
                            >
                                <strong
                                    >{{ Math.ceil(uploadPercentage) }}%</strong
                                >
                            </v-progress-linear>
                        </v-col>
                    </v-col>
                </v-row>
                <v-row class="mb-3" dense>
                    <a
                        v-if="
                            gasto.id &&
                            gasto.nombre_archivo != null &&
                            gasto.nombre_archivo != false
                        "
                        target="_blank"
                        :href="gasto.path"
                    >
                        {{ gasto.nombre_archivo }}
                    </a>
                </v-row>
                <v-row dense>
                    <v-col cols="12">
                        <v-textarea
                            outlined
                            dense
                            v-model="gasto.descripcion"
                            label="Descripción"
                        ></v-textarea>
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
                            @click="saveGasto"
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
                    <span>Guardar Gasto</span>
                </v-tooltip>
            </v-col>
        </v-row>
        <!--<v-custom-title text="Guardar Gasto"></v-custom-title>

        <v-form class="mt-5">

            <v-row dense>
                <v-col cols="12" md="3">
                    <v-text-field class="my-input" filled :error-messages="errors.errors.codigo ? errors.errors.codigo[0] : null" v-model="gasto.codigo" label="Codigo" required></v-text-field>
                </v-col>

                <v-col cols="12" md="2">
                    <v-text-field filled

                        :error-messages="errors.errors.importe ? errors.errors.importe[0] : null"
                        v-model="gasto.importe"
                        @change="validaImporte"
                        label="Importe"
                        required
                        prefix=""></v-text-field>

                        <small v-if="errorImporte">
                            {{errorImporte}}
                        </small>
                </v-col>

                 <v-col cols="12" md="3">
                    <v-select filled :items="tipos" item-value="id" item-text="nombre" v-model="gasto.tipo_gasto_id" label="Tipo"></v-select>
                </v-col>
            </v-row>

            <v-row>

                <v-col cols="12" md="5">
                     <v-menu ref="menu" v-model="menu" :close-on-content-click="false" :return-value.sync="gasto.fecha" transition="scale-transition" offset-y min-width="290px">

                        <template v-slot:activator="{ on, attrs }">
                            <v-text-field filled :error-messages="errors.errors.fecha ? errors.errors.fecha[0] : null" v-model="gasto.fecha" label="Fecha" append-icon="mdi-calendar" readonly v-bind="attrs" v-on="on">
                            </v-text-field>
                        </template>

                        <v-date-picker v-model="gasto.fecha" no-title scrollable>
                            <v-spacer></v-spacer>

                            <v-btn text color="primary" @click="menu = false">
                                Cancel
                            </v-btn>

                            <v-btn text color="primary" @click="$refs.menu.save(gasto.fecha)">
                                OK
                            </v-btn>
                        </v-date-picker>

                    </v-menu>
                </v-col>

                <v-col cols="12" md="5">
                    <v-file-input filled prepend-icon="mdi-file-image" ref="file" accept="" label="Archivo" v-on:change="handleFileUpload"></v-file-input>

                     <v-col v-if="isloading" cols="12" md="5">
                        <v-progress-linear v-model="uploadPercentage" height="25">
                            <strong>{{ Math.ceil(uploadPercentage) }}%</strong>
                        </v-progress-linear>
                    </v-col>
                </v-col>
            </v-row>

            <v-row class="mb-3" dense>
                <a v-if="gasto.id && gasto.nombre_archivo != null && gasto.nombre_archivo != false" target="_blank" :href="gasto.path">
                    {{gasto.nombre_archivo}}
                </a>
            </v-row>

            <v-row dense>

            </v-row>

            <v-row dense>
                <v-col cols="12" md="5">
                    <v-textarea v-model="gasto.descripcion" label="Descripción"></v-textarea>
                </v-col>
            </v-row>

            <v-row class="mt-3">
                <v-col cols="12" md="3">
                    <v-btn rounded depressed @click="saveGasto" :disabled="isloading" color="success" class="white--text">Guardar</v-btn>

                     <router-link :to="{ path: `/lista-gastos` }" class="action-buttons">

                          <v-btn rounded depressed  color="warning" class="white--text">Volver</v-btn>
                     </router-link>
                </v-col>


            </v-row>

        </v-form> -->

        <!--  <div>
            <loading v-if="isloading"></loading>
         <v-row>
            <file-input
                    :files="files"
                    v-on:file-change="setFiles"
                    file-clear="clearFiles"
                    id="inputFile">
            </file-input>
            <div v-if="botonEnviar">
                <v-btn rounded depressed @click="uploadFile" :disabled="isloading" color="success" class="white--text">     Escanear Imagenes
                </v-btn>
            </div>
         </v-row>
        <v-row>
            <v-col cols="12" sm="12" md="6" >
               <span v-if="imagePreview">
                 <v-tooltip bottom>
                   <template v-slot:activator="{ on, attrs }">
                    <v-btn
                        x-small
                        v-model="clearFileButton"
                        color="error"
                        v-bind="attrs"
                        v-on="on"
                        @click="resetInput()"
                        type="button"
                        class="float-left">
                              <v-icon>mdi-close</v-icon>
                    </v-btn>
                     </template>
                        <span>Quitar Imagen</span>
                     </v-tooltip>
                     <v-row v-for="img,i,key in imagePreview" :key="i">
                         <img  width="100%" style="height:100%" :src="img"  class="img-responsive">
                     </v-row>
               </span>
          </v-col>
           <v-col cols="12" sm="12" md="6" >
            <div v-if="responseScan">
            </div>
            <div style="overflow-y: scroll!important; height:100%;">
               <pre>
                    {{responseScan}}
                </pre>
            </div>
           </v-col>
        </v-row>
    </div>   -->
    </v-card>
</template>

<script>
import FileInput from "../../../global_components/FileInput.vue";
import Loading from "./Loading.vue";
export default {
    components: {
        "file-input": FileInput,
        loading: Loading,
    },
    props: ["value"],
    data() {
        return {
            save_tipo: {},
            loader: null,
            loading3: false,
            files: [],
            imagePreview: null,
            levelSelect: null,
            mandar: "",
            clearFileButton: false,
            ocultarImagen: false,
            botonEnviar: false,
            responseScan: "",
            //##############
            menu: false,
            tipos: [],
            proyectos: [],
            menu: false,
            uploadPercentage: 0,
            gasto: {
                id: "",
                codigo: "",
                fecha: new Date().toISOString().substr(0, 10),
                importe: "",
                descripcion: "",
                archivo: null,
                nombre_archivo: null,
                path: null,
                tipo_gasto_id: "",
                user_id: localStorage.getItem("user_id"),
                proyecto_id: "",
            },
            errorImporte: "",
            fileName: "",
        };
    },
    watch: {
        gasto(val) {
            this.$emit("input", val);
        },
        value(val) {
            this.gasto = val;
        },
        loader() {
            const l = this.loader;
            this[l] = !this[l];
            setTimeout(() => (this[l] = false), 3000);
            this.loader = null;
        },
    },
    created() {
        this.getTiposGasto();
        this.getAllProyectos();
        this.getRandomCode();
        if (this.$route.query.id) {
            this.getGastoById(this.$route.query.id);
        }
    },
    methods: {
        deleteProyectoEstado(id) {
            axios.get(`api/delete-tipos-gasto/${id}`).then((res) => {
                this.getTiposGasto();
            });
        },
        saveproyectoEstado() {
            axios.post("api/save-tipos-gasto", this.save_tipo).then((res) => {
                this.getTiposGasto();
                this.save_tipo = {};
            });
        },
        verDocumento() {
            console.log("ver documento");
        },
        getTiposGasto() {
            axios
                .get(`api/get-tipos-gasto/${localStorage.getItem("user_id")}`)
                .then(
                    (res) => {
                        this.tipos = res.data;
                    },
                    (res) => {
                        this.$toast.error("Error consultando tipos de gasto");
                    }
                );
        },
        getAllProyectos() {
            axios.get(`api/get-all-proyectos`).then(
                (res) => {
                    this.proyectos = res.data;
                },
                (res) => {
                    this.$toast.error("Error consultando proyectos");
                }
            );
        },
        resetInput() {
            // this.ocultarImagen = true
            this.functioResetInput();
            // this.form.nombreImagen = 'Selecciona un logo para tu tienda'
        },
        functioResetInput() {
            var input = document.getElementById("inputFile");
            input.children[0].type = "text";
            input.children[0].type = "file";
            this.files = [];
            // const input = this.$refs.fileInput
            // input.type = 'text'
            // input.type = 'file'
            this.imagePreview = [];
            this.responseScan = "";
            // this.clearFileButton = false
        },
        uploadFile() {
            if (this.files.length == 0) {
                alert("Debe seleccionar al menos un archivo");
                return;
            }
            let formSendFiles = new FormData();
            for (let fileSave of this.files) {
                formSendFiles.append("imagen[]", fileSave, fileSave.name);
            }
            formSendFiles.append("user_id", this.captureUriId);
            formSendFiles.append("carpeta", "ocr");
            formSendFiles.append("parentPholder", "ocr");
            for (var file in this.files.length) {
                formSendFiles.append("imagen[]", file, file.name);
            }
            axios.post(`api/ocr`, formSendFiles).then(
                (res) => {
                    let respuestOk = res.status;
                    if (respuestOk * 1 == 200) {
                        this.$toast.sucs("Documento cargado");
                        this.responseScan = res.data.ocr;
                        console.log(jsons);
                    }
                },
                (err) => {
                    this.$toast.error("Error cargando documento(s)");
                }
            );
        },
        setFiles(files) {
            const filesPreview = files;
            Object.keys(filesPreview).forEach((i) => {
                const file = filesPreview[i];
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.imagePreview.push(reader.result);
                };
                this.imagePreview = [];
                reader.readAsDataURL(file);
            });
            this.botonEnviar = true;
            console.log(this.imagePreview);
            if (files !== undefined) {
                this.files = files;
                this.disableUploadButtonImage = false;
            }
        },
        limpiar() {
            // const input = this.$refs.file
            // input.type = 'text'
            // input.type = 'file'
            var input = document.getElementById("inputFile");
            input.children[0].type = "text";
            input.children[0].type = "file";
            this.files = [];
            this.responseScan = "";
        },
        getGastoById(gasto_id) {
            axios.get(`api/get-gasto-by-id/${gasto_id}`).then(
                (res) => {
                    this.gasto = res.data;
                },
                (res) => {
                    this.$toast.error("Error consultando Gasto");
                }
            );
        },
        saveGasto() {
            let formData = new FormData();
            if (this.gasto.id) formData.append("id", this.gasto.id);
            if (this.gasto.codigo) formData.append("codigo", this.gasto.codigo);
            if (this.gasto.importe)
                formData.append("importe", this.gasto.importe);
            if (this.gasto.descripcion)
                formData.append("descripcion", this.gasto.descripcion);
            if (this.gasto.archivo)
                formData.append("imagen[]", this.gasto.archivo);
            if (this.gasto.fileName)
                formData.append("nombreArchivo", this.fileName);
            if (this.gasto.tipo_gasto_id)
                formData.append("tipo_gasto_id", this.gasto.tipo_gasto_id);
            if (this.gasto.fecha) formData.append("fecha", this.gasto.fecha);
            if (this.gasto.user_id)
                formData.append("user_id", this.gasto.user_id);
            if (this.gasto.proyecto_id)
                formData.append("proyecto_id", this.gasto.proyecto_id);
            axios
                .post("api/save-gasto", formData, {
                    headers: {
                        "Content-Type": "multipart/form-data",
                    },
                    onUploadProgress: function (progressEvent) {
                        this.uploadPercentage = parseInt(
                            Math.round(
                                (progressEvent.loaded / progressEvent.total) *
                                    100
                            )
                        );
                    }.bind(this),
                })
                .then(
                    (res) => {
                        this.$toast.sucs("Gasto guardado con exito");
                        this.$emit("Saved");
                    },
                    (res) => {
                        this.$toast.error("Error guardando gasto");
                    }
                );
        },
        handleFileUpload(file) {
            this.fileName = file.name;
            this.gasto.archivo = file;
        },
        validaImporte(valor) {
            this.errorImporte = "";
            var RE = /^\d*\.?\d*$/;
            if (RE.test(valor)) {
                this.errorImporte = "";
            } else {
                this.errorImporte = "Inserte un numero entero o decimal";
                this.gasto.importe = "";
            }
            if (this.gasto.importe.length > 10) {
                // alert('El numero es mu grande')
                this.errorImporte = "Inserte un valor de importe válido";
            }
        },
        getRandomCode() {
            let randomChars =
                "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
            let result = "";
            for (var i = 0; i < 3; i++) {
                result += randomChars.charAt(
                    Math.floor(Math.random() * randomChars.length)
                );
            }
            this.gasto.codigo = result;
        },
    },
    computed: {
        captureUriId() {
            return localStorage.user_id;
        },
        isloading() {
            return this.$store.getters.getloading;
        },
        errors() {
            return this.$store.getters.geterrors;
        },
    },
};
</script>

<style media="screen">
.my-input input {
    text-transform: uppercase;
}
</style>
