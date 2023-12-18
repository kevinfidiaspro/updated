<template>
    <v-card class="pa-3 ma-3">
        <v-toolbar flat color="#1d2735" dark>
            <v-icon class="white--text" style="font-size: 45px">
                mdi-chat
            </v-icon>
            <pre><v-toolbar-title><h2>Lista chats</h2></v-toolbar-title></pre>
        </v-toolbar>
        <loader v-if="isloading"></loader>
        <v-data-table dense :headers="headers" :items="chats" disable-pagination hide-default-footer item-key="id" class="elevation-0 mt-6">
            <template v-slot:item.unrread="{ item }">
                <router-link :to="{ path: `/chat?id=${item.id}` }" class="action-buttons">
                    <v-chip small dark :color="item.unrread > 0 ? 'green' : 'red'">{{item.unrread}}</v-chip>
                </router-link>
            </template>
        </v-data-table>
        <v-tooltip right>
            <template v-slot:activator="{ on, attrs }">
                <v-btn fab :to="'/'" :loading="isloading" :disabled="isloading" color="blue" class="mt-3 mx-3" v-bind="attrs" v-on="on">
                    <v-icon class="white--text">mdi-arrow-left-bold-outline</v-icon>
                </v-btn>
            </template>
            <span>Volver</span>
        </v-tooltip>
    </v-card>
</template>
<script>
    export default {
        data() {
            return {
                chats: [],
                headers: [{
                        text: 'id',
                        value: 'id',
                    },
                    {
                        text: 'nombre',
                        value: 'nombre',
                    },
                    {
                        text: 'email',
                        value: 'email',
                    },
                    {
                        text: 'Mensajes',
                        value: 'unrread',
                    },
                ]
            }
        },

        created() {
            this.getChats()
        },

        methods: {
            getChats() {
                axios.get(`api/get-chats`).then(res => {
                    this.chats = res.data
                }, res => {
                    this.$toast.error('Error consultando lista de chats')
                })
            }
        },
        computed: {
            isloading() {
                return this.$store.getters.getloading
            }
        }
    }
</script>