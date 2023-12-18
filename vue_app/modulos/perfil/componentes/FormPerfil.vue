<template>

    <v-container>

        <v-custom-title text="Actualizar Contraseña"></v-custom-title>

        <loader v-if="isloading"></loader>

        <v-form>

            <v-row>
                <v-col cols="12" md="3">
                    <v-text-field filled :error-messages="errors.errors.old_password ? errors.errors.old_password[0] : null" :append-icon="show1 ? 'mdi-eye' : 'mdi-eye-off'" :type="show1 ? 'text' : 'password'" @click:append="show1 = !show1"
                      v-model="user.old_password" label="Contraseña antigua" required></v-text-field>
                </v-col>
            </v-row>

            <v-row>
                <v-col cols="12" md="3">
                    <v-text-field filled :error-messages="errors.errors.password ? errors.errors.password[0] : null" :append-icon="show2 ? 'mdi-eye' : 'mdi-eye-off'" :type="show2 ? 'text' : 'password'" @click:append="show2 = !show2"
                      v-model="user.password" label="Nueva Contraeña" required></v-text-field>
                </v-col>
            </v-row>

            <v-row>
                <v-col cols="12">
                    <v-btn rounded depressed @click="changePassword" :disabled="isloading" color="success" class="white--text">Guardar</v-btn>
                </v-col>
            </v-row>

        </v-form>

    </v-container>

</template>

<script>
    export default {
        data() {
            return {
                show1: false,
                show2: false,
                user: {
                    old_password: '',
                    password: '',
                }
            }
        },
        methods: {
            changePassword() {
                axios.post('api/change-password', this.user).then(res => {
                    this.$toast.sucs(res.data)
                    this.setDefaultData()
                }, res => {
                    this.$toast.error('Algo ha salido mal')
                })
            },
            setDefaultData() {
                this.show1 = false
                this.show2 = false;
                this.user = {
                    old_password: '',
                    password: '',
                }
            }
        },
        computed: {

            isloading() {
                return this.$store.getters.getloading
            },

            errors() {
                return this.$store.getters.geterrors
            }
        }
    }
</script>