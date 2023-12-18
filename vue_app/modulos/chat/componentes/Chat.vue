<template>
    <v-container>
        <v-card>
            <v-toolbar flat color="#1d2735" dark>
                <v-toolbar-title>Contacto Soporte {{user.role}}</v-toolbar-title>
            </v-toolbar>
            <v-card-text ref="chatcontainer" style="max-height:60vh; overflow-y: auto;overflow-x: hidden;">

                <template v-for="mensaje in chat.mensajes">
                    <default-mensaje v-if="isOwner(mensaje)" :mensaje="mensaje.mensaje"></default-mensaje>
                    <own-mensaje v-else :mensaje="mensaje.mensaje"></own-mensaje>
                </template>

            </v-card-text>

            <v-card-actions>
                <v-row dense align="center" justify="center">
                    <v-col cols="12" md="11">
                        <v-textarea v-model="mensaje.mensaje" hide-details @keydown.enter.exact.prevent="send" no-resize rows="3" outlined label=""></v-textarea>
                    </v-col>

                    <v-col cols="12" md="1">
                        <v-btn class="d-none d-sm-flex" @click="send" :disabled="!mensaje.mensaje || isloading" :loader="isloading" fab color="green">
                            <v-icon class="white--text">
                                mdi-send
                            </v-icon>
                        </v-btn>

                        <v-btn class="d-flex d-sm-none" color="green" block :disabled="!mensaje.mensaje || isloading" :loader="isloading">
                            <v-icon class="white--text">
                                mdi-send
                            </v-icon>
                        </v-btn>
                    </v-col>

                </v-row>
            </v-card-actions>
        </v-card>
    </v-container>
</template>

<script>
    import defaultMensaje from './defaultMensaje'
    import ownMensaje from './ownMensaje'

    export default {
        name: 'chat',
        components: {
            defaultMensaje,
            ownMensaje
        },
        data() {
            return {
                mensaje: {
                    mensaje: null
                },
                chat: {
                    user_id: null,
                    unrread: null,
                    mensajes: []
                }
            }
        },

        created() {
            this.getChat(this.$route.query.id)
        },

        updated() {
            this.$nextTick(() => {
                if (this.$refs.chatcontainer && this.$refs.chatcontainer.lastElementChild) {
                    this.$refs.chatcontainer.scrollTop = this.$refs.chatcontainer.lastElementChild.offsetTop
                }
            })
        },

        methods: {
            getChat(id = null) {
                let url = id ? `api/get-chat/${id}` : `api/get-chat`
                axios.get(url).then(res => {
                    this.chat = res.data
                    this.setSeen(res.data.id)
                }, res => {
                    this.$toast.error('Error consultando mensajes')
                })
            },
            send() {
                if (this.mensaje.mensaje == null) {
                    return this.$toast.warn('Campo de texto vacio')
                }
                axios.post(`api/send-mensaje/${this.chat.id}`, this.mensaje).then(res => {
                    this.mensaje.mensaje = null
                    this.chat.mensajes.push(res.data)

                }, res => {
                    this.$toast.error('Error consultando mensajes')
                })
            },

            isOwner(mensaje) {
                return (mensaje.from_user == this.user_id) || (this.user.role == 1 && mensaje.to_user != null)
            },

            newline() {
                this.mensaje.mensaje = `${this.mensaje.mensaje}\n`;
            },

            setSeen(chat_id) {
                axios.get(`api/set-seen-messages/${chat_id}`).then(res => {
                    console.log('success')

                }, res => {
                    this.$toast.warn('Consulta ha fallado')
                })
            }
        },

        computed: {
            user_id() {
                return localStorage.getItem('user_id')
            },
            isloading() {
                return this.$store.getters.getloading
            },
            user() {
                return this.$store.getters.getuser
            },
        }
    }
</script>