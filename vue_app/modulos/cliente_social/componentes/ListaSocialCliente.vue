<template>
    <v-card class="pa-3 ma-3">
        <v-toolbar flat color="#1d2735" dark>
            <v-icon class="white--text" style="font-size: 45px">
                mdi-bullhorn
            </v-icon>
            <pre><v-toolbar-title><h2>Contenido</h2></v-toolbar-title></pre>
        </v-toolbar>
        <loader v-if="isloading"></loader>
        <v-row v-for="(item, i1) in contenido" v-bind:key="i1">
 
            <v-col cols="12" >
                <v-carousel  >
                    <template v-for="(archivo, i) in item.archivos">
                        <v-carousel-item
                                        v-if="getItemType(archivo) == 'image'"
                                        contain
   
                                        v-bind:key="index"
                                        cover
                                    >
                                        <img
                                            style="height:100%; width:100%;object-fit: contain;"
                                            :src="archivo"
                                            controls
                                        ></img>
                                    </v-carousel-item>
                        <v-carousel-item
                            v-else-if="getItemType(archivo) == 'video'"
                            v-bind:key="i"
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
                                        v-bind:key="i"
                                        cover
                                        ><a
                                            style="cursor: pointer"
                                            target="__blank"
                                            :href="archivo"
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
                <div v-if="item.fecha_publicacion" style="padding-top:1rem;font-weight: bold;padding-bottom: 1rem;">
                                <span>Fecha de Publicación: {{ item.fecha_publicacion | format_date }}</span>
                            </div>
                <p class="justify-breaks ">{{item.texto}}</p>
                   
                

                
  
            </v-col>
            <v-col cols="12">
                <v-btn rounded depressed class="white--text" @click="openModal(item)" color="red">Rechazar</v-btn>

                <v-btn rounded depressed class="white--text" @click="Aceptar(item)" color="light-blue">Aceptar</v-btn>

            </v-col>
        </v-row> 
                
                
   
      
   
          

           
        

        <v-dialog v-model="dialog" max-width="500px">
            <v-card>
                <v-card-title
                    class="text-h5 aviso"
                    style="
                        justify-content: center;
                        background: #1d2735;
                        color: white;
                    "
                >
                    Cancelar
                </v-card-title>
                <v-card-text style="text-align: center">
                    <h2>Indique la razón</h2>
                    <v-textarea outlined v-model="seleccionado.observaciones"></v-textarea>
                </v-card-text>
                <v-card-actions class="pt-3">
                    <v-spacer></v-spacer>
                    <v-btn
                        color="error"
                        large
                        @click="
                            dialog = false;
                            selectedItem = {};
                        "
                        >Cancelar</v-btn
                    >
                    <v-btn color="success" large @click="Rechazar()"
                        >Confirmar</v-btn
                    >
                    <v-spacer></v-spacer>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-card>
</template>
<style>
.justify-breaks {
  text-align: justify;
  white-space: pre-line;
  padding: 1rem;
}
</style>
<script>
export default {
    data() {
        return {
            search: "",
            generate_dialog: false,
            cliente: {},
            contenido: [],
            selectedItem: 0,
            dialog: false,
            usuarios: [],
            index: 0,
            seleccionado:{},
            headers: [
                { text: "Contenido", value: "contenido" },
                { text: "Estado", value: "estado", sortable: false },
            ],
        };
    },
    created() {
        console.log(this.$route.path)
        if (this.$route.query.token) {
            this.getAllContenido();
        }
    },
    methods: {
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
        Rechazar(){
            axios
                .post(
                    `api/rechazar-contenido-redes/${this.$route.query.token}`,this.seleccionado
                )
                .then(
                    (res) => {
                        this.getAllContenido();
                        this.dialog();
                    },
                    (err) => {
                        this.$toast.error("Error consultando datos");
                    }
                );
        },
        Aceptar(item){
            axios
                .post(
                    `api/aceptar-contenido-redes/${this.$route.query.token}`,item
                )
                .then(
                    (res) => {
                        this.getAllContenido();
                    },
                    (err) => {
                        this.$toast.error("Error consultando datos");
                    }
                );
        },
        copyUrl() {
            navigator.clipboard.writeText(
                `https://plataforma.fidiaspro.com/#/social-cliente?token=${this.cliente.token_redes}`
            );
        },
        getItemType(item) {
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
        generateToken() {
            axios.get(`api/add-token-redes/${this.cliente.id}`).then(
                (res) => {
                    this.cliente.token_redes = res.data.token_redes;
                },
                (err) => {
                    this.$toast.error("Error generando token");
                }
            );
        },
        getActiveClientes() {
            axios.get(`api/get-all-clientes`).then(
                (res) => {
                    this.usuarios = res.data.users;
                    for (let i = 0; i < this.usuarios.length; i++) {
                        const element = this.usuarios[i];
                        element.created_at = new Date(
                            element.created_at
                        ).toLocaleDateString();
                    }
                },
                (err) => {
                    this.$toast.error("Error consultando Usuario");
                }
            );
        },
        getAllContenido() {
            axios
                .get(
                    `api/get-contenido-pendiente-redes/${this.$route.query.token}`
                )
                .then(
                    (res) => {
                        this.contenido = res.data;
                    },
                    (err) => {
                        this.$toast.error("Error consultando datos");
                    }
                );
        },
        deletepromocion() {
            this.dialog = false;
            axios
                .get(
                    `api/delete-promocion/${
                        this.contenido[this.selectedItem].id
                    }`
                )
                .then(
                    (res) => {
                        this.contenido.splice(this.selectedItem, 1);
                        this.$toast.sucs("promocion eliminada con exito");
                    },
                    (res) => {
                        this.$toast.error("Error eliminando promocion");
                    }
                );
        },
        changeActive(item) {
            axios.get(`api/change-promocion-active/${item.id}`).then(
                (res) => {
                    item.active = !item.active;
                    this.$toast.sucs("promocion actualizada");
                },
                (err) => {
                    this.$toast.error("Error consultando datos");
                }
            );
        },
        openModal(item) {
            this.seleccionado = item
            this.dialog = true;
        },
    },
    watch: {
        cliente(val) {
            if (val) {
                if (val.token_redes == null) {
                    this.generateToken();
                } else {
                    this.getAllContenido();
                }
            }
        },
    },
    computed: {
        isloading: function () {
            return this.$store.getters.getloading;
        },
        user: function () {
            return this.$store.getters.getuser;
        },
    },
};
</script>
