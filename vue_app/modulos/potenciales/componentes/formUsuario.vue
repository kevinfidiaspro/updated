<template>
    <v-card flat>
        <v-row dense>
            <v-row dense>
                <v-col cols="12" md="6" align="center">
                    <v-card class="mt-3 mr-4 mb-6" style="max-height: 100px !important;max-width: 250px !important;" elevation="0">
                        <v-img v-if="proyecto.usuario.avatar" :src="proyecto.usuario.avatar" contain aspect-ratio="3"></v-img>
                        <v-img v-if="!proyecto.usuario.avatar" src="/default.png" contain aspect-ratio="3"></v-img>
                        <v-btn class="mt-1" v-if="proyecto.usuario.avatar" fab x-small color="error" @click="defaultAvatar()">
                            <v-icon class="white--text">mdi-close-circle-outline</v-icon>
                        </v-btn>
                    </v-card>
                </v-col>
                <v-col cols="12" md="5" class="mt-6" style="max-width: 360px !important;" align="center">
                    <v-file-component v-on:file_changed="fileChanged"></v-file-component>
                </v-col>
            </v-row>
            <v-col cols="12" md="4">
                <v-autocomplete v-if="!proyecto.usuario_id" dense outlined prepend-icon="mdi-account-search-outline" v-model="proyecto.usuario" 
                    :error-messages=" errors.errors['usuario.nombre'] ? errors.errors['usuario.nombre'][0] : null" 
                    return-object :items="usuarios" item-value="id" item-text="nombre" label="Seleccione o Cree Usuario Nuevo">
                </v-autocomplete>
            </v-col>
        </v-row>
        <v-row dense>
            <v-col cols="12" md="4">
                <v-text-field dense outlined :error-messages=" errors.errors['usuario.nombre'] ? errors.errors['usuario.nombre'][0] : null" 
                    v-model="proyecto.usuario.nombre" label="Nombre y Apellidos" required>
                </v-text-field>
            </v-col>
            <v-col cols="12" md="4">
                <v-text-field dense outlined v-model="proyecto.usuario.nombre_fiscal" label="Nombre Fiscal" >
                </v-text-field>
            </v-col>
            <v-col cols="12" md="4">
                <v-text-field dense outlined counter maxlength="9" minlength="9" v-model="proyecto.usuario.cif" label="CIF/DNI">
                </v-text-field>
            </v-col>
        </v-row>
        <v-row dense>
            <v-col cols="12" md="4">
                <v-text-field dense outlined :error-messages=" errors.errors['usuario.telefono'] ? errors.errors['usuario.telefono'][0] : null " 
                    v-model="proyecto.usuario.telefono" label="Teléfono" :rules="[rules.number_rule]" counter maxlength="9" required>
                </v-text-field>
            </v-col>
            <v-col cols="12" md="4">
                <v-text-field dense outlined :error-messages="  errors.errors['usuario.email'] ? errors.errors['usuario.email'][0] : null "
                    v-model="proyecto.usuario.email" label="Email" required>
                </v-text-field>
            </v-col>
        </v-row>
        <v-row dense>
            <v-col cols="12" md="4">
                <v-text-field dense outlined label="Direccion" v-model="proyecto.usuario.direccion"></v-text-field>
            </v-col>
            <v-col cols="12" md="2">
                <v-text-field dense outlined v-model="proyecto.usuario.codigo_postal" label="Codigo Postal" :rules="[rules.number_rule]" counter maxlength="5" required>
                </v-text-field>
            </v-col>
            <v-col cols="12" md="3">
                <v-text-field dense outlined v-model="proyecto.usuario.localidad" label="Localidad"></v-text-field>
            </v-col>
            <v-col cols="12" md="3">
                <v-autocomplete dense outlined v-model="proyecto.usuario.provincia_id" :items="provincias" item-value="id" item-text="nombre" label="Provincia">
                </v-autocomplete>
            </v-col>
        </v-row>
        <v-row dense>
            <v-col cols="12" md="6" class="mt-2">
                <v-text-field dense outlined counter maxlength="20" minlength="20" v-model="proyecto.usuario.cuenta" label="Cuenta Bancaria"></v-text-field>
            </v-col>
            <v-col cols="12" md="6">
                <v-menu ref="menu" v-model="menu" :close-on-content-click="false" :return-value.sync="proyecto.usuario.fecha_alta" transition="scale-transition" offset-y min-width="290px">
                    <template v-slot:activator="{ on, attrs }">
                        <v-text-field v-model="proyecto.usuario.fecha_alta" label="Fecha de Alta Cliente" append-icon="mdi-calendar" v-bind="attrs" v-on="on">
                        </v-text-field>
                    </template>
                    <v-date-picker color="#1d2735" first-day-of-week="1" v-model="proyecto.usuario.fecha_alta" no-title scrollable>
                        <v-spacer></v-spacer>
                        <v-btn text color="red" @click="menu = false"><strong>Cancelar</strong></v-btn>
                        <v-btn text color="success" @click="$refs.menu.save(proyecto.usuario.fecha_alta)"><strong>OK</strong></v-btn>
                    </v-date-picker>
                </v-menu>
            </v-col>
        </v-row>
        <v-row dense>
            <v-col cols="12" md="12" class="mb-3">
                <!-- Descripcion -->
                <small><strong>Observaciones</strong></small>
                <!-- Editor -->
                <ckeditor style="cursor: none" :editor="editor" v-model="proyecto.usuario.observaciones" :config="editorConfig"></ckeditor>
            </v-col>
        </v-row>
    </v-card>
</template>
<script>
    export default {
        props: ['proyecto', 'errors', 'usuarios', 'provincias', 'editorConfig', 'editor', 'editorData'],
        data() {
            return { menu: false, rules: { number_rule: (value) => /^\d+$/.test(value) || "Campo numérico", } }
        },

        methods: {
            fileChanged(base_64) {
                this.proyecto.usuario.avatar = base_64;
            },
            defaultAvatar() {
                this.proyecto.usuario.avatar = null;
            },
            getAvatar(e) {
                this.createImage(e.target.files[0]);
            },
            createImage: function(file) {
                let reader = new FileReader();
                reader.onload = (e) => {
                    this.proyecto.usuario.avatar = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        }
    }
</script>