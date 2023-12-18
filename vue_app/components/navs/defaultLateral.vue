<template>
    <v-list dense nav>
        <v-list-item :to="{ path: '/' }">
            <v-list-item-icon>
                <v-icon color="black">mdi-home</v-icon>
            </v-list-item-icon>

            <v-list-item-content>
                <v-list-item-title class="black--text"
                    >Inicio</v-list-item-title
                >
            </v-list-item-content>
        </v-list-item>

        <template v-if="$route.path !== '/login'">
            <v-list-item
                v-for="item in items"
                :key="item.path"
                :to="{ path: item.path }"
            >
                <v-list-item-icon>
                    <v-icon color="black">{{ item.icon }}</v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                    <v-list-item-title
                        class="black--text"
                        v-html="item.text"
                    ></v-list-item-title>
                </v-list-item-content>
            </v-list-item>
        </template>
    </v-list>
</template>

<script>
export default {
    data() {
        return {
            rol: 0,
            items: [
                {
                    path: "/lista-usuario",
                    icon: "mdi-account",
                    text: "Usuarios",
                    auth: false,
                    roles: [1],
                },
                {
                    path: "/lista-productos",
                    icon: "mdi-shape-plus",
                    text: "Productos",
                    auth: false,
                    roles: null,
                },
                {
                    path: "/lista-agentes",
                    icon: "mdi-account-tie",
                    text: "Agentes",
                    auth: false,
                    roles: [1],
                },
                {
                    path: "/lista-clientes",
                    icon: "mdi-home-account",
                    text: "Clientes",
                    auth: false,
                    roles: null,
                },
                {
                    path: "/lista-proyectos-cliente",
                    icon: "mdi-file-cabinet",
                    text: "Proyectos Client",
                    auth: false,
                    roles: null,
                },
                {
                    path: "/lista-presupuesto",
                    icon: "mdi-cash",
                    text: "Presupuesto",
                    auth: false,
                    roles: null,
                },
                {
                    path: "/lista-contrato",
                    icon: "mdi-cash",
                    text: "Contrato",
                    auth: false,
                    roles: null,
                },
                {
                    path: "/lista-seguimiento",
                    icon: "mdi-clock",
                    text: "Seguimiento",
                    auth: false,
                    roles: null,
                },
                {
                    path: "/lista-pedidos",
                    icon: "mdi-cash",
                    text: "Pedidos",
                    auth: false,
                    roles: [1],
                },
                {
                    path: "/lista-facturas",
                    icon: "mdi-cash",
                    text: "Facturas",
                    auth: false,
                    roles: [1],
                },
            ],
        };
    },
    created() {
        this.rol = localStorage.getItem("role");
        console.log(this.rol);
        let items = [];
        this.items.forEach((element) => {
            let correcto = false;
            if (element.roles != null) {
                for (let i = 0; i < element.roles.length; i++) {
                    if (element.roles[i] == this.rol) {
                        items.push(element);
                        break;
                    }
                }
            } else {
                items.push(element);
            }
        });
        this.items = items;
    },
};
</script>
<style media="screen">
.v-list-item--active {
    background-color: #59fee2 !important;
}
</style>
