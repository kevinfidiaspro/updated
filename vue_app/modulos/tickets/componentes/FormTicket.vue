<template>
    <v-container>
        <loader v-if="isloading"></loader>
        <v-card>
            <v-toolbar flat color="#1d2735" dark>
                <v-toolbar-title
                    >Guardar / Editar Ticket</v-toolbar-title
                >
            </v-toolbar>

            <v-row class="ma-1 pa-3">
                <v-col cols="12">
                    <v-form class="mt-3">
                        <v-row>
                            <v-col cols="12" md="4">
                                <v-autocomplete
                                    label="Estado"
                                    outlined
                                    dense
                                    v-model="ticket.id_estado_ticket"
                                    :items="estados"
                                    item-text="descripcion"
                                    item-value="id"
                                >
                                </v-autocomplete>
                            </v-col>
                            <v-col cols="12" md="4">
                                <v-autocomplete
                                    label="Departamento"
                                    outlined
                                    dense
                                    v-model="ticket.id_departamento"
                                    :items="departamentos"
                                    item-text="descripcion"
                                    item-value="id"
                                >
                                </v-autocomplete>
                            </v-col>

                            <v-col cols="12" md="4">
                                <v-menu
                                    ref="menu"
                                    v-model="menu"
                                    :close-on-content-click="false"
                                    :return-value.sync="ticket.fecha_finalizacion"
                                    transition="scale-transition"
                                    offset-y
                                    min-width="290px"
                                >
                                    <template v-slot:activator="{ on, attrs }">
                                        <v-text-field
                                            v-model="ticket.fecha_finalizacion"
                                            label="Fecha de finalización"
                                            append-icon="mdi-calendar"
                                            v-bind="attrs"
                                            v-on="on"
                                        >
                                        </v-text-field>
                                    </template>
                                    <v-date-picker
                                        color="#1d2735"
                                        first-day-of-week="1"
                                        v-model="ticket.fecha_finalizacion"
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
                                            @click="$refs.menu.save(ticket.fecha_finalizacion)"
                                            ><strong>OK</strong></v-btn
                                        >
                                    </v-date-picker>
                                </v-menu>
                            </v-col>

                            <v-col cols="12" md="4">
                                <v-autocomplete
                                    label="Cliente"
                                    outlined
                                    dense
                                    v-model="ticket.id_usuario"
                                    :items="clientes"
                                    item-text="nombre"
                                    item-value="id"
                                >
                                </v-autocomplete>
                            </v-col>

                            <v-col cols="12" md="4">
                                <v-autocomplete
                                    label="Responsable"
                                    outlined
                                    dense
                                    v-model="ticket.id_responsable"
                                    :items="responsables"
                                    item-text="nombre"
                                    item-value="id"
                                >
                                </v-autocomplete>
                            </v-col>

                            <v-col cols="12" md="4">
                                <v-autocomplete
                                    label="Urgencia"
                                    outlined
                                    dense
                                    v-model="ticket.id_urgencia"
                                    :items="urgencia"
                                    item-text="descripcion"
                                    item-value="id"
                                >
                                </v-autocomplete>
                            </v-col>

                            <v-col cols="12">
                                <v-textarea
                                    dense
                                    outlined
                                    height="100px"
                                    label="Descripcion"
                                    v-model="ticket.descripcion"
                                >
                                </v-textarea>
                            </v-col>
                        </v-row>

                        <!-- <v-row>
                            <v-col cols="12">
                                <v-btn
                                    v-if="!ticket.id"
                                    color="success"
                                    class="white--text"
                                    @click="saveTicket"
                                    >Guardar</v-btn
                                >
                                <v-btn
                                    @click="saveTicket"
                                    v-if="ticket.id"
                                    color="success"
                                    class="white--text"
                                    >Actualizar</v-btn
                                >
                                <v-btn
                                    @click="deleteTicket(ticket.id)"
                                    v-if="ticket.id"
                                    color="red"
                                    class="white--text"
                                    >Eliminar</v-btn
                                >
                            </v-col>
                        </v-row> -->
                    </v-form>
                </v-col>
            </v-row>
        </v-card>

        <v-row class="mt-3">
            <!-- Botones Navegacion -->
            <v-col cols="12">
                <v-tooltip top>
                    <template v-slot:activator="{ on, attrs }">
                        <v-btn
                            fab
                            @click="volver"
                            :loading="isloading"
                            :disabled="isloading"
                            color="blue"
                            class="mx-2"
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
                <v-tooltip top>
                    <template v-slot:activator="{ on, attrs }">
                        <v-btn
                            fab
                            @click="saveTicket"
                            :loading="isloading"
                            :disabled="isloading"
                            color="success"
                            class="mx-2"
                            v-bind="attrs"
                            v-on="on"
                        >
                            <v-icon class="white--text">mdi-content-save-all</v-icon>
                        </v-btn>
                    </template>
                    <span v-if="ticket.id">Actualizar</span>
                    <span v-else>Guardar</span>
                </v-tooltip>
                <v-tooltip v-if="ticket.id" top>
                    <template v-slot:activator="{ on, attrs }">
                        <v-btn
                            fab
                            @click="deleteTicket(ticket.id)"
                            :loading="isloading"
                            :disabled="isloading"
                            color="red"
                            class="mx-2"
                            v-bind="attrs"
                            v-on="on"
                        >
                            <v-icon class="white--text">mdi-trash-can</v-icon>
                        </v-btn>
                    </template>
                    <span>Eliminar</span>
                </v-tooltip>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
