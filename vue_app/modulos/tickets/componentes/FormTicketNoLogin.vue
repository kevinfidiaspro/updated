<template>
    <v-app id="inspire">
        <v-main>
            <v-container class="fill-height" fluid>
                <v-row align="center" justify="center">
                    <v-col cols="12" md="4">
                        <img width="100%;" src="https://fidiaspro.com/wp-content/uploads/2022/03/Logo-Fidias-Pro-sin-fondo-Negro-e1646664499686.png" alt="">

                        <div class="elementor-heading-title">Crear tickets</div>
                    </v-col>
                    <v-col cols="12" md="11" >
                        <v-card class="mx-4 my-4 pa-4" elevation="5">
                            <v-row style="padding: 50px">
                                <v-col cols="12">
                                    <v-form class="mt-3">
                                        <v-row>
                                            <v-col cols="12">
                                                <v-autocomplete
                                                    label="Departamento"
                                                    v-model="ticket.id_departamento"
                                                    :items="departamentos"
                                                    item-text="descripcion"
                                                    item-value="id"
                                                    outlined
                                                    :rules="[rules.required]"
                                                >
                                                </v-autocomplete>
                                            </v-col>

                                            <v-col cols="12">
                                                <v-autocomplete
                                                    label="Urgencia"
                                                    v-model="ticket.id_urgencia"
                                                    :items="urgencia"
                                                    item-text="descripcion"
                                                    item-value="id"
                                                    outlined
                                                    :rules="[rules.required]"
                                                >
                                                </v-autocomplete>
                                            </v-col>

                                            <v-col cols="12">
                                                <v-text-field
                                                    label="Teléfono o email"
                                                    v-model="tlfOrEmail"
                                                    counter
                                                    maxlength="30"
                                                    minlength="30"
                                                    :rules="[rules.required, rules.counter]"
                                                    required
                                                    @change="getUsuario"
                                                    outlined
                                                ></v-text-field>
                                            </v-col>

                                            <v-col cols="12">
                                                <v-text-field
                                                    label="Cliente"
                                                    v-model="ticket.nombre_usuario"
                                                    readonly
                                                    required
                                                    outlined
                                                ></v-text-field>
                                            </v-col>

                                            <v-col cols="12">
                                                <v-textarea
                                                    label="Descripcion"
                                                    v-model="ticket.descripcion"
                                                    outlined
                                                >
                                                </v-textarea>
                                            </v-col>
                                        </v-row>

                                        <v-row>
                                            <v-col cols="12">
                                                <v-btn
                                                    class="save-btn"
                                                    @click="saveTicket"
                                                    >Crear</v-btn
                                                >
                                                <!-- <v-btn
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
                                                > -->
                                            </v-col>
                                        </v-row>
                                    </v-form>
                                </v-col>
                            </v-row>
                        </v-card>
                    </v-col>
                </v-row>
            </v-container>
    </v-main>
    </v-app>
</template>

<script>
export default {
    data() {
        return {
            urgencia: [],
            departamentos: [],
            ticket: {
                id_urgencia: null, 
                id_departamento: null, 
                id_usuario: null,
                nombre_usuario: '',
                descripcion: ''
            },
            tlfOrEmail: '',
            rules: {
                required: value => !!value || 'Este campo es requerido.',
                counter: value => value.length <= 30 || 'Max 30 characters',
            },
        };
    },
    created() {
        this.getUrgencia();
        this.getDepartamentos();
    },
    methods: {
        getUrgencia() {
            axios.get("api/get-urgencia").then((res) => {
                this.urgencia = res.data.success;
            });
        },
        getDepartamentos() {
            axios.get("api/get-departamentos").then((res) => {
                this.departamentos = res.data.success;
            });
        },
        getUsuario() {
            const self = this;
            axios
                .get(`api/get-usuario-by-attribute/${self.tlfOrEmail}`)
                .then(function (response) {
                    let usuario = response.data.success
                    self.ticket.nombre_usuario = usuario.nombre;
                    self.ticket.id_usuario = usuario.id;
                });
        },
        saveTicket() {
            const self = this;
            axios
                .post("api/create-ticket", this.ticket)
                .then(function (response) {
                    self.$toast.sucs(response.data.success);
                    self.ticket = {
                        id_urgencia: null, 
                        id_departamento: null, 
                        id_usuario: null,
                        nombre_usuario: '',
                        descripcion: ''
                    }
                    self.tlfOrEmail = ''
                })
                .error(function (error) {
                    self.$toast.error(error.data.error);
                });
        }
    }
};
</script>
<style scoped>
    .elementor-heading-title{
        color: #000;
        font-size: 3vw;
        font-weight: 700;
        text-align: center;
        margin: 20px 0;
    }

    @media (max-width: 768px) {
        .elementor-heading-title{
            font-size: 9vw;
        }
    }

    @media (max-width: 1024px) {
        .elementor-heading-title{
            font-size: 6.4vw;
        }
    }

    .save-btn{
        background-color: rgb(241, 234, 226) !important;
        color: #212020 !important;
        font-weight: 600;
    }

    .save-btn:hover{
        background-color: #212020 !important; 
        color: rgb(241, 234, 226) !important;
    }
</style>
    