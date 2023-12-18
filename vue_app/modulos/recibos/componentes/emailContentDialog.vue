<template>
    <v-dialog @click:outside="closeDialog" v-model="email_dialog" width="400">

        <v-card>
            <v-card-title class="headline primary white--text" dark primary-title>
                Enviar email
            </v-card-title>

            <v-card-text class="px-3 py-3">

                <loader v-if="isloading"></loader>

                <v-row>
                    <v-col cols="12" md="10">
                        <v-text-field append-icon="mdi-pencil" v-model="sendto.email" hide-details :filled="email_disabled" label="Email" :readonly="email_disabled" @click:append="email_disabled = !email_disabled"></v-text-field>
                    </v-col>
                </v-row>

            </v-card-text>

            <v-divider></v-divider>

            <v-card-actions>
                <v-btn color="blue" class="white--text" @click="sendEmail">aceptar</v-btn>

                <v-spacer></v-spacer>

                <v-btn color="red" text @click="closeDialog">cerrar</v-btn>

            </v-card-actions>

        </v-card>

    </v-dialog>

</template>

<script>
    export default {
        props: ['email_dialog', 'email', 'url_files', 'isloading', 'tipo'],

        data() {
            return {
                email_disabled: true,
                sendto: {
                    email: null,
                    archivo: {
                        label: null,
                        archivo: null,
                    }
                }
            }
        },

        watch: {
            'email'(n) {
                this.sendto.email = n
            }
        },

        methods: {
            sendEmail() {

                this.sendto.archivo = {
                    label: this.tipo,
                    archivo: this.name_files[this.tipo]
                }

                axios.post(`api/send-email`, this.sendto).then(res => {
                    this.$toast.sucs('Email enviado con exito')

                    this.email_disabled = true

                    this.sendto.archivo = {
                        label: null,
                        archivo: null
                    }

                    this.closeDialog()
                }, res => {
                    this.$toast.error('Fallo envio de email')
                })

            },

            closeDialog() {
                this.$emit('close_dialog')
            }
        },

        computed: {
            name_files() {
                let items = []
                items['nota'] = this.url_files.nota_url
                items['presupuesto'] = this.url_files.presupuesto_url
                items['factura'] = this.url_files.factura_url
                return items
            }
        }
    }
</script>