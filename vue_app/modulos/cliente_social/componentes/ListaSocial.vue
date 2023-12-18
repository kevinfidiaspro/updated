<template>
    <v-card class="pa-3 ma-3">
        <v-toolbar flat color="#1d2735" dark>
            <v-icon class="white--text" style="font-size: 45px">
                mdi-bullhorn
            </v-icon>
            <pre><v-toolbar-title><h2>Listado Redes Sociales</h2></v-toolbar-title></pre>
        </v-toolbar>
        <loader v-if="isloading"></loader>
        <v-tooltip right>
            <template v-slot:activator="{ on, attrs }">
                <v-btn
                    fab
                    :to="'/'"
                    :loading="isloading"
                    :disabled="isloading"
                    color="blue"
                    class="mx-3 mt-2"
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
        <v-tooltip right>
            <template v-slot:activator="{ on, attrs }">
                <v-btn
                    v-if="user.role != null && user.role != 2 && user.role !=4"
                    fab
                    :to="{
                        path: `/form-social${
                            cliente.id ? `?id_cliente=${cliente.id}` : ''
                        }`,
                    }"
                    :loading="isloading"
                    :disabled="isloading"
                    color="orange darken-1"
                    class="mt-2"
                    v-bind="attrs"
                    v-on="on"
                >
                    <v-icon class="white--text">mdi-plus-box</v-icon>
                </v-btn>
            </template>
            <span>Nuevo Contenido</span>
        </v-tooltip>
        <v-tooltip right   v-if="cliente.id != null">
            <template v-slot:activator="{ on, attrs }">
                <v-btn
                  
                    fab
                    @click="sendEmail"
                    :loading="isloading"
                    :disabled="isloading"
                    color="orange darken-1"
                    class="mt-2"
                    v-bind="attrs"
                    v-on="on"
                >
                    <v-icon class="white--text">mdi-gmail</v-icon>
                </v-btn>
            </template>
            <span>Enviar Notificacion</span>
        </v-tooltip>
        <v-row>
            <v-col cols="12" md="4">
                <v-autocomplete
                    outlined
                    label="Cliente"
                    v-model="cliente"
                    return-object
                    item-text="nombre"
                    :items="searchResults"
                    :search-input.sync="searchQuery"
                    :filter="customFilter"
                ></v-autocomplete>
            </v-col>
            <v-col cols="12" md="8" v-if="cliente.token_redes">
                <div style="display: flex">
                    <v-text-field
                        label="Url Usuario"
                        :value="`https://plataforma.fidiaspro.com/#/social-cliente?token=${cliente.token_redes}`"
                    ></v-text-field>
                    <v-btn color="primary" @click="copyUrl"
                        ><v-icon class="white--text"
                            >mdi-content-copy</v-icon
                        ></v-btn
                    >
                </div>
            </v-col>
        </v-row>
        <v-data-table
            :headers="headers"
            :search="search"
            :items-per-page="10"
            :items="contenido"
            item-key="id"
            class="elevation-1"
        >
        <template v-slot:item.imagenes="{item}">
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
        </template>
            <template v-slot:item.fecha_publicacion="{ item }">
                <span>{{ item.fecha_publicacion | format_date }}</span>
            </template>
            <template v-slot:item.estado="{ item }">
                <v-chip
                    v-if="item.estado"
                    class="ma-2 white--text"
                    :color="
                        item.id_estado == 1 
                            ? 'green'
                            : item.id_estado == 2
                            ? 'red'
                            : '#e1ad01'
                    "
                >
                    {{ item.estado.nombre??'Cambiar Estado' }}
                </v-chip>
              
            </template>
            <template v-slot:item.action="{ item }">
                <router-link
                    v-if="user.role != null && user.role != 2 && user.role !=4"
                    :to="{ path: `/form-social?id=${item.id}` }"
                    class="action-buttons"
                >
                    <v-icon
                        small
                        class="mr-2"
                        color="#1d2735"
                        style="font-size: 25px"
                        title="EDITAR"
                        >mdi-pencil-outline</v-icon
                    >
                </router-link>
                <v-icon
                    v-if="user.role != null && user.role != 2 && user.role !=4"
                    @click="openModal(item)"
                    small
                    class="mr-2"
                    color="red"
                    style="font-size: 25px"
                    title="BORRAR"
                >
                    mdi-trash-can
                </v-icon>
            </template>
            <template v-slot:item.file_name="{ item }">
                <img
                    :src="item.path"
                    style="width: 150px; height: 40px"
                    class="mt-1"
                />
            </template>
            <template v-slot:item.url="{ item }">
                <!--<span v-if="item.url != 'undefined'"> <a :href="'https://www.'+item.url" target="_blank">{{item.url}}</a></span>-->
                <span v-if="item.url != 'undefined'">
                    <a :href="item.url" target="_blank">{{ item.url }}</a></span
                >
                <span v-else>No Asignada</span>
            </template>
        </v-data-table>

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
                    Aviso
                </v-card-title>
                <v-card-text style="text-align: center">
                    <h2>¿Estás seguro que deseas eliminar?</h2>
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
                    <v-btn color="success" large @click="deletepromocion()"
                        >Confirmar</v-btn
                    >
                    <v-spacer></v-spacer>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-card>
</template>

<script>
export default {
    data() {
        return {
            searchQuery: "",
            search: "",
            generate_dialog: false,
            cliente: {},
            searchResults: [],
            contenido: [],
            selectedItem: 0,
            dialog: false,
            usuarios: [],
            headers: [
                { text: "Imagenes", value: "imagenes" },
                { text: "Observaciones", value: "observaciones" },
                { text: "Fecha publicacion", value: "fecha_publicacion" },

                { text: "Estado", align: "center", value: "estado" },
                { text: "Acciones", value: "action", sortable: false },
            ],
        };
    },
    mounted() {
        this.getActiveClientes();
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
        customFilter(item, queryText, itemText) {
            const nombreMatch = item.nombre
                .toLowerCase()
                .includes(queryText.toLowerCase());
            const proyectoNombreMatch = item.proyectos.some((proyecto) =>
                proyecto.nombre.toLowerCase().includes(queryText.toLowerCase())
            );
            return nombreMatch || proyectoNombreMatch;
        },
        buscar(query) {
            this.searchResults = this.usuarios.filter((item) => {
                const nombreMatch = item.nombre
                    .toLowerCase()
                    .includes(query.toLowerCase());
                const proyectoNombreMatch = item.proyectos.some((proyecto) =>
                    proyecto.nombre.toLowerCase().includes(query.toLowerCase())
                );
                return nombreMatch || proyectoNombreMatch;
            });
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
        copyUrl() {
            navigator.clipboard.writeText(
                `https://plataforma.fidiaspro.com/#/social-cliente?token=${this.cliente.token_redes}`
            );
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
        sendEmail() {
            axios.post(`api/send-notification-social-mail`,{id:this.cliente.id}).then(
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
                    this.searchResults = res.data.users;
                    if (this.$route.query.id) {
                        this.cliente = this.usuarios.find((element) => {
                            return element.id == this.$route.query.id;
                        });
                    }
                },
                (err) => {
                    this.$toast.error("Error consultando Usuario");
                }
            );
        },
        getAllContenido() {
            axios
                .get(`api/get-contenido-redes/${this.cliente.token_redes}`)
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
                    `api/delete-contenido-redes/${
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
            this.selectedItem = this.contenido.indexOf(item);
            this.dialog = true;
        },
    },
    watch: {
        searchQuery(val) {},
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
