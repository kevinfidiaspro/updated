<template>
    <v-card>
        <v-toolbar flat color="#1d2735" dark>
            <v-icon class="white--text" style="font-size: 45px">
                mdi-bullhorn
            </v-icon>
            <pre><v-toolbar-title><h2>Marketing SVD</h2></v-toolbar-title></pre>
        </v-toolbar>
        <loader v-if="isloading"></loader>

        <v-data-table
            :headers="headers"
            :search="search"
            :items-per-page="10"
            :items="seguimientos"
            item-key="id"
            class="elevation-1"
        >
        <template v-slot:item.gastos_totales="{ item }">
                {{ item.gastos_totales }} €
            </template>
            <template v-slot:item.total_venta="{ item }">
                {{ item.total_venta}} €
            </template>
            <template v-slot:item.beneficio="{ item }">
                {{ item.total_venta - item.gastos_totales}} €
            </template>
            <template v-slot:item.cdv_ventas="{ item }">
                {{ item.tasa_med != 0?item.total_venta/item.tasa_med:0}} €
            </template>
            <template v-slot:item.cdv_beneficios="{ item }">
                {{ item.tasa_med != 0?(item.total_venta-item.gastos_totales)/item.tasa_med:0}} €
            </template>
            <template v-slot:item.active="{ item }">
                <v-chip
                    v-if="user.role == 1"
                    @click="changeActive(item)"
                    class="ma-2 white--text"
                    :color="item.active ? 'green' : 'red'"
                >
                    {{ item.active ? "activo" : "inactivo" }}
                </v-chip>
                <v-chip
                    v-if="user.role == 2"
                    class="ma-2 white--text"
                    :color="item.active ? 'green' : 'red'"
                >
                    {{ item.active ? "activo" : "inactivo" }}
                </v-chip>
            </template>
            <template v-slot:item.action="{ item }">
                <router-link
                    v-if="user.role == 1 || user.role == 2"
                    :to="{ path: `/guardar-seguimiento-campana?id=${item.id}` }"
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
                <router-link
                    v-if="user.role == 1 || user.role == 2"
                    :to="{ path: `/lista-fases-campana?id=${item.id}` }"
                    class="action-buttons"
                >
                    <v-icon
                        small
                        class="mr-2"
                        color="#1d2735"
                        style="font-size: 25px"
                        title="EDITAR"
                        >mdi-eye-outline</v-icon
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
                            selectedItem = null;
                        "
                        >Cancelar</v-btn
                    >
                    <v-btn color="success" large @click="deletePlan()"
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
            seguimientos: [],
            selectedItem: 0,
            dialog: false,
            headers: [
                { text: "Nombre", value: "nombre" },
                { text: "Venta Total", value: "total_venta" },
                { text: "Gasto Total", value: "gastos_totales" },
                { text: "Beneficio", value: "beneficio" },
                {
                    text: "CDV Ventas (Ventas/ALC)",
                    value: "cdv_ventas",
                    sortable: false,
                },
                {
                    text: "CDV Beneficio (Bº/ALC)",
                    value: "cdv_beneficios",
                    sortable: false,
                },
                {
                    text: "Venta x € Invertido",
                    value: "action",
                    sortable: false,
                },
                { text: "% Ventas", value: "action", sortable: false },
            ],
        };
    },
    mounted() {
        this.getAllSeguimientos();
    },
    methods: {
        deletePlan() {
            axios.get(`api/delete-seguimiento/${this.selectedItem.id}`).then(
                (res) => {
                    this.$toast.sucs("Seguimiento eliminado");
                    this.dialog = false;
                    this.getAllSeguimientos();
                },
                (err) => {
                    this.$toast.error("Error eliminando Planificacion");
                }
            );
            this.getClientes();
        },
        getAllSeguimientos() {
            axios.get("api/get-seguimientos").then(
                (res) => {
                    this.seguimientos = res.data;
                },
                (err) => {
                    this.$toast.error("Error consultando datos");
                }
            );
        },

        openModal(item) {
            this.selectedItem = item;
            this.dialog = true;
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
