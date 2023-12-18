<template>
    <v-card class="pa-3 ma-3">
        <v-toolbar flat color="#1d2735" dark>
            <v-icon class="white--text" style="font-size: 45px"
                >mdi-account-supervisor-circle</v-icon
            >
            <pre><v-toolbar-title><h2>Lista Usuarios</h2></v-toolbar-title></pre>
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
                    class="mt-2 mx-3"
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
                    :to="{ path: `/guardar-usuario` }"
                    :loading="isloading"
                    :disabled="isloading"
                    color="orange darken-1"
                    class="mt-2"
                    v-bind="attrs"
                    v-on="on"
                >
                    <v-icon class="white--text"
                        >mdi-account-plus-outline</v-icon
                    >
                </v-btn>
            </template>
            <span>Nuevo Usuario</span>
        </v-tooltip>
        <v-row>
            <v-col cols="12" md="4">
                <v-text-field
                    prepend-icon="mdi-account-search"
                    v-model="search"
                    label="Buscar"
                ></v-text-field>
            </v-col>
        </v-row>
        <v-data-table
            dense
            :headers="headers"
            :items="Usuarios"
            :search="search"
            :items-per-page="15"
            item-key="id"
            class="elevation-1"
            :sort-by="['nombre']"
            :sort-desc="[false]"
        >
            <template v-slot:item.fecha_alta="{ item }">
                <span>{{ item.fecha_alta }}</span>
            </template>
            <template v-slot:item.action="{ item }">
                <router-link
                    :to="{ path: `/guardar-usuario?id=${item.id}` }"
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
                    <v-btn color="success" large @click="deleteUser()"
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
            headers: [
                { text: "Usuario", value: "nombre", sortable: false },
                { text: "Email", value: "email", sortable: false },
                { text: "Perfil", value: "role", sortable: false },
                { text: "Fecha Alta", value: "fecha_alta", sortable: false },
                { text: "Acciones", value: "action", sortable: false },
            ],
            Usuarios: [],
            selectedItem: 0,
            dialog: false,
        };
    },
    created() {
        this.getUsuarios();
    },
    methods: {
        getUsuarios() {
            axios.post(`api/get-usuarios-empleados-all`).then(
                (res) => {
                    console.log(res.data.users);
                    this.Usuarios = res.data.users;

                    for (let i = 0; i < this.Usuarios.length; i++) {
                        const element = this.Usuarios[i];
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
        openModal(item) {
            this.selectedItem = this.Usuarios.indexOf(item);
            this.dialog = true;
        },
        deleteUser() {
            axios
                .get(
                    `api/delete-usuario/${this.Usuarios[this.selectedItem].id}`
                )
                .then(
                    (res) => {
                        this.$toast.sucs("Usuario eliminado");
                        this.dialog = false;
                    },
                    (err) => {
                        this.$toast.error("Error eliminando Usuario");
                    }
                );
            this.getUsuarios();
        },
    },
    computed: {
        isloading: function () {
            return this.$store.getters.getloading;
        },
    },
};
</script>
