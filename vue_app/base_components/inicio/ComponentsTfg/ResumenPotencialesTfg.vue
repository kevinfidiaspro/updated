<template>
    <div style="margin-top: 2rem">
        <v-toolbar flat color="#1d2735" dark>
            <v-icon class="white--text" style="font-size: 45px"
                >mdi-account-supervisor-circle</v-icon
            >
            <pre><v-toolbar-title><h2>Resumen Potenciales</h2></v-toolbar-title></pre>
        </v-toolbar>

        <v-data-table
            hide-default-footer
            dense
            :headers="headers"
            :items="gastos"
            :search="filtros_prueba.search"
            :items-per-page="-1"
            item-key="id"
            class="elevation-1"
            :sort-by="['nombre']"
            :sort-desc="[false]"
        >
            <template v-slot:item.action="{ item }">
                <v-icon
                    @click="
                        () => {
                            gasto = item;
                            form_dialog = true;
                        }
                    "
                    small
                    class="mr-2"
                    color="#1d2735"
                    style="font-size: 25px"
                    title="EDITAR"
                    >mdi-pencil-outline</v-icon
                >

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
    </div>
</template>
<script>
export default {
    data() {
        return {
            dia: new Date().toISOString().substr(0, 10),
            search: "",
            filtros_prueba: {
                search: "",
                tipos: {},
            },
            headers: [],
            form_dialog: false,
            gasto: {},
            gastos: [],
            selectedItem: 0,
            dialog: false,
            inicio: null,
            fin: null,
            pagina: null,
            tipos: [],
        };
    },
    created() {
        this.getTipos();
        const currentDate = new Date();

        // Get the year and month of the current date
        const year = currentDate.getFullYear();
        const month = currentDate.getMonth() + 1;

        // Create a new Date object for the first day of the month
        const first = new Date(year, month, 1);

        // Create a new Date object for the last day of the month
        const lastDayOfMonth = new Date(year, month, 0, 23, 59, 59);
        //console.log(`${first.getFullYear()}-${first.getMonth()}-1`);
        // Format the first and last day as strings (e.g., "YYYY-MM-DD")
        this.inicio = `${year}-${first
            .getMonth()
            .toString()
            .padStart(2, "0")}-01`;
        this.fin = lastDayOfMonth.toISOString().split("T")[0];
        this.getPotenciales();
    },
    watch: {
        inicio: {
            handler(val) {
                this.getPotenciales();
            },
        },
        fin: {
            handler(val) {
                this.getPotenciales();
            },
        },
        pagina: {
            handler(val) {
                this.getPotenciales();
            },
        },
    },
    methods: {
        getTipos() {
            const self = this;
            axios.get(`api/get-empresas-tfg`).then(function (response) {
                self.tipos = response.data;
            });
        },
        getFormattedDate(date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, "0");
            const day = String(date.getDate()).padStart(2, "0");
            return `${year}-${month}-${day}`;
        },
        getPotenciales() {
            const today = new Date();
            const sevenDaysPrior = new Date(today);
            sevenDaysPrior.setDate(today.getDate() - 2);

            const todayFormatted = this.getFormattedDate(today);
            const sevenDaysPriorFormatted =
                this.getFormattedDate(sevenDaysPrior);
            axios
                .get(
                    `api/get-resumen-potenciales-tfg?inicio=${sevenDaysPriorFormatted}&fin=${todayFormatted}${
                        this.pagina == null ? "" : `&web=${this.pagina}`
                    }`
                )
                .then(
                    (res) => {
                        this.gastos = res.data.data.reverse();
                        this.headers = res.data.headers;
                    },
                    (err) => {
                        this.$toast.error("Error consultando Gastos");
                    }
                );
        },
        openModal(item) {
            this.selectedItem = item;
            this.dialog = true;
        },
        deleteUser() {
            console.log(this.selectedItem);
            axios
                .post("api/delete-tfg-gasto", {
                    id: this.selectedItem.id,
                })
                .then(
                    (res) => {
                        this.$toast.sucs("Gasto eliminado");
                        this.dialog = false;
                        this.getPotenciales();
                    },
                    (err) => {
                        this.$toast.error("Error eliminando Gasto");
                    }
                );
        },
    },
    computed: {
        isLoading: function () {
            return this.$store.getters.getloading;
        },
    },
};
</script>
