<template>
    <v-card class="pa-3 ma-3">
        <v-toolbar flat color="#1d2735" dark>
            <v-icon class="white--text" style="font-size: 45px"
                >mdi-microsoft-powerpoint</v-icon
            >
            <pre><v-toolbar-title><h2>Lista Consumos</h2></v-toolbar-title></pre>
        </v-toolbar>
        <loader v-if="isloading"></loader>

        <v-row style="margin-top: 1rem">
            <v-col cols="12" md="4">
                <date-select
                    type="month"
                    label="Mes"
                    v-model="fecha"
                ></date-select>
            </v-col>
            <v-col cols="12" md="4">
                <v-autocomplete
                    v-model="id_usuario"
                    :items="usuarios"
                    item-text="nombre"
                    item-value="id"
                    label="Usuarios"
                >
                </v-autocomplete>
            </v-col>
        </v-row>
        <v-data-table
            dense
            :headers="headers"
            :items="proyectos"
            :search="search"
            :items-per-page="15"
            item-key="id"
            class="elevation-0"
            :sort-by="['usuario.nombre']"
            :sort-desc="[false]"
        >
            <template v-slot:item.usuario.nombre="{ item }">
                <span v-if="item.usuario">{{ item.usuario.nombre }}</span>
                <span v-else style="color: red !important"
                    ><strong>Cliente Eliminado</strong></span
                >
            </template>
            <template v-slot:item.action="{ item }">
                <router-link
                    :to="{
                        path: `/guardar-proyecto?id=${item.id}&tipo=${$route.query.tipo}`,
                    }"
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
                    @click="openModal(item)"
                    small
                    class="mr-2"
                    color="red"
                    style="font-size: 25px"
                    title="BORRAR"
                    >mdi-trash-can</v-icon
                >
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
                    <v-btn color="success" large @click="deleteProyecto()"
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
            search: "",
            proyectos: [],
            usuarios: [],
            id_usuario: null,
            fecha: new Date().toISOString(),
            selectedItem: 0,
            clienteIdProyect: 0,
            dialog: false,
            headers: [
                { text: "Cliente", value: "cliente", sortable: false },
                { text: "Proyecto", value: "nombre", sortable: false },
                { text: "Tipo", value: "tipo", sortable: false },
                {
                    text: "Horas Estimadas",
                    value: "estimadas",
                    sortable: false,
                },
                { text: "Horas Reales", value: "reales", sortable: false },
                { text: "Diferencia", value: "diferencia", sortable: false },
            ],
        };
    },
    created() {
        this.clienteIdProyect = this.$route.query.id;
        this.getAllProyectos();
        this.getUsuarios();
    },
    watch: {
        fecha(val) {
            this.getAllProyectos();
        },
        id_usuario(val) {
            this.getAllProyectos();
        },
    },
    methods: {
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
        getAllProyectos() {
            axios
                .get(
                    `api/get-consumo-proyectos?mes=${this.fecha}${
                        this.id_usuario == null
                            ? ""
                            : `&id_usuario=${this.id_usuario}`
                    }`
                )
                .then(
                    (res) => {
                        this.proyectos = res.data.data;
                        console.log(this.proyectos);
                    },
                    (res) => {
                        this.$toast.error("Error consultando proyectos");
                    }
                );
        },
    },
    computed: {
        isloading: function () {
            return this.$store.getters.getloading;
        },
    },
};
</script>
