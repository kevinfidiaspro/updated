<template>
    
    <v-app id="inspire">
        <v-main>
            <v-container class="fill-height" fluid>

                <v-row align="center" justify="center">
                    <v-col cols="12" md="4" >

                        <img width="100%;" src="https://fidiaspro.com/wp-content/uploads/2022/03/Logo-Fidias-Pro-sin-fondo-Negro-e1646664499686.png" alt="">

                        <v-card style="border-radius:0;" flat class="elevation-2">
                            <v-card-text>
                                <v-form>
                                    <v-text-field prepend-icon="mdi-account" v-model="user.email" label="Email" required></v-text-field>
                                    <v-text-field prepend-icon="mdi-key" v-model="user.password" label="Contraseña" required type="password"></v-text-field>
                                </v-form>
                            </v-card-text>
                            <v-card-actions>
                                <router-link to="/forgot">
                                    Olvide Contraseña
                                    </router-link>
                                <v-spacer></v-spacer>
                                
                                <v-btn depressed :disabled="isloading" @click="Login" color="blue" class="white--text" :loading="isloading">Login</v-btn>
                            </v-card-actions>
                            <v-spacer></v-spacer>
                          
                                   
                        </v-card>
                    </v-col>
                </v-row>
            </v-container>
        </v-main>
    </v-app>


</template>

<script>
    import auth from '../../auth/auth.js'
    export default {
        data() {
            return {
                user: {
                    email: '',
                    password: ''
                }
            }
        },
        mounted() {
            
            if(localStorage != undefined){
                let valid = localStorage.user_id
                if(valid){
                    window.location.href = '/'
                }
            }
               
            
        },
        methods: {
            Login() {
                auth.signin(this.user)
            }
        },
        computed: {
            isloading: function() {
                return this.$store.getters.getloading
            },
        }
    }
</script>

<style>
    .cover-img {
        height: 100%
    }

    .back {
        background-color: #212121;
    }
</style>