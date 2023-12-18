<template>
    <v-card class="pa-3 ma-3">
        <v-toolbar flat color="#1d2735" dark>
            <v-icon class="white--text" style="font-size: 45px">
                mdi-bullhorn
            </v-icon>
            <pre><v-toolbar-title><h2>Listado de Leads</h2></v-toolbar-title></pre>
        </v-toolbar>
        <loader v-if="isloading"></loader>
        <v-tooltip right>
            <template v-slot:activator="{ on, attrs }">
                <v-btn
                    fab
                    :to="`/lista-formulario-facebook?id=${$route.query.page_id}`"
                    :loading="isloading"
                    :disabled="isloading"
                    color="blue"
                    class="mx-3 mt-2"
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
        <v-row>
            <v-col cols="12" md="4">
                <v-text-field
                    prepend-icon="mdi-account-search"
                    v-model="search"
                    label="busqueda"
                ></v-text-field>
            </v-col>
        </v-row>
        <v-data-table
            :headers="headers"
            :search="search"
            :items-per-page="10"
            :items="formularios"
            item-key="id"
            class="elevation-1"
        >
            <template v-slot:item.active="{ item }">
                <v-chip
                    v-if="user.role == 1 || user.role == 7"
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
                <v-icon
                    @click="FixLead(item)"
                    small
                    class="mr-2"
                    color="#1d2735"
                    style="font-size: 25px"
                    title="Arreglar"
                    >mdi-pencil-outline</v-icon
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
    </v-card>
</template>

<script>
export default {
    data() {
        return {
            search: "",
            formularios: [],
            selectedItem: 0,
            dialog: false,
            headers: [
                { text: "Nombre", value: "name" },
                { text: "Acciones", value: "action", sortable: false },
            ],
        };
    },
    mounted() {
        this.getAllLeads();
    },
    methods: {
        FixLead(item) {
            axios.post(`webhook/fix-lead`, item).then(
                (res) => {
                    this.formularios = res.data;
                },
                (err) => {
                    this.$toast.error("Error consultando datos");
                }
            );
        },
        getAllLeads() {
            axios
                .get(`webhook/get-form-leads?form_id=${this.$route.query.id}`)
                .then(
                    (res) => {
                        this.formularios = res.data.leads;
                        let headers = [
                            {
                                text: "Nombre",
                                value: "value." + res.data.form.field_name,
                            },
                            {
                                text: "Email",
                                value: "value." + res.data.form.field_email,
                            },
                            {
                                text: "Telefono",
                                value: "value." + res.data.form.field_phone,
                            },
                            {
                                text: "Acciones",
                                value: "action",
                                sortable: false,
                            },
                        ];
                        this.headers = headers;
                    },
                    (err) => {
                        this.$toast.error("Error consultando datos");
                    }
                );
        },

        openModal(item) {
            this.selectedItem = this.formularios.indexOf(item);
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
