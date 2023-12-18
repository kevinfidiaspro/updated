<template>
    <v-card class="pa-3 ma-3">
        <v-toolbar flat color="#1d2735" dark>
            <v-icon class="white--text" style="font-size: 45px"
                >mdi-file-tree</v-icon
            >
            <pre><v-toolbar-title><h2 style="margin-left:20px;"> Tareas</h2></v-toolbar-title></pre>
        </v-toolbar>
        <loader v-if="isloading"></loader>
        <v-tabs horizontal>
            <v-tab>
                <v-icon left>mdi-book-variant-multiple</v-icon>Añadir Tareas
            </v-tab>
            <v-tab> <v-icon left>mdi-account</v-icon>Buscar Tareas </v-tab>
            <!--Tab 1 Añadir Tareas-->

            <v-tab-item class="pa-3 ma-1">
                <v-card flat>
                    <v-row style="margin-top: 10px">
                        <v-col cols="12" md="3">
                            <v-menu
                                ref="menu"
                                v-model="menu"
                                :close-on-content-click="false"
                                :return-value.sync="filtros.fecha"
                                transition="scale-transition"
                                offset-y
                                min-width="290px"
                            >
                                <template v-slot:activator="{ on, attrs }">
                                    <v-text-field
                                        v-model="filtros.fecha"
                                        label="Fecha"
                                        append-icon="mdi-calendar"
                                        v-bind="attrs"
                                        v-on="on"
                                    >
                                    </v-text-field>
                                </template>
                                <v-date-picker
                                    color="#1d2735"
                                    first-day-of-week="1"
                                    v-model="filtros.fecha"
                                    no-title
                                    scrollable
                                >
                                    <v-spacer></v-spacer>
                                    <v-btn
                                        text
                                        color="red"
                                        @click="menu = false"
                                        ><strong>Cancelar</strong></v-btn
                                    >
                                    <v-btn
                                        text
                                        color="success"
                                        @click="$refs.menu.save(filtros.fecha)"
                                        ><strong>OK</strong></v-btn
                                    >
                                </v-date-picker>
                            </v-menu>
                        </v-col>
                        <v-col cols="12" md="3">
                            <v-autocomplete
                                dense
                                outlined
                                v-model="tarea.id_proyecto"
                                :items="proyectos_filtrados"
                                item-text="nombre"
                                item-value="id"
                                label="Proyectos"
                            >
                            </v-autocomplete>
                        </v-col>

                        <v-col cols="12" md="2">
                            <v-text-field
                                dense
                                outlined
                                type="number"
                                v-model="tarea.tiempo"
                                label="Minutos"
                            >
                            </v-text-field>
                        </v-col>
                    </v-row>
                    <v-row>
                        <v-col cols="12" md="10">
                            <v-textarea
                                dense
                                outlined
                                height="60px"
                                v-model="tarea.descripcion"
                                label="Descripción"
                            >
                            </v-textarea>
                        </v-col>
                        <v-col cols="12" md="1">
                            <v-tooltip right>
                                <template v-slot:activator="{ on, attrs }">
                                    <v-btn
                                        fab
                                        @click="addTarea"
                                        :loading="isloading"
                                        :disabled="isloading"
                                        color="orange darken-1"
                                        class="mt-2"
                                        v-bind="attrs"
                                        v-on="on"
                                    >
                                        <v-icon class="white--text"
                                            >mdi-playlist-plus</v-icon
                                        >
                                    </v-btn>
                                </template>
                                <span>Añadir</span>
                            </v-tooltip>
                        </v-col>
                    </v-row>
                    <v-row style="justify-content: space-between">
                        <v-col
                            v-if="semanal == 0"
                            cols="12"
                            md="8"
                            style="text-align: left"
                        >
                            <span style="font-weight: bold"
                                >Minutos utilizado:</span
                            >
                            {{ tiempo_utilizado }}<br />
                            <span style="font-weight: bold"
                                >Minutos asignados:</span
                            >
                            {{ tiempo_asignado }}<br />
                            <span style="font-weight: bold"
                                >Minutos disponibles:</span
                            >
                            <span
                                :style="
                                    tiempo_asignado - tiempo_utilizado < 0
                                        ? 'color:red'
                                        : ''
                                "
                                >{{ tiempo_asignado - tiempo_utilizado }}</span
                            >
                        </v-col>
                        <v-col
                            v-if="semanal == 1"
                            cols="12"
                            md="8"
                            style="text-align: left"
                        >
                            <span style="font-weight: bold"
                                >Minutos utilizado esta semana:</span
                            >
                            {{ tiempo_utilizado }}<br />
                            <span style="font-weight: bold"
                                >Minutos asignados esta semana:</span
                            >
                            {{ tiempo_asignado }}<br />
                            <span style="font-weight: bold" v-if="pasado < 0"
                                >Minutos excedidos semana pasada:</span
                            >
                            <span v-if="pasado < 0" style="color: red">{{
                                pasado
                            }}</span
                            ><br v-if="pasado < 0" />
                            <span style="font-weight: bold"
                                >Minutos disponibles esta semana:</span
                            >
                            <span
                                :style="
                                    tiempo_asignado -
                                        tiempo_utilizado +
                                        pasado <
                                    0
                                        ? 'color:red'
                                        : ''
                                "
                                >{{
                                    tiempo_asignado - tiempo_utilizado + pasado
                                }}</span
                            >
                        </v-col>
                        <v-col
                            v-if="semanal == 2"
                            cols="12"
                            md="8"
                            style="text-align: left"
                        >
                            <span style="font-weight: bold"
                                >Minutos utilizado este mes:</span
                            >
                            {{ tiempo_utilizado }}<br />
                            <span style="font-weight: bold"
                                >Minutos asignados este mes:</span
                            >

                            {{ tiempo_asignado }}<br />
                            <span style="font-weight: bold" v-if="pasado < 0"
                                >Minutos excedidos mes pasado:</span
                            >
                            <span v-if="pasado < 0" style="color: red">{{
                                pasado
                            }}</span
                            ><br v-if="pasado < 0" />
                            <span style="font-weight: bold"
                                >Minutos disponibles este mes:</span
                            >
                            <span
                                :style="
                                    tiempo_asignado -
                                        tiempo_utilizado +
                                        pasado <
                                    0
                                        ? 'color:red'
                                        : ''
                                "
                                >{{
                                    tiempo_asignado - tiempo_utilizado + pasado
                                }}</span
                            >
                        </v-col>
                        <v-col cols="12" md="10">
                            <img
                                v-if="tiempo_asignado - tiempo_utilizado < 0"
                                height="auto"
                                width="150px"
                                src="trsite.jpg"
                            />
                        </v-col>
                        <v-col cols="12" md="2">
                            <v-text-field
                                readonly
                                disabled
                                v-model="total"
                                label="Total minutos dedicados"
                            ></v-text-field>
                        </v-col>
                    </v-row>
                    <v-data-table
                        style="margin-top: 20px"
                        dense
                        :headers="headers"
                        :items="tareas"
                        :search="search"
                        item-key="id"
                        class="elevation-1"
                        :sort-by="['nombre']"
                        :sort-desc="[false]"
                    >
                        <template v-slot:item.action="{ item }">
                            <v-icon
                                @click="editTarea(item)"
                                small
                                class="mr-2"
                                color="blue"
                                >mdi-pencil</v-icon
                            >
                            <v-icon
                                @click="deleteTarea(item)"
                                small
                                class="mr-2"
                                color="red"
                                >mdi-trash-can</v-icon
                            >
                        </template>
                    </v-data-table>
                </v-card>
            </v-tab-item>
            <!--Tab 2 Buscar Tareas-->
            <v-tab-item>
                <v-card>
                    <v-row style="margin-bottom: 15px; margin-top: 15px">
                        <v-col cols="12" md="3" v-if="rol == 1 || rol == 7">
                            <v-autocomplete
                                dense
                                outlined
                                v-model="filtros2.usuario"
                                :items="usuarios"
                                item-text="nombre"
                                item-value="id"
                                label="Usuarios"
                            >
                            </v-autocomplete>
                        </v-col>
                        <v-col cols="12" md="3">
                            <v-autocomplete
                                dense
                                outlined
                                v-model="filtros2.proyecto"
                                :items="proyectos"
                                item-text="nombre_completo"
                                item-value="id"
                                label="Proyectos"
                            >
                            </v-autocomplete>
                        </v-col>
                        <v-col cols="12" md="2">
                            <v-menu
                                ref="menu"
                                v-model="menu3"
                                :close-on-content-click="false"
                                :return-value.sync="filtros2.fecha_inicio"
                                transition="scale-transition"
                                offset-y
                                min-width="290px"
                            >
                                <template v-slot:activator="{ on, attrs }">
                                    <v-text-field
                                        v-model="filtros2.fecha_inicio"
                                        label="Fecha Inicio"
                                        append-icon="mdi-calendar"
                                        v-bind="attrs"
                                        v-on="on"
                                    >
                                    </v-text-field>
                                </template>
                                <v-date-picker
                                    color="#1d2735"
                                    first-day-of-week="1"
                                    v-model="filtros2.fecha_inicio"
                                    no-title
                                    scrollable
                                >
                                    <v-spacer></v-spacer>
                                    <v-btn
                                        text
                                        color="red"
                                        @click="menu = false"
                                        ><strong>Cancelar</strong></v-btn
                                    >
                                    <v-btn
                                        text
                                        color="success"
                                        @click="
                                            $refs.menu.save(
                                                filtros2.fecha_inicio
                                            )
                                        "
                                        ><strong>OK</strong></v-btn
                                    >
                                </v-date-picker>
                            </v-menu>
                        </v-col>
                        <v-col cols="12" md="2">
                            <v-menu
                                ref="menu2"
                                v-model="menu2"
                                :close-on-content-click="false"
                                :return-value.sync="filtros2.fecha_fin"
                                transition="scale-transition"
                                offset-y
                                min-width="290px"
                            >
                                <template v-slot:activator="{ on, attrs }">
                                    <v-text-field
                                        v-model="filtros2.fecha_fin"
                                        label="Fecha Fin"
                                        append-icon="mdi-calendar"
                                        v-bind="attrs"
                                        v-on="on"
                                    >
                                    </v-text-field>
                                </template>
                                <v-date-picker
                                    color="#1d2735"
                                    first-day-of-week="1"
                                    v-model="filtros2.fecha_fin"
                                    no-title
                                    scrollable
                                >
                                    <v-spacer></v-spacer>
                                    <v-btn
                                        text
                                        color="red"
                                        @click="menu2 = false"
                                        ><strong>Cancelar</strong></v-btn
                                    >
                                    <v-btn
                                        text
                                        color="success"
                                        @click="
                                            $refs.menu2.save(filtros2.fecha_fin)
                                        "
                                        ><strong>OK</strong></v-btn
                                    >
                                </v-date-picker>
                            </v-menu>
                        </v-col>
                        <v-col cols="12" md="2">
                            <v-tooltip right>
                                <template v-slot:activator="{ on, attrs }">
                                    <v-btn
                                        fab
                                        @click="buscarTareas"
                                        :loading="isloading"
                                        :disabled="isloading"
                                        color="orange darken-1"
                                        class="mt-2"
                                        v-bind="attrs"
                                        v-on="on"
                                    >
                                        <v-icon class="white--text"
                                            >mdi-filter</v-icon
                                        >
                                    </v-btn>
                                </template>
                                <span>Filtrar</span>
                            </v-tooltip>
                        </v-col>
                    </v-row>
                    <v-row style="justify-content: end">
                        <v-col cols="6" md="2">
                            <v-text-field
                                readonly
                                disabled
                                v-model="minutos_estimados"
                                label="Total minutos Estimados"
                            ></v-text-field>
                        </v-col>
                        <v-col cols="6" md="2">
                            <v-text-field
                                readonly
                                disabled
                                v-model="total"
                                label="Total minutos dedicados"
                            ></v-text-field>
                        </v-col>
                    </v-row>
                    <v-data-table
                        dense
                        :headers="headers2"
                        :items="tareas"
                        :search="search"
                        item-key="id"
                        class="elevation-1"
                        :sort-by="['nombre']"
                        :sort-desc="[false]"
                    >
                    </v-data-table>
                </v-card>
            </v-tab-item>
        </v-tabs>
        <v-tooltip right>
            <template v-slot:activator="{ on, attrs }">
                <v-btn
                    fab
                    :to="'/'"
                    :loading="isloading"
                    :disabled="isloading"
                    color="blue"
                    class="mx-3"
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
                    fab
                    :loading="isloading"
                    :disabled="isloading"
                    color="green"
                    class="mx-3"
                    v-bind="attrs"
                    v-on="on"
                    @click="Exportar()"
                >
                    <v-icon class="white--text">mdi-file-excel</v-icon>
                </v-btn>
            </template>
            <span>Descargar</span>
        </v-tooltip>
    </v-card>
</template>
<script>
export default {
    data() {
        return {
            minutos_estimados: 0,
            pasado: 0,
            tiempo_utilizado: 0,
            tiempo_asignado: 0,
            semanal: 1,
            menu: false,
            menu2: false,
            menu3: false,
            total: 0,
            editar: false,
            tiempo_tarea_sin_editar: 0,
            tarea: {
                id: "null",
                id_proyecto: "null",
                fecha: "",
                descripcion: "",
                tiempo: 0,
                id_usuario: localStorage.getItem("user_id"),
            },
            filtros: {
                fecha: null,
                id_usuario: localStorage.getItem("user_id"),
            },
            filtros2: {
                fecha_inicio: null,
                fecha_fin: null,
                usuario: null,
                proyecto: null,
            },
            search: "",
            headers: [
                // {text: 'Id',value: 'id',sortable: false},
                { text: "Fecha", value: "fecha", sortable: false },
                { text: "Proyecto", value: "nombre_proyecto", sortable: true },
                { text: "Descripcion", value: "descripcion", sortable: false },
                { text: "Tiempo", value: "tiempo", sortable: false },
                { text: "Acciones", value: "action", sortable: false },
            ],
            headers2: [
                // {text: 'Id',value: 'id',sortable: false},
                { text: "Fecha", value: "fecha", sortable: false },
                { text: "Empleado", value: "nombre_usuario", sortable: true },
                { text: "Proyecto", value: "nombre_proyecto", sortable: true },
                { text: "Descripcion", value: "descripcion", sortable: false },
                { text: "Tiempo", value: "tiempo", sortable: false },
            ],
            tareas: [],
            proyectos: [],
            proyectos_filtrados: [],
            rol: "",
            usuarios: [],
        };
    },
    created() {
        this.rol = localStorage.getItem("role");
        this.getUsuarios();
        this.getProyectos();
        const formatYmd = (date) => date.toISOString().slice(0, 10);
        this.filtros.fecha = formatYmd(new Date());
        var date = new Date();
        var fecha_inicio = new Date(date.getFullYear(), date.getMonth(), 1);
        var fecha_fin = new Date(date.getFullYear(), date.getMonth() + 1, 0);
        this.filtros2.fecha_inicio = fecha_inicio.toISOString().slice(0, 10);
        this.filtros2.fecha_fin = fecha_fin.toISOString().slice(0, 10);
        //this.getTareas();
    },
    watch: {
        "filtros2.proyecto": function (val) {
            const proyecto = this.proyectos.find(
                (element) => element.id == val
            );
            this.minutos_estimados = proyecto?.minutos_estimados ?? 0;
            console.log(proyecto);
        },
        "tarea.id_proyecto": function (val) {
            this.getTiempoSemana();
        },
        "filtros.fecha"(n) {
            this.getTareas();
        },
    },
    methods: {
        getTiempoSemana() {
            axios.post("api/tiempo-tarea-proyecto", this.tarea).then((res) => {
                this.tiempo_utilizado = res.data.tiempo;
                this.tiempo_asignado = res.data.asignados;
                this.semanal = res.data.semanal;
                this.pasado = res.data.pasado;
            });
        },
        getUsuarios() {
            axios.post(`api/get-usuarios-empleados`).then(
                (res) => {
                    this.usuarios = res.data.users.data;
                    this.usuarios.unshift({ nombre: "Todos", id: null });
                },
                (err) => {
                    this.$toast.error("Error consultando Fichajes");
                }
            );
        },
        Exportar() {
            axios
                .post(`api/exportar-tareas`, this.filtros2, {
                    responseType: "blob",
                })
                .then((res) => {
                    const url = window.URL.createObjectURL(
                        new Blob([res.data])
                    );
                    const link = document.createElement("a");
                    link.href = url;
                    link.setAttribute("download", "tareas.xlsx");
                    document.body.appendChild(link);
                    link.click();
                })
                .catch((err) => {
                    console.error(err);
                });
        },
        buscarTareas() {
            axios.post(`api/buscar-tareas`, this.filtros2).then(
                (res) => {
                    this.tareas = res.data;
                    this.total = 0;
                    this.tareas.forEach((element) => {
                        this.total = this.total + element.tiempo;
                    });
                },
                (err) => {}
            );
        },
        getTareas() {
            console.log(this.filtros);
            axios.post(`api/get-tareas`, this.filtros).then(
                (res) => {
                    this.tareas = res.data;
                    this.total = 0;
                    this.tareas.forEach((element) => {
                        this.total = this.total + element.tiempo;
                    });
                },
                (err) => {}
            );
        },
        addTarea() {
            this.tarea.fecha = this.filtros.fecha;

            axios.post(`api/save-tarea`, this.tarea).then(
                (res) => {
                    this.tareas = res.data;
                    this.$toast.success("Tarea creada correctamente");
                    this.total = 0;
                    this.tareas.forEach((element) => {
                        this.total = this.total + element.tiempo;
                    });
                    this.resetCampos();
                },
                (err) => {
                    this.$toast.error("Error creando tarea");
                }
            );
        },
        editTarea(item) {
            this.tarea = item;
        },
        deleteTarea(item) {
            axios.post(`api/delete-tarea/${item.id}`).then(
                (res) => {
                    this.$toast.success("Tarea eliminada correctamente");
                    this.getTareas();
                },
                (err) => {
                    this.$toast.error("Error eliminado tarea");
                }
            );
        },
        getProyectos() {
            axios.get(`api/get-proyectos-activos`, this.filtros).then(
                (res) => {
                    this.proyectos = res.data;
                    this.proyectos.unshift({
                        nombre_completo: "Todos",
                        id: null,
                    });
                },
                (err) => {
                    this.$toast.error("Error consultando Proyectos");
                }
            );
            axios.get(`api/get-proyectos-by-user`, this.filtros).then(
                (res) => {
                    this.proyectos_filtrados = res.data;
                },
                (err) => {
                    this.$toast.error("Error consultando Proyectos");
                }
            );
        },
        resetCampos() {
            this.tarea = {
                id: "null",
                id_proyecto: "null",
                fecha: "",
                descripcion: "",
                tiempo: "",
                id_usuario: localStorage.getItem("user_id"),
            };
        },
    },
    computed: {
        isloading: function () {
            return this.$store.getters.getloading;
        },
    },
};
</script>
