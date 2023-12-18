<template>
    <v-row>
        <v-col cols="12" md="4">
            <div
                style="flex;justify-content: center; text-align: center;padding: 1rem;"
            >
                <h4>Agregar Vacaciones</h4>
                <v-date-picker multiple v-model="picker"></v-date-picker>
                <v-btn
                    class="white--text"
                    @click="saveVacations"
                    color="success"
                    >Añadir</v-btn
                >
            </div>
        </v-col>

        <v-col cols="12" md="8">
            <div class="picker-header">
                <button type="button" @click="previousWeek()">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="picker-header-arrow"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M15 19l-7-7 7-7"
                        />
                    </svg>
                </button>

                <h2>
                    {{ year }}
                </h2>
                <button type="button" @click="nextWeek()">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="picker-header-arrow"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M9 5l7 7-7 7"
                        />
                    </svg>
                </button>
            </div>
            <v-data-table :headers="headers" :items="vacaciones">
                <template v-slot:item.action="{ item }">
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
        </v-col>
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
                    <v-btn color="success" large @click="deleteVacacion()"
                        >Confirmar</v-btn
                    >
                    <v-spacer></v-spacer>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-row>
</template>
<script>
export default {
    props: ["user"],
    data() {
        return {
            headers: [
                { text: "Fecha", value: "fecha", sortable: false },
                { text: "Acciones", value: "action", sortable: false },
            ],
            vacaciones: [],
            picker: [],
            year: new Date().getFullYear(),
            dialog: false,
            selectedItem: {},
        };
    },
    created() {
        this.getVacations();
    },
    methods: {
        deleteVacacion() {
            axios.get(`api/delete-vacaciones/${this.selectedItem.id}`).then(
                (res) => {
                    this.getVacations();

                    this.$toast.sucs("Vacación eliminada");
                    this.dialog = false;
                },
                (err) => {
                    this.$toast.error("Error eliminando Vacación");
                }
            );
            this.getUsuarios();
        },
        openModal(item) {
            this.selectedItem = item;
            this.dialog = true;
        },
        previousWeek() {
            this.year--;
            this.getVacations();
        },
        nextWeek() {
            this.year++;
            this.getVacations();
        },
        getVacations() {
            this.$emit("update");
            console.log("vacaciones");
            axios
                .get(
                    `api/get-vacaciones?id_usuario=${this.user.id}&year=${this.year}`
                )
                .then((res) => {
                    this.vacaciones = res.data.data;
                    console.log(this.vacaciones);
                });
        },
        saveVacations() {
            axios
                .post(`api/save-vacaciones`, {
                    vacaciones: this.picker,
                    id_usuario: this.user.id,
                })
                .then((res) => {
                    this.getVacations();
                });
        },
    },
};
</script>
