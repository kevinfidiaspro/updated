<template>
    <v-app>
        <top
            v-if="
                $route.path !== '/login' &&
                $route.path !== '/social-cliente' &&
                $route.path !== '/reset-password' &&
                $route.path !== '/forgot' &&
                $route.path !== '/crear-ticket'
            "
            v-on:toggleDrawer="toggleDrawer"
        ></top>
        <lateral
            v-if="
                $route.path !== '/login' &&
                $route.path !== '/social-cliente' &&
                $route.path !== '/reset-password' &&
                $route.path !== '/forgot' &&
                $route.path !== '/crear-ticket'
            "
            :drawer="drawer"
            v-on:emitInnputDrawer="getInnputDrawer"
        ></lateral>

        <v-main color="purple darken-3">
            <v-container
                fluid
                grid-list-xs
                :class="{
                    'pa-0':
                        $route.path == '/login' ||
                        $route.path !== '/social-cliente' ||
                        $route.path !== '/reset-password' ||
                        ($route.path !== '/forgot' &&
                            $route.path !== '/crear-ticket'),
                }"
            >
                <router-view :key="$route.path"></router-view>
            </v-container>
        </v-main>
    </v-app>
</template>
<style>
.pointer {
    cursor: pointer;
}
</style>
<script>
import Top from "../navs/Top.vue";
import Lateral from "../navs/Lateral.vue";
import auth from "../../auth/auth";
export default {
    components: {
        Top,
        Lateral,
    },
    watch: {
        drawer: {
            immediate: true,
            handler: function (n) {},
        },
    },
    data() {
        return {
            drawer: true,
        };
    },
    created() {
        this.getUser();
    },
    methods: {
        getUser() {
            axios.get("/api/current-user").then((res) => {
                const data = res.data;
                const user_id = localStorage.getItem("user_id").toString();
                //console.log(user_id);
                //console.log(data.role == 1);
                //console.log(data.id.toString());
                //console.log(user_id);
                //console.log(data.id.toString() != user_id);
                //console.log(user_id != null);
                if (
                    data.role == 1 &&
                    data.id.toString() != user_id &&
                    user_id != null
                )
                    return;
                localStorage.setItem("user_name", data.nombre);
                localStorage.setItem("user_email", data.email);
                localStorage.setItem("role", data.role);
                localStorage.setItem("rol_tfg", data.role_tfg);
                localStorage.setItem("user_id", data.id);
                localStorage.setItem("real_role", data.role);
                localStorage.setItem("real_user_id", data.id);
            });
        },
        isActive() {
            return false; //this.$route.path != '/login'
        },
        toggleDrawer() {
            this.drawer = !this.drawer;
        },
        getInnputDrawer(e) {
            this.drawer = e;
        },
    },
};
</script>

<style media="screen">
.my-container {
    padding: 1rem;
    background-color: #ffffff;
    border-radius: 7px;
}

.v-content__wrap {
    background-color: #f3f6fd !important;
}

a {
    text-decoration: none;
}

.theme--light.v-application {
    background: rgb(225, 225, 225) !important;
}

.theme--light.v-app-bar.v-toolbar.v-sheet {
    background-color: #ffffff !important;
}

.theme--light.v-data-table
    .v-data-table-header
    th.sortable
    .v-data-table-header__icon {
    display: none;
}
</style>
