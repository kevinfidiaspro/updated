<template>
    <v-container>
        <loader v-if="isloading"></loader>
        <v-card shaped class="mx-4 my-4 pa-4">
            <v-row>
                <v-col cols="12">
                    <v-toolbar
                        flat
                        color="#1d2735"
                        dark
                        style="border-radius: 5px"
                    >
                        <v-icon class="white--text" style="font-size: 45px"
                            >mdi-account-supervisor-circle</v-icon
                        >
                        <pre><v-toolbar-title><h2>Configuracion Facebook</h2></v-toolbar-title></pre>
                    </v-toolbar>
                    <v-form class="mt-3">
                        <v-row>
                            <v-col cols="12">
                                <v-text-field
                                    v-model="app_id"
                                    label="AppId"
                                ></v-text-field>
                            </v-col>
                            <v-col cols="12">
                                <v-btn
                                    color="success"
                                    class="white--text"
                                    @click="Login()"
                                    >ingresar</v-btn
                                >
                            </v-col>
                            <v-col cols="12">
                                <v-btn
                                    color="success"
                                    class="white--text"
                                    @click="FacebookAddApp()"
                                    >Agregar App</v-btn
                                >
                            </v-col>
                            <v-col cols="6">
                                <v-text-field
                                    v-model="user_id"
                                    label="UserId"
                                ></v-text-field>
                            </v-col>
                            <v-col cols="6">
                                <v-text-field
                                    v-model="short_user_token"
                                    label="Token de Corta Duracion"
                                ></v-text-field>
                            </v-col>
                            <v-col cols="6">
                                <v-text-field
                                    v-model="long_lived_token"
                                    label="Token de larga duracion"
                                ></v-text-field>
                            </v-col>
                            <v-col cols="6">
                                <v-text-field
                                    v-model="page_token"
                                    label="Token de Pagina"
                                ></v-text-field>
                            </v-col>
                        </v-row>
                    </v-form>
                </v-col>
            </v-row>
        </v-card>
    </v-container>
</template>
<script>
export default {
    data() {
        return {
            short_user_token: null,
            long_lived_token: null,
            page_token: null,
            user_id: null,
            app_id: "1157939191471232",
            app_secret: "a9dbc967148107b1e1c162cb3aa2380a",
            page_id: "104140814678268",
        };
    },
    created() {
        this.FacebookInit();
    },
    watch: {
        short_user_token() {
            this.getLongLivedToken();
        },
        long_lived_token() {
            this.getPageAccessToken();
        },
    },
    methods: {
        FacebookAddApp() {
            let me = this;
            var instance = axios.create();
            delete instance.defaults.headers.common["Authorization"];
            delete instance.defaults.headers.common["Authorization"];
            delete instance.defaults.headers.common["X-CSRF-TOKEN"];

            instance.defaults.withCredentials = false;

            axios
                .post("api/get-add-app", {
                    page_id: me.page_id,
                    page_token: me.page_token,
                })
                .then((res) => {
                    alert("ha sido exitoso");
                    axios.post("/webhook/set-token", {
                        fb_token: me.page_token,
                    });
                });
        },
        FacebookInit() {
            console.log("initializing");

            FB.init({
                appId: this.app_id,
                autoLogAppEvents: true,
                xfbml: true,
                version: "v14.0",
            });
        },
        Login() {
            let me = this;
            // this.short_user_token = 'EAAQdI7ot3IABAF4ZCrM1URqCtuDvyF7rHDLRvMpLy3jFJWaZCWu89ZA6ZBtdIcmZAMd9A6OUhdEckuRqRGU6TZAJGuVwQxjZAMpGhPzTQz8SWoxN1UQkG2smxkDLTvRPAcENESqXsBgaK1QMGvXQG8vXRQlxxllXXrm0T8P2tcvuIOgepBbnxfrwDNaWANt6ZCZBCqxoslZA8wdezf7DTradEe0aEleoz1VBsZD'
            //return;
            FB.login(function (response2) {
                if (response2.authResponse) {
                    console.log("Welcome!  Fetching your information.... ");
                    FB.api("/me", function (response1) {
                        me.user_id = response1.id;
                        FB.getLoginStatus(function (response) {
                            if (response.status === "connected") {
                                me.short_user_token =
                                    response.authResponse.accessToken;
                            }
                        });
                    });
                } else {
                    console.log(
                        "User cancelled login or did not fully authorize."
                    );
                }
            });
        },
        getLongLivedToken() {
            let me = this;
            var instance = axios.create();
            delete instance.defaults.headers.common["Authorization"];
            delete instance.defaults.headers.common["X-CSRF-TOKEN"];

            instance.defaults.withCredentials = false;

            axios
                .post("api/get-long-lived-token", {
                    app_id: me.app_id,
                    app_secret: me.app_secret,
                    short_user_token: me.short_user_token,
                })
                .then(
                    (res) => {
                        me.long_lived_token = res.data.access_token;
                    },
                    (res) => {}
                );
        },
        getPageAccessToken() {
            let me = this;
            var instance = axios.create();
            delete instance.defaults.headers.common["Authorization"];
            delete instance.defaults.headers.common["X-CSRF-TOKEN"];
            instance.defaults.withCredentials = false;

            axios
                .post(`api/get-page-access-token`, {
                    page_id: me.page_id,
                    long_lived_token: me.long_lived_token,
                })
                .then(
                    (res) => {
                        console.log(res.data);
                        this.page_token = res.data.access_token;
                    },
                    (res) => {}
                );
        },
        VerAppsInstalads() {
            let me = this;
            FB.api(`/${me.page_id}/subscribed_apps`, function (response) {
                if (response && !response.error) {
                    /* handle the result */
                }
            });
        },
    },
};
</script>
