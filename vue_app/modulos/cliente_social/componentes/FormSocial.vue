<template>
    <v-container>
        <loader v-if="isloading"></loader>
        <v-form>
            <v-card flat>
                <v-toolbar flat color="#1d2735" dark>
                    <v-toolbar-title
                        >Guardar / Editar Contenido</v-toolbar-title
                    >
                </v-toolbar>
                <v-card-text>
                    <v-row dense>
                        <v-col cols="12" md="4">
                            <v-autocomplete
                                outlined
                                label="Cliente"
                                v-model="contenido.id_cliente"
                                :items="usuarios"
                                item-value="id"
                                item-text="nombre"
                            ></v-autocomplete>
                        </v-col>

                        <v-col cols="12" md="4">
                            <v-autocomplete
                                outlined
                                label="Estado"
                                v-model="contenido.id_estado"
                                :items="estados"
                                item-value="id"
                                item-text="nombre"
                            ></v-autocomplete>
                        </v-col>
                        <v-col cols="12" md="4">
                            <date-select
                                label="Fecha de publicacion"
                                v-model="contenido.fecha_publicacion"
                            >
                            </date-select>
                        </v-col>
                    </v-row>
                    <v-col
                        v-if="isloading"
                        cols="12"
                        md="4"
                        class="pt-3 pl-0 pb-0"
                        offset="1"
                    >
                        <v-progress-linear
                            v-model="uploadPercentage"
                            height="25"
                            class="pa-1 ma-0"
                        >
                            <strong>{{ Math.ceil(uploadPercentage) }} %</strong>
                        </v-progress-linear>
                    </v-col>
                    <v-row dense>
                        <v-col cols="12" md="4">
                            <v-file-input
                                style="color: black !important"
                                multiple
                                :rules="rules"
                                show-size
                                v-model="nombre"
                                prepend-icon="mdi-file-image"
                                id="file"
                                label="Archivos"
                            ></v-file-input>
                        </v-col>

                        <v-col cols="12" md="4">
                            <v-btn
                                color="primary"
                                class="white--text"
                                @click="AgregarArchivo"
                                >Agregar</v-btn
                            >
                        </v-col>
                        <v-col cols="12" md="4">
                            <v-btn
                                color="primary"
                                class="white--text"
                                @click="EliminarArchivo()"
                                >Eliminar</v-btn
                            >
                        </v-col>
                    </v-row>
                    <v-row align-content="center">
                        <v-col cols="12">
                            <v-carousel v-model="index">
                                <template
                                    v-for="(
                                        archivo, index
                                    ) in contenido.archivos"
                                >
                                    <v-carousel-item
                                        v-if="getItemType(archivo) == 'image'"
                                        contain
                                        v-bind:key="index"
                                        cover
                                    >
                                        <img
                                            style="
                                                height: 400px;
                                                width: 100%;
                                                object-fit: contain;
                                            "
                                            :src="archivo"
                                            controls
                                        />
                                    </v-carousel-item>

                                    <v-carousel-item
                                        v-else-if="
                                            getItemType(archivo) == 'video'
                                        "
                                        v-bind:key="index"
                                        cover
                                    >
                                        <video
                                            style="width: 100%; height: 90%"
                                            :src="archivo"
                                            controls
                                        ></video>
                                    </v-carousel-item>
                                    <v-carousel-item
                                        v-else
                                        v-bind:key="index"
                                        cover
                                        ><a
                                            style="cursor: pointer"
                                            @click="openDoc(archivo)"
                                        >
                                            <div
                                                style="
                                                    display: flex;
                                                    align-items: center;
                                                    justify-content: center;
                                                    height: 100%;
                                                    flex-direction: column;
                                                "
                                            >
                                                <div>
                                                    <v-icon size="5rem"
                                                        >mdi-file-document</v-icon
                                                    >
                                                    <div>
                                                        Documento Redes.{{
                                                            getExtension(
                                                                archivo
                                                            )
                                                        }}
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </v-carousel-item>
                                </template>
                            </v-carousel>
                        </v-col>
                    </v-row>

                    <v-row>
                        <v-col cols="12">
                            <v-textarea
                                outlined
                                v-model="contenido.texto"
                                label="Texto"
                            ></v-textarea>
                        </v-col>
                        <v-col cols="12">
                            <v-textarea
                                outlined
                                readonly
                                v-model="contenido.observaciones"
                                label="Observaciones"
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
                                @click="
                                    $router.push(
                                        `/lista-social?id=${contenido.id_cliente}`
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
                                @click="guardarContenido"
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
                        <span>Guardar Promoción</span>
                    </v-tooltip>
                </v-col>
            </v-row>
        </v-form>
    </v-container>
</template>

<style>
.v-file-input__text {
    color: black !important;
}
.v-window__container {
}
</style>
<script>
export default {
    data() {
        return {
            rules: [
                (value) => {
                    console.log(value);
                    return (
                        !value ||
                        !value.length ||
                        value[0].size < 52428800 ||
                        `El archivo no puede pesar mas de 50MB!`
                    );
                },
            ],
            nombre: "",
            index: "",
            contenido: {
                id: null,
                nombre: null,
                active: false,
                file_name: null,
                name: null,
                path: null,
                archivos: [],
            },
            usuarios: [],
            estados: [],
            imagePreview: [],
            uploadPercentage: 0,
        };
    },
    created() {
        this.getActiveClientes();
        this.getEstados();
        if (this.$route.query.id_cliente) {
            this.contenido.id_cliente = parseInt(this.$route.query.id_cliente);
            console.log(this.contenido.id_cliente);
        }
        if (this.$route.query.id) {
            this.getContenidoById(this.$route.query.id);
        }
    },
    methods: {
        openDoc(archivo) {
            var fileURL = archivo;
            if (archivo[0] != "h") {
                const file_str = archivo.split("base64,")[1];
                var byteCharacters = atob(file_str);
                var byteNumbers = new Array(byteCharacters.length);
                for (var i = 0; i < byteCharacters.length; i++) {
                    byteNumbers[i] = byteCharacters.charCodeAt(i);
                }
                var byteArray = new Uint8Array(byteNumbers);
                var file = new Blob([byteArray], {
                    type: `${archivo.split("base64,")[0]}base64`,
                });
            }

            var fileURL = URL.createObjectURL(file);
            var a = document.createElement("a");
            document.body.appendChild(a);
            a.style = "display: none";
            a.href = fileURL;
            a.download = `DocumentoFidiasRedes.${
                archivo.split(";base64,")[0].split("/")[1]
            }`;
            a.click();

            // Clean up
            window.URL.revokeObjectURL(fileURL);
            document.body.removeChild(a);
        },
        EliminarArchivo() {
            this.contenido.archivos.splice(this.index, 1);
        },
        getExtension(item) {
            console.log(item);
            if (typeof item === "string" || item instanceof String) {
                if (item[0] == "h") {
                    const match = item.match(/\.([0-9a-z]+)(?:[\?#]|$)/i);
                    return match ? match[1] : null;
                }

                let result = item
                    .split(";")[0]
                    .split("data:")
                    ["1"].split("/")[1];
                return result;
            }
        },
        getItemType(item) {
            console.log(item);
            if (typeof item === "string" || item instanceof String) {
                if (item[0] == "h") return this.isImgUrl(item);
                let result = item.split("data:")["1"].split("/")[0];
                return result;
            }
        },
        isImgUrl(url) {
            if (/\.(jpg|jpeg|png|webp|avif|gif)$/i.test(url)) {
                return "image";
            } else if (/\.(mp4|avi|mkv|flv|mov|wmv|webm)$/i.test(url)) {
                return "video";
            } else {
                return "doc";
            }
        },
        getActiveClientes() {
            axios.get(`api/get-all-clientes`).then(
                (res) => {
                    this.usuarios = res.data.users;
                },
                (err) => {
                    this.$toast.error("Error consultando Usuario");
                }
            );
        },

        getEstados() {
            axios.get(`api/get-estados-contenido`).then(
                (res) => {
                    this.estados = res.data;
                },
                (err) => {
                    this.$toast.error("Error consultando Usuario");
                }
            );
        },
        getContenidoById(contenido_id) {
            axios.get(`api/get-contenido-redes-by-id/${contenido_id}`).then(
                (res) => {
                    this.contenido = res.data;
                },
                (res) => {
                    this.$toast.error("Algo ha salido mal");
                }
            );
        },

        getBase64: (file) => {
            return new Promise((resolve, reject) => {
                const reader = new FileReader();
                reader.readAsDataURL(file);
                reader.onload = () => resolve(reader.result);
                reader.onerror = (error) => reject(error);
            });
        },
        AgregarArchivo() {
            let me = this;

            for (let file of document.getElementById("file").files) {
                let extension = me.getFileExtension(file);
                console.log(`File extension: ${extension}`);
                me.getBase64(file).then((base64) => {
                    let crip = base64.split(";")[0].split("data:")["1"];
                    const mime = crip.split("/")[0];
                    if ((mime == "application") & (extension != "pdf")) {
                        const prev = `data:application/${extension};base64,`;
                        const res = prev + base64.split("base64,")[1];
                        me.contenido.archivos.push(res);
                    } else {
                        me.contenido.archivos.push(base64);
                    }

                    file = null;
                });
            }
        },
        getFileExtension(file) {
            return file.name.split(".").pop();
        },
        guardarContenido() {
            axios
                .post("api/save-contenido-redes", this.contenido, {
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
                        this.$toast.sucs("Contenido guardado");
                        this.$router.push(
                            `/lista-social?id=${this.contenido.id_cliente}`
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
