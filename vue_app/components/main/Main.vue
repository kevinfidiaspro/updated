<template>
    <v-app style="background-color: #c1c1c1">
        <top
            v-if="
                $route.path !== '/login' &&
                $route.path !== '/pedir-cita' &&
                $route.path !== '/social-cliente' &&
                $route.path !== '/forgot' &&
                $route.path !== '/reset-password'
            "
            v-on:toggleDrawer="toggleDrawer"
        ></top>
        <lateral
            v-if="
                $route.path !== '/login' &&
                $route.path !== '/pedir-cita' &&
                $route.path !== '/social-cliente' &&
                $route.path !== '/forgot' &&
                $route.path !== '/reset-password'
            "
            :drawer="drawer"
            v-on:emitInnputDrawer="getInnputDrawer"
        ></lateral>

        <v-main color="purple darken-3">
            <v-container
                fluid
                grid-list-xs
                :class="
                    $route.path == '/login' ||
                    $route.path == '/pedir-cita' ||
                    $route.path !== '/social-cliente' ||
                    $route.path == '/reset-password' ||
                    $route.path == '/forgot'
                        ? 'pa-0'
                        : ''
                "
            >
                <router-view></router-view>
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
    created() {
        this.getUser();
    },
    data() {
        return {
            drawer: true,
        };
    },
    methods: {
        getUser() {
            axios.get("/current-user").then((res) => {
                const data = res.data;
                const user_id = localStorage.getItem("user_id");
                if (
                    data.user.role == 1 &&
                    data.user.id != user_id &&
                    user_id != null
                )
                    return;
                //localStorage.setItem("id_token", data.token);
                localStorage.setItem("user_name", data.name);
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
<style>
.row_red {
    background-color: rgb(250, 207, 207);
}
</style>
<style media="screen">
.v-application {
    font-family: "Roboto", sans-serif !important;
}
.title {
    font-family: "Roboto", sans-serif !important;
}

.my-container {
    padding: 1rem;
}

.v-content__wrap {
    background-color: #efeeee;
}

a {
    text-decoration: none;
}

/*.v-toolbar__title {
        font-weight: 300;
        color: #555;
        font-size: 18px;
        margin-bottom: 6px;
    }*/
</style>
