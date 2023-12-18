<template>
    <v-card class="pa-3 ma-3">
        <v-toolbar flat color="#1d2735" dark>
            <v-icon class="white--text" style="font-size: 45px"
                >mdi-account-supervisor-circle</v-icon
            >
            <pre><v-toolbar-title><h2>Lista Seguimiento</h2></v-toolbar-title></pre>
        </v-toolbar>
        <v-tooltip right>
            <template v-slot:activator="{ on, attrs }">
                <v-btn
                    fab
                    :to="{ path: `/calendario-seguimiento` }"
                    :loading="isloading"
                    :disabled="isloading"
                    color="primary darken-1"
                    class="mt-2"
                    v-bind="attrs"
                    v-on="on"
                >
                    <v-icon class="white--text">mdi-calendar</v-icon>
                </v-btn>
            </template>
            <span>Calendario Seguimientos</span>
        </v-tooltip>
        <v-row>
            <v-col cols="12" md="4">
                <date-select label="Fecha Inicio" v-model="fecha_inicio">
                </date-select>
            </v-col>
            <v-col cols="12" md="4">
                <date-select label="Fecha Fin" v-model="fecha_fin">
                </date-select>
            </v-col>
            <v-col cols="12" md="4">
                <v-btn
                    fab
                    @click="getSeguimientos"
                    :loading="isloading"
                    :disabled="isloading"
                    color="orange darken-1"
                    class="mt-2"
                    v-bind="attrs"
                    v-on="on"
                >
                    <v-icon class="white--text">mdi-filter</v-icon>
                </v-btn>
            </v-col>
        </v-row>
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
            :items="Seguimientos"
            :search="search"
            :items-per-page="15"
            item-key="id"
            class="elevation-1"
            :sort-by="['nombre']"
            :sort-desc="[false]"
        >
            <template v-slot:item.fecha="{ item }">
                <span>{{ item.fecha | format_date }}</span>
            </template>
            <template v-slot:item.action="{ item }">
                <router-link
                    :to="{ path: `/guardar-seguimiento?id=${item.id}` }"
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
            fecha_inicio: null,
            fecha_fin: null,
            search: "",
            headers: [
                {
                    text: "Nombre",
                    value: "proyecto.usuario.nombre",
                    sortable: false,
                },
                {
                    text: "Comentario",
                    value: "comentario",
                    sortable: false,
                },
                { text: "Fecha", value: "fecha", sortable: false },

                { text: "Acciones", value: "action", sortable: false },
            ],
            Seguimientos: [],
            selectedItem: 0,
            dialog: false,
        };
    },
    created() {
        //this.getSeguimientos()
    },
    methods: {
        getSeguimientos() {
            const self = this;
            let str = "";
            if (this.fecha_inicio) str += "&fecha_inicio=" + this.fecha_inicio;
            if (this.fecha_fin) str += "&fecha_fin=" + this.fecha_fin;

            axios
                .get(
                    `api/get-tareas-proyecto?filter=yes${str}&${
                        this.$route.meta.potencial
                            ? "potencial_only"
                            : "cliente_only"
                    }=true`
                )
                .then(
                    (res) => {
                        self.Seguimientos = res.data;
                    },
                    (err) => {
                        this.$toast.error("Error consultando Seguimientos");
                    }
                );
        },
        openModal(item) {
            this.selectedItem = this.Seguimientos.indexOf(item);
            this.dialog = true;
        },
        deleteUser() {
            axios
                .post("api/cancel-tareas-proyecto", {
                    id: this.Seguimientos[this.selectedItem].id,
                })
                .then(
                    (res) => {
                        this.$toast.sucs("Seguimiento eliminado");
                        this.dialog = false;
                        this.getSeguimientos();
                    },
                    (err) => {
                        this.$toast.error("Error eliminando Seguimiento");
                    }
                );
        },
    },
    computed: {
        isloading: function () {
            // return this.$store.getters.getloading
        },
    },
};
</script>
