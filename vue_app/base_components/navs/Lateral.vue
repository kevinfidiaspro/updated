<template>
    <v-navigation-drawer
        color="white"
        @input="getthe"
        v-model="local_drawer"
        :mini-variant="mini"
        app
        class="elevation-4"
        style="height: 100svh !important"
    >
        <div class="background-nav"></div>

        <v-list-item @click.stop="mini = !mini" class="avatar-b-color">
            <v-list-item-content>
                <v-list-item-title class="text-center">
                    <img
                        style="width: 75%; margin: auto"
                        :src="'https://fidiaspro.com/wp-content/uploads/2022/03/Logo-Fidias-Pro-sin-fondo-Negro-e1646664499686.png'"
                    />
                </v-list-item-title>
            </v-list-item-content>
        </v-list-item>

        <v-divider></v-divider>

        <default-lateral></default-lateral>
        <div style="padding: 1rem" v-if="main_rol == 1">
            <v-autocomplete
                v-model="id_usuario"
                :items="usuarios"
                item-text="nombre"
                item-value="id"
                label="Usuario"
            >
            </v-autocomplete>
        </div>
    </v-navigation-drawer>
</template>

<script>
import defaultLateral from "./defaultLateral.vue";
export default {
    props: ["drawer"],
    components: {
        defaultLateral,
    },
    created() {
        this.getUsuarios();
    },
    methods: {
        getthe(e) {
            this.$emit("emitInnputDrawer", e);
        },
        getUsuarios() {
            axios.post(`api/get-usuarios-empleados`).then(
                (res) => {
                    this.usuarios = res.data.users.data;
                },
                (err) => {
                    this.$toast.error("Error consultando Fichajes");
                }
            );
        },
    },
    data() {
        return {
            usuarios: [],
            id_usuario: parseInt(localStorage.getItem("user_id")),
            main_rol: parseInt(localStorage.getItem("real_role")),
            lateral: "defaultLateral",
            mini: false,
            local_drawer: true,
        };
    },
    watch: {
        id_usuario(val) {
            const usuario = this.usuarios.find((element) => element.id == val);
            localStorage.setItem("role", usuario.role);
            localStorage.setItem("user_id", usuario.id);
            localStorage.setItem("rol_tfg", usuario.rol_tfg);
            window.location.reload();
        },
        drawer: {
            immediate: true,
            handler: function (n) {
                this.local_drawer = n;
            },
        },
        computed: {
            auth() {
                //return this.$store.getters.getauth
                return "a";
            },
            user() {
                return "u";
                //  return this.$store.getters.getuser
            },
        },
    },
};
</script>

<style media="screen">
.v-navigation-drawer .v-list .v-list-item > .v-list__tile .v-list__tile__title {
    font-size: 14px !important;
    font-weight: 100;
    padding: 0;
}

.v-list--dense .v-list-item .v-list-item__subtitle,
.v-list--dense .v-list-item .v-list-item__title,
.v-list-item--dense .v-list-item__subtitle,
.v-list-item--dense .v-list-item__title {
    font-size: 17px !important;
    font-weight: 300;
    text-transform: capitalize;
}

.v-application--is-ltr .v-list-item__action:first-child,
.v-application--is-ltr .v-list-item__icon:first-child {
    margin-right: 15px !important;
}

.v-list--dense .v-list-item .v-list-item__icon,
.v-list-item--dense .v-list-item__icon {
    opacity: 0.9;
    color: #fff;
}

.v-list--nav .v-list-item {
    padding: 8px 15px !important;
}

.v-list-item--active:before,
.v-list-item--active:hover:before,
.v-list-item:focus:before {
    /*opacity: 1*/
}

.theme--dark.v-list-item--active:before,
.theme--dark.v-list-item--active:hover:before,
.theme--dark.v-list-item:focus:before {
    /*opacity: 0;*/
}

.avatar-b-color {
    background-color: #fff !important;
}

.v-list-item--active {
    background-color: rgb(91, 198, 236) !important;
    color: white !important;
}

.theme--dark.v-list-item--active:hover::before,
.theme--dark.v-list-item--active::before {
    /*opacity: 0 !important;*/
}
</style>
