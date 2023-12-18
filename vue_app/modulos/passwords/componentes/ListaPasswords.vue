<template>
    <v-card class="pa-3 ma-3">
        <v-toolbar flat color="#1d2735" dark>
            <v-icon class="white--text" style="font-size: 45px;">mdi-form-textbox-password</v-icon><pre><v-toolbar-title><h2>Lista Contraseñas</h2></v-toolbar-title></pre>
        </v-toolbar>
        <loader v-if="isloading"></loader>
        <v-tooltip right>
            <template v-slot:activator="{ on, attrs }">
                <v-btn fab :to="'/'" :loading="isloading" :disabled="isloading" color="blue" class="mx-3 mt-2" v-bind="attrs" v-on="on">
                    <v-icon class="white--text">mdi-arrow-left-bold-outline</v-icon>
                </v-btn>
            </template>
            <span>Volver</span>
        </v-tooltip>
        <v-tooltip right>
            <template v-slot:activator="{ on, attrs }">
                <v-btn fab :to="{ path: `/guardar-password` }" :loading="isloading" :disabled="isloading" color="orange darken-1" class="mt-2" v-bind="attrs" v-on="on">
                    <v-icon class="white--text">mdi-lastpass</v-icon>
                </v-btn>
            </template>
            <span>Nueva Contraseña</span>
        </v-tooltip>
        <v-row>
            <v-col cols="12" md="4">
                <v-text-field prepend-icon="mdi-lock-question" v-model="search" label="Buscar"></v-text-field>
            </v-col>
        </v-row>
        <v-data-table dense :headers="headers" :items="passwords" :search="search" :items-per-page="10" item-key="id" class="elevation-1" :sort-by="['tipo']" :sort-desc="[false]">
            <template v-slot:item.action="{ item }">
                <router-link :to="{ path: `/guardar-password?id=${item.id}` }" class="action-buttons">
                    <v-icon small class="mr-2" color="#1d2735" style="font-size: 25px;" title="EDITAR">mdi-pencil-outline</v-icon>
                </router-link>
                <v-icon @click="openModal(item)" small class="mr-2" color="red" style="font-size: 25px;" title="BORRAR">mdi-trash-can</v-icon>
            </template>
        </v-data-table>
        <v-dialog v-model="dialog" max-width="500px">
            <v-card>
                <v-card-title class="text-h5 aviso" style="justify-content: center; background:#1d2735; color:white">
                    Aviso
                </v-card-title>
                <v-card-text style="text-align:center">
                    <h2>¿Estás seguro que deseas eliminar?</h2>
                </v-card-text>
                <v-card-actions class="pt-3">
                    <v-spacer></v-spacer>
                    <v-btn
                        color="error"
                        large
                        @click="dialog = false;selectedItem={}">Cancelar</v-btn>
                    <v-btn
                        color="success"
                        large
                        @click="deletePassword()">Confirmar</v-btn>
                    <v-spacer></v-spacer>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-card>
</template>

<script>
    export default {
        data() {
            return {
                search: '',
                passwords: [],
                selectedItem: 0,
                dialog: false,
                headers: [
                    {text: 'CONCEPTO / WEB / PLATAFORMA',value: 'web',sortable: false},
                    {text: 'Cuenta',value: 'usuario_mail',sortable: false},
                    {text: 'Tipo',value: 'tipo',sortable: false},
                    {text: 'Acciones',value: 'action',sortable: false}
                ],
            }
        },
        created() {
            this.getAllPasswords()
        },
        methods: {
            getAllPasswords() {
                axios.get(`api/get-all-passwords/`).then(res => {
                    this.passwords = res.data
                }, err => {
                    this.$toast.error('Error consultando cuentas')
                })
            },
            deletePassword(item) {
                this.dialog = false
                axios.get(`api/delete-password/${this.passwords[this.selectedItem].id}`).then(res => {
                    this.passwords.splice(this.selectedItem, 1)
                    this.$toast.sucs('Contraseña eliminada')
                }, err => {
                    this.$toast.error('Error eliminando contraseña')
                })
            },
            openModal(item){
                this.selectedItem = this.passwords.indexOf(item);
                this.dialog = true;
            },
        },
        computed: {
            isloading: function() {
                return this.$store.getters.getloading
            },
        }
    }
</script>
