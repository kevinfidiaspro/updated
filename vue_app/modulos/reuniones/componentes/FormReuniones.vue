<template>
    <v-container>
        <v-card shaped class="mx-4 my-4 pa-4">
            <v-row>
                <v-col cols="12">
                    <v-toolbar
                        flat
                        color="#1d2735"
                        dark
                        style="border-radius: 5px"
                    >
                        <v-icon class="white--text" style="font-size: 45px"
                            >mdi-account-supervisor-circle</v-icon
                        >
                        <pre><v-toolbar-title><h2>Crear reunion</h2></v-toolbar-title></pre>
                    </v-toolbar>
                    <v-form class="mt-3">
                        <v-row>
                            <v-col cols="12" md="4">
                                <date-select
                                    v-model="reunion.fecha"
                                    label="Fecha"
                                >
                                </date-select>
                            </v-col>
                            <v-col col="12" md="4">
                                <v-autocomplete
                                    outlined
                                    label="Hora Desde"
                                    v-model="reunion.hora_desde"
                                    :items="horas"
                                    item-value="value"
                                    item-text="text"
                                >
                                </v-autocomplete
                            ></v-col>

                            <v-col col="12" md="4">
                                <v-autocomplete
                                    outlined
                                    label="Hora Hasta"
                                    v-model="reunion.hora_hasta"
                                    :items="horas"
                                    item-value="value"
                                    item-text="text"
                                >
                                </v-autocomplete
                            ></v-col>
                        </v-row>
                        <v-row>
                            <v-col cols="6">
                                <v-autocomplete
                                    label="Asistente"
                                    v-model="id_asistente"
                                    :items="empleados"
                                    item-text="nombre"
                                    item-value="id"
                                ></v-autocomplete>
                            </v-col>
                            <v-col cols="6">
                                <v-btn
                                    label="asistente"
                                    @click="AddAsistente"
                                    color="success"
                                    >Agregar Asistente</v-btn
                                >
                            </v-col>
                            <v-col cols="12">
                                <v-data-table
                                    :headers="headers"
                                    :items="reunion.asistentes"
                                    item-key="id"
                                    class="elevation-1"
                                >
                                    <template v-slot:item.action="{ item }">
                                        <v-icon
                                            @click="
                                                deleteAsistente(
                                                    item.id_asistente
                                                )
                                            "
                                            small
                                            class="mr-2"
                                            color="red"
                                        >
                                            mdi-trash-can
                                        </v-icon>
                                    </template>
                                </v-data-table>
                            </v-col>
                            <v-col cols="12">
                                <v-textarea
                                    label="Comentario"
                                    v-model="reunion.comentario"
                                >
                                </v-textarea>
                            </v-col>
                        </v-row>

                        <v-row>
                            <v-col cols="12">
                                <v-btn
                                    v-if="!reunion.id"
                                    :disabled="isloading"
                                    color="success"
                                    class="white--text"
                                    @click="savereunion"
                                    >Guardar</v-btn
                                >
                                <v-btn
                                    @click="savereunion"
                                    v-if="reunion.id"
                                    :disabled="isloading"
                                    color="success"
                                    class="white--text"
                                    >Actualizar</v-btn
                                >
                                <v-btn
                                    @click="openDeleteDialog"
                                    v-if="reunion.id"
                                    :disabled="isloading"
                                    color="red"
                                    class="white--text"
                                    >Eliminar</v-btn
                                >
                            </v-col>
                        </v-row>
                    </v-form>
                </v-col>
            </v-row>
        </v-card>

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
                    <v-btn color="success" large @click="deleteReunion()"
                        >Confirmar</v-btn
                    >
                    <v-spacer></v-spacer>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-container>
</template>

<script>
export default {
    data() {
        return {
            headers: [
                {
                    text: "Nombre",
                    value: "asistente.nombre",
                },
                {
                    text: "Acciones",
                    value: "action",
                    sortable: false,
                },
            ],
            dialog: false,
            id_asistente: null,
            horas: [],
            proyectos: [],
            empleados: [],
            reunion: {
                asistentes: [],
            },
        };
    },
    watch: {},
    created() {
        this.generateHoras();
        this.getUsuarios();
        if (this.$route.query.id) {
            this.getreunion(this.$route.query.id);
        }
    },
    methods: {
        deleteReunion() {
            axios.post(`api/delete-reunion`, { id: this.reunion.id }).then(
                (res) => {
                    this.$router.push("calendario-reuniones");
                },
                (err) => {
                    this.$toast.error("Error consultando empleados");
                }
            );
        },
        openDeleteDialog() {
            this.dialog = true;
        },
        AddAsistente() {
            if (this.id_asistente) {
                this.reunion.asistentes.push({
                    id_asistente: this.id_asistente,
                    asistente: this.empleados.find(
                        (element) => element.id == this.id_asistente
                    ),
                });
            }
            this.id_asistente = null;
        },
        deleteAsistente(id) {
            if (id) {
                const asis = this.reunion.asistentes.find(
                    (element) => element.id_asistente == id
                );
                const index = this.reunion.asistentes.indexOf(asis);
                if (index > -1) {
                    // only splice array when item is found

                    this.reunion.asistentes.splice(index, 1); // 2nd parameter means remove one item only
                }
            }
            this.id_asistente = null;
        },
        getUsuarios() {
            axios.post(`api/get-usuarios-empleados-all`).then(
                (res) => {
                    this.empleados = res.data.users;
                },
                (err) => {
                    this.$toast.error("Error consultando empleados");
                }
            );
        },
        generateHoras() {
            this.horas = [];
            for (let val = 9; val <= 19; val += 0.5) {
                let str = val.toString().split(".");
                let txt = "";
                if (str.length > 1) {
                    txt = str[0].toString().padStart(2, "0") + ":30";
                } else {
                    txt = val.toString().padStart(2, "0") + ":00";
                }
                this.horas.push({ text: txt, value: val });
            }
        },
        getreunion(id) {
            const self = this;
            axios.get(`api/get-reunion?id=${id}`).then(function (response) {
                self.reunion = response.data;
                console.log(self.reunion);
            });
        },
        savereunion() {
            const self = this;
            console.log(this.reunion);
            axios
                .post("api/save-reunion", this.reunion)
                .then(function (response) {
                    self.$router.push("calendario-reuniones");
                });
        },
        getProyectos() {
            const self = this;
            axios.get("api/get-all-potenciales").then(function (response) {
                self.proyectos = response.data;
            });
        },
        createreunion() {},
    },
    filters: {},
};
</script>