export default {
    data() {
        return {
            estados: [],
            departamentos: [],
            responsables: [],
            clientes: [],
            urgencia: [],
            ticket: {},
            menu: false,
        };
    },
    watch: {},
    created() {
        this.getEstados();
        this.getDepartamentos();
        this.getResponsables();
        this.getAllClientes();
        this.getUrgencia();
        if (this.$route.query.id) {
            this.getTicket(this.$route.query.id);
        }
    },
    methods: {
        getEstados() {
            axios.get("api/get-estado-tickets").then((res) => {
                this.estados = res.data.success;
            });
        },
        getDepartamentos() {
            axios.get("api/get-departamentos").then((res) => {
                this.departamentos = res.data.success;
            });
        },
        getUrgencia() {
            axios.get("api/get-urgencia").then((res) => {
                this.urgencia = res.data.success;
            });
        },

        getResponsables() {
            axios.post(`api/get-usuarios-empleados-all`).then(
                (res) => {
                    this.responsables = res.data.users;
                },
                (err) => {
                    this.$toast.error("Error consultando Usuario");
                }
            );
        },

        getAllClientes() {
            axios.get(`api/get-all-clientes`).then(
                (res) => {
                    this.clientes = res.data.users;
                    // for (let i = 0; i < this.usuarios.length; i++) {
                    //     const element = this.usuarios[i];
                    //     element.created_at = new Date(
                    //         element.created_at
                    //     ).toLocaleDateString();
                    // }
                },
                (err) => {
                    this.$toast.error("Error consultando Usuario");
                }
            );
        },

        getTicket(id) {
            const self = this;
            axios.get(`api/get-ticket/${id}`).then(function (response) {
                self.ticket = response.data.success;
            });
        },
        saveTicket() {
            const self = this;
            let ruta = ''; 
            if (self.$route.query.id) {
                ruta = 'update-ticket';
            }else{
                ruta = 'create-ticket';
            }

            axios
                .post(`api/${ruta}`, this.ticket)
                .then(function (response) {
                    self.$router.push("tickets");
                });
        },
        deleteTicket(id) {
            const self = this;
            axios
                .get(`api/delete-ticket/${id}`)
                .then(function (response) {
                    self.$router.push("tickets");
                });
        },
        volver() {
            this.$router.push(`/tickets`);
        },
    },
    computed: {
        isloading() {
            return this.$store.getters.getloading;
        },
    },
};
</script>
