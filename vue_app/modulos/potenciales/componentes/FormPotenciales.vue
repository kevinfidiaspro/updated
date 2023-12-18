<template>
    <v-container>
        <loader v-if="isloading"></loader>
        <v-card>
            <v-toolbar flat color="#1d2735" dark>
                <v-toolbar-title
                    >Guardar / Editar {{ $route.meta.titulo }}</v-toolbar-title
                >
            </v-toolbar>
            <v-tabs horizontal>
                <!-- Pestañas -->
                <v-tab
                    ><v-icon left>mdi-book-variant-multiple</v-icon
                    ><Video></Video>enta</v-tab
                >
                <v-tab><v-icon left>mdi-account</v-icon>Cliente</v-tab>
                <v-tab
                    ><v-icon left>mdi-folder-multiple-outline</v-icon
                    >Archivo</v-tab
                >
                <v-tab><v-icon left>mdi-calendar</v-icon>Seguimiento</v-tab>
                <!-- Pestañas -->
                <v-tab-item class="pa-3 ma-1">
                    <v-card flat>
                        <v-row dense>
                            <v-col cols="12" md="6" class="pt-3 pl-0 pb-0">
                                <v-select
                                    dense
                                    outlined
                                    :error-messages="
                                        errors.errors['servicio_id']
                                            ? errors.errors['servicio_id'][0]
                                            : null
                                    "
                                    v-model="potencial.servicio_id"
                                    :items="servicios"
                                    item-text="nombre"
                                    item-value="id"
                                    label="Servicio Contratado"
                                >
                                </v-select>
                            </v-col>
                            <v-col cols="12" md="6" class="pl-3 pt-0 pb-0">
                                <v-menu
                                    ref="menu2"
                                    v-model="menu2"
                                    :close-on-content-click="false"
                                    :return-value.sync="potencial.fecha_alta"
                                    transition="scale-transition"
                                    :error-messages="
                                        errors.errors['fecha_alta']
                                            ? errors.errors['fecha_alta'][0]
                                            : null
                                    "
                                    offset-y
                                    min-width="290px"
                                >
                                    <template v-slot:activator="{ on, attrs }">
                                        <v-text-field
                                            v-model="potencial.fecha_alta"
                                            label="Fecha de Alta Servicio"
                                            append-icon="mdi-calendar"
                                            v-bind="attrs"
                                            v-on="on"
                                        >
                                        </v-text-field>
                                    </template>
                                    <v-date-picker
                                        color="#1d2735"
                                        first-day-of-week="1"
                                        v-model="potencial.fecha_alta"
                                        no-title
                                        scrollable
                                    >
                                        <v-spacer></v-spacer>
                                        <v-btn
                                            text
                                            color="red"
                                            @click="menu2 = false"
                                            ><strong>Cancelar</strong></v-btn
                                        >
                                        <v-btn
                                            text
                                            color="success"
                                            @click="
                                                $refs.menu2.save(
                                                    potencial.fecha_alta
                                                )
                                            "
                                            ><strong>OK</strong></v-btn
                                        >
                                    </v-date-picker>
                                </v-menu>
                            </v-col>
                        </v-row>
                        <v-row dense>
                            <v-col cols="12" md="12">
                                <!-- Descripcion -->
                                <small
                                    ><strong
                                        >Detalles del Servicio
                                        contratado</strong
                                    ></small
                                >
                                <!-- Editor -->
                                <ckeditor
                                    style="cursor: none"
                                    :editor="editor"
                                    v-model="potencial.detalle_servicio"
                                    :config="editorConfig"
                                ></ckeditor>
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col v-if="potencial.lead_form" cols="12" md="6">
                                <v-text-field
                                    readonly
                                    dense
                                    outlined
                                    v-model="potencial.lead_form.name"
                                    label="Campaña Facebook"
                                ></v-text-field>
                            </v-col>
                            <v-col cols="12" md="6">
                                <dynamic_select
                                    :dense="true"
                                    :outlined="true"
                                    title="Estado"
                                    v-bind:estados="estados_potencial"
                                    show="nombre"
                                    :elemento="potencial.id_estado_potencial"
                                    @create="createEstado"
                                    @delete="deleteEstado"
                                    @getEstado="updateEstadoS"
                                    @update="updateEstado"
                                    @elementoUpdate="updatethisEstado"
                                >
                                    <v-text-field
                                        v-model="estado_potencial.nombre"
                                        type="text"
                                        label="Nombre"
                                        required
                                    ></v-text-field>
                                </dynamic_select>
                            </v-col>
                            <v-col cols="12" md="6">
                                <v-autocomplete
                                    dense
                                    outlined
                                    label="Campaña"
                                    v-model="potencial.id_lead_form"
                                    :items="campanas"
                                    item-text="name"
                                    item-value="id"
                                ></v-autocomplete>
                            </v-col>
                            <v-col cols="12" md="6">
                                <v-checkbox
                                    dense
                                    label="¿Es Kit?"
                                    v-model="potencial.es_kit"
                                ></v-checkbox>
                            </v-col>
                        </v-row>
                        <v-row dense>
                            <v-col cols="12" md="4" class="mx-0 my-0 px-0 py-0">
                                <v-col cols="12" md="12">
                                    <v-select
                                        dense
                                        outlined
                                        :error-messages="
                                            errors.errors['estado_id']
                                                ? errors.errors['estado_id'][0]
                                                : null
                                        "
                                        v-model="potencial.estado_id"
                                        :items="estados"
                                        item-text="nombre"
                                        item-value="id"
                                        label="Estado"
                                    >
                                    </v-select>
                                </v-col>
                            </v-col>
                            <v-col cols="12" md="4" class="mx-0 my-0 px-0 py-0">
                                <v-col cols="12" md="12">
                                    <v-text-field
                                        dense
                                        outlined
                                        :error-messages="
                                            errors.errors['pvp']
                                                ? errors.errors['pvp'][0]
                                                : null
                                        "
                                        v-model="potencial.pvp"
                                        label="Precio Proyecto"
                                    >
                                    </v-text-field>
                                </v-col>
                            </v-col>
                            <v-col cols="12" md="4" class="mx-0 my-0 px-0 py-0">
                                <v-col cols="12" md="12">
                                    <v-text-field
                                        dense
                                        outlined
                                        v-model="potencial.pvp_gasto"
                                        label="Gasto Externo"
                                    ></v-text-field>
                                </v-col>
                            </v-col>
                        </v-row>
                        <v-row dense>
                            <v-col cols="12" md="12">
                                <!-- Descripcion -->
                                <small
                                    ><strong
                                        >Detalles de Gasto Externo</strong
                                    ></small
                                >
                                <!-- Editor -->
                                <ckeditor
                                    style="cursor: none"
                                    :editor="editor"
                                    v-model="potencial.detalles_gasto"
                                    :config="editorConfig"
                                ></ckeditor>
                            </v-col>
                        </v-row>
                    </v-card>
                </v-tab-item>

                <v-tab-item class="pa-3 ma-1">
                    <v-card flat>
                        <v-row dense>
                            <v-row>
                                <v-col cols="12" md="6" align="center">
                                    <v-card
                                        class="mt-3 mr-4 mb-6"
                                        style="
                                            max-height: 100px !important;
                                            max-width: 250px !important;
                                        "
                                        elevation="0"
                                    >
                                        <v-img
                                            v-if="potencial.usuario.avatar"
                                            :src="potencial.usuario.avatar"
                                            contain
                                            aspect-ratio="3"
                                        ></v-img>
                                        <v-img
                                            v-if="!potencial.usuario.avatar"
                                            src="/default.png"
                                            contain
                                            aspect-ratio="3"
                                        ></v-img>
                                        <v-btn
                                            class="mt-1"
                                            v-if="potencial.usuario.avatar"
                                            fab
                                            x-small
                                            color="error"
                                            @click="defaultAvatar()"
                                        >
                                            <v-icon class="white--text"
                                                >mdi-close-circle-outline</v-icon
                                            >
                                        </v-btn>
                                    </v-card>
                                </v-col>
                                <v-col
                                    cols="12"
                                    md="5"
                                    class="mt-6"
                                    style="max-width: 360px !important"
                                    align="center"
                                >
                                    <v-file-component
                                        v-on:file_changed="fileChanged"
                                    ></v-file-component>
                                </v-col>
                            </v-row>
                            <v-col cols="12" md="6" class="mt-4">
                                <v-autocomplete
                                    v-if="!potencial.usuario_id"
                                    dense
                                    outlined
                                    prepend-icon="mdi-account-search-outline"
                                    v-model="potencial.usuario"
                                    :error-messages="
                                        errors.errors['usuario.nombre']
                                            ? errors.errors['usuario.nombre'][0]
                                            : null
                                    "
                                    return-object
                                    :items="usuarios"
                                    item-value="id"
                                    item-text="nombre"
                                    label="Seleccione o Cree Usuario Nuevo"
                                >
                                </v-autocomplete>
                            </v-col>
                        </v-row>
                        <v-row dense>
                            <v-col cols="12" md="4">
                                <v-text-field
                                    dense
                                    outlined
                                    :error-messages="
                                        errors.errors['usuario.nombre']
                                            ? errors.errors['usuario.nombre'][0]
                                            : null
                                    "
                                    v-model="potencial.usuario.nombre"
                                    label="Nombre y Apellidos"
                                    required
                                >
                                </v-text-field>
                            </v-col>
                            <v-col cols="12" md="4">
                                <v-text-field
                                    dense
                                    outlined
                                    v-model="potencial.usuario.nombre_fiscal"
                                    label="Nombre Fiscal"
                                >
                                </v-text-field>
                            </v-col>
                            <v-col cols="12" md="4">
                                <v-text-field
                                    dense
                                    outlined
                                    v-model="potencial.usuario.cif"
                                    label="CIF/DNI"
                                >
                                </v-text-field>
                            </v-col>
                        </v-row>
                        <v-row dense>
                            <v-col cols="12" md="3">
                                <v-text-field
                                    dense
                                    outlined
                                    :error-messages="
                                        errors.errors['usuario.telefono']
                                            ? errors.errors[
                                                  'usuario.telefono'
                                              ][0]
                                            : null
                                    "
                                    v-model="potencial.usuario.telefono"
                                    label="Teléfono"
                                    :rules="[rules.number_rule]"
                                    counter
                                    maxlength="12"
                                    required
                                >
                                </v-text-field>
                            </v-col>
                            <v-col cols="12" md="3">
                                <v-text-field
                                    dense
                                    outlined
                                    :error-messages="
                                        errors.errors['usuario.email']
                                            ? errors.errors['usuario.email'][0]
                                            : null
                                    "
                                    v-model="potencial.usuario.email"
                                    label="Email"
                                    required
                                >
                                </v-text-field>
                            </v-col>
                            <v-col cols="12" md="3">
                                <v-autocomplete
                                    dense
                                    outlined
                                    :items="empleados"
                                    item-value="id"
                                    item-text="nombre"
                                    label="Vendedor"
                                    v-model="potencial.usuario.vendedor_id"
                                >
                                </v-autocomplete>
                            </v-col>
                            <v-col cols="12" md="3">
                                <v-select
                                    readonly
                                    dense
                                    outlined
                                    :items="roles"
                                    item-value="id"
                                    item-text="role"
                                    label="Seleccione un Perfil"
                                    v-model="potencial.usuario.role"
                                >
                                </v-select>
                            </v-col>
                        </v-row>
                        <v-row dense>
                            <v-col cols="12" md="4">
                                <v-text-field
                                    dense
                                    outlined
                                    label="Direccion"
                                    v-model="potencial.usuario.direccion"
                                ></v-text-field>
                            </v-col>
                            <v-col cols="12" md="2">
                                <v-text-field
                                    dense
                                    outlined
                                    :error-messages="
                                        errors.errors['usuario.codigo_postal']
                                            ? errors.errors[
                                                  'usuario.codigo_postal'
                                              ][0]
                                            : null
                                    "
                                    v-model="potencial.usuario.codigo_postal"
                                    label="Codigo Postal"
                                    :rules="[rules.number_rule]"
                                    counter
                                    maxlength="5"
                                    required
                                >
                                </v-text-field>
                            </v-col>
                            <v-col cols="12" md="3">
                                <v-text-field
                                    dense
                                    outlined
                                    v-model="potencial.usuario.localidad"
                                    label="Localidad"
                                ></v-text-field>
                            </v-col>
                            <v-col cols="12" md="3">
                                <v-autocomplete
                                    dense
                                    outlined
                                    v-model="potencial.usuario.provincia_id"
                                    :items="provincias"
                                    item-value="id"
                                    item-text="nombre"
                                    label="Provincia"
                                >
                                </v-autocomplete>
                            </v-col>
                        </v-row>
                        <v-row dense>
                            <v-col cols="12" md="6" class="mt-2">
                                <v-text-field
                                    dense
                                    outlined
                                    v-model="potencial.usuario.cuenta"
                                    label="Cuenta Bancaria"
                                ></v-text-field>
                            </v-col>
                            <v-col cols="12" md="6">
                                <v-menu
                                    ref="menu"
                                    v-model="menu"
                                    :close-on-content-click="false"
                                    :return-value.sync="
                                        potencial.usuario.fecha_alta
                                    "
                                    transition="scale-transition"
                                    offset-y
                                    min-width="290px"
                                >
                                    <template v-slot:activator="{ on, attrs }">
                                        <v-text-field
                                            v-model="
                                                potencial.usuario.fecha_alta
                                            "
                                            label="Fecha de Alta Cliente"
                                            append-icon="mdi-calendar"
                                            v-bind="attrs"
                                            v-on="on"
                                        >
                                        </v-text-field>
                                    </template>
                                    <v-date-picker
                                        color="#1d2735"
                                        first-day-of-week="1"
                                        v-model="potencial.usuario.fecha_alta"
                                        no-title
                                        scrollable
                                    >
                                        <v-spacer></v-spacer>
                                        <v-btn
                                            text
                                            color="red"
                                            @click="menu = false"
                                            ><strong>Cancelar</strong></v-btn
                                        >
                                        <v-btn
                                            text
                                            color="success"
                                            @click="
                                                $refs.menu.save(
                                                    potencial.usuario.fecha_alta
                                                )
                                            "
                                            ><strong>OK</strong></v-btn
                                        >
                                    </v-date-picker>
                                </v-menu>
                            </v-col>
                        </v-row>
                        <v-row dense>
                            <v-col cols="12" md="12" class="mb-3">
                                <!-- Descripcion -->
                                <small><strong>Observaciones</strong></small>
                                <!-- Editor -->
                                <ckeditor
                                    style="cursor: none"
                                    :editor="editor"
                                    v-model="potencial.usuario.observaciones"
                                    :config="editorConfig"
                                ></ckeditor>
                            </v-col>
                        </v-row>
                    </v-card>
                </v-tab-item>

                <v-tab-item class="pa-3 ma-1">
                    <tab-archivo
                        tipo="potencial"
                        :item="potencial"
                    ></tab-archivo>
                </v-tab-item>
                <v-tab-item class="pa-3 ma-1">
                    <v-card flat>
                        <div
                            style="
                                display: flex;
                                justify-content: space-between;
                            "
                        >
                            <div class="font-weight-bold mb-3 black--text">
                                AGREGAR TAREA A SEGUIMIENTO DEL PROYECTO
                            </div>
                            <v-btn
                                fab
                                color="success"
                                @click="dialog_whatsapp = true"
                                ><v-icon color="white"
                                    >mdi-whatsapp</v-icon
                                ></v-btn
                            >
                        </div>

                        <v-row>
                            <v-col cols="12">
                                <v-form class="mt-3">
                                    <v-row>
                                        <v-col cols="12">
                                            <date-select
                                                v-model="seguimiento.fecha"
                                                label="Fecha"
                                            >
                                            </date-select>
                                        </v-col>
                                        <v-col cols="12">
                                            <v-checkbox
                                                label="Alarma"
                                                v-model="seguimiento.alarma"
                                            ></v-checkbox>
                                        </v-col>
                                        <v-col cols="12">
                                            <v-autocomplete
                                                outlined
                                                label="Horas"
                                                v-model="seguimiento.hora"
                                                :items="horas"
                                                item-value="value"
                                                item-text="text"
                                            >
                                            </v-autocomplete>
                                        </v-col>
                                        <v-col cols="12">
                                            <v-textarea
                                                outlined
                                                label="Comentario"
                                                v-model="seguimiento.comentario"
                                            >
                                            </v-textarea>
                                        </v-col>
                                    </v-row>

                                    <v-row>
                                        <v-col cols="12" class="pb-4">
                                            <v-btn
                                                v-if="!seguimiento.id"
                                                :disabled="isloading"
                                                color="success"
                                                class="white--text"
                                                @click="saveSeguimiento"
                                                >Añadir</v-btn
                                            >
                                            <v-btn
                                                @click="saveSeguimiento"
                                                v-if="seguimiento.id"
                                                :disabled="isloading"
                                                color="success"
                                                class="white--text"
                                                >Actualizar</v-btn
                                            >
                                            <v-btn
                                                @click="deleteSeguimiento"
                                                v-if="seguimiento.id"
                                                :disabled="isloading"
                                                color="red"
                                                class="white--text"
                                                >Eliminar</v-btn
                                            >
                                        </v-col>
                                    </v-row>
                                </v-form>
                            </v-col>
                        </v-row>
                        <v-data-table
                            dense
                            :headers="seguimientos_headers"
                            :items="Seguimientos"
                            :items-per-page="15"
                            item-key="id"
                            class="elevation-1"
                            :sort-by="['nombre']"
                            :sort-desc="[false]"
                        >
                            <template v-slot:item.fecha="{ item }">
                                <span>{{ item.fecha | format_date }}</span>
                            </template>
                            <template v-slot:item.hora="{ item }">
                                {{ item.hora | hora_formated }}
                            </template>
                            <template v-slot:item.action="{ item }">
                                <v-icon
                                    @click="seguimiento = item"
                                    small
                                    class="mr-2"
                                    color="#1d2735"
                                    style="font-size: 25px"
                                    title="EDITAR"
                                    >mdi-pencil-outline</v-icon
                                >
                                <v-icon
                                    @click="deleteSeguimiento(item)"
                                    small
                                    class="mr-2"
                                    color="red"
                                    style="font-size: 25px"
                                    title="BORRAR"
                                    >mdi-trash-can</v-icon
                                >
                            </template>
                        </v-data-table>
                    </v-card>
                </v-tab-item>
            </v-tabs>
        </v-card>
        <v-row class="mt-3" v-if="role == 1 || role == 5">
            <!-- Botones Potencial -->
            <v-col cols="12">
                <v-tooltip top>
                    <template v-slot:activator="{ on, attrs }">
                        <v-btn
                            fab
                            :to="{
                                path: `/registrar-presupuesto?id=${$route.query.id}`,
                            }"
                            :loading="isloading"
                            :disabled="isloading"
                            color="blue-grey lighten-2"
                            class="mx-2"
                            v-bind="attrs"
                            v-on="on"
                            readonly
                        >
                            <v-icon class="white--text"
                                >mdi-text-box-search-outline</v-icon
                            >
                        </v-btn>
                    </template>
                    <span>Ver Presupuesto</span>
                </v-tooltip>
            </v-col>
        </v-row>
        <v-row class="mt-3">
            <!-- Botones Navegacion -->
            <v-col cols="12">
                <v-tooltip top>
                    <template v-slot:activator="{ on, attrs }">
                        <v-btn
                            fab
                            @click="volver"
                            :loading="isloading"
                            :disabled="isloading"
                            color="blue"
                            class="mx-2"
                            v-bind="attrs"
                            v-on="on"
                        >
                            <v-icon class="white--text"
                                >mdi-arrow-left-bold-outline</v-icon
                            >
                        </v-btn>
                    </template>
                    <span>Volver</span>
                </v-tooltip>
                <v-tooltip top>
                    <template v-slot:activator="{ on, attrs }">
                        <v-btn
                            fab
                            @click="savePotencial"
                            :loading="isloading"
                            :disabled="isloading"
                            color="success"
                            class="mx-2"
                            v-bind="attrs"
                            v-on="on"
                        >
                            <v-icon class="white--text"
                                >mdi-content-save-all</v-icon
                            >
                        </v-btn>
                    </template>
                    <span>Guardar {{ $route.meta.titulo }}</span>
                </v-tooltip>
            </v-col>
        </v-row>
        <WhatsAppDialog
            :name="potencial.usuario.nombre"
            v-model="dialog_whatsapp"
            :to="potencial.usuario.telefono"
        ></WhatsAppDialog>
    </v-container>
</template>
<script>
import WhatsAppDialog from "./WhatsAppDialog.vue";
import { provincias_mixin } from "../../../global_mixins/provincias_mixin";
import { servicios_mixin } from "../../../global_mixins/servicios_mixin";
import { estados_mixin } from "../../../global_mixins/estados_mixin";
import FileInput from "../../../global_components/FileInput.vue";
import VFileComponent from "../../../global_components/VFileComponent.vue";
import ClassicEditor from "@ckeditor/ckeditor5-build-classic";
import tabArchivo from "../../../global_components/tabArchivo";
export default {
    components: {
        "file-input": FileInput,
        VFileComponent,
        tabArchivo,
        WhatsAppDialog,
    },
    mixins: [provincias_mixin, servicios_mixin, estados_mixin],
    data() {
        return {
            empleados: [],

            dialog_whatsapp: false,
            horas: [],
            seguimiento: {},
            Seguimientos: [],
            seguimientos_headers: [
                {
                    text: "Nombre",
                    value: "proyecto.usuario.nombre",
                    sortable: false,
                },
                { text: "comentario", value: "comentario", sortable: false },
                { text: "Fecha", value: "fecha", sortable: false },
                { text: "Hora", value: "hora", sortable: false },

                { text: "Acciones", value: "action", sortable: false },
            ],
            editor: ClassicEditor,
            editorData: "<p>Escriba Aqui Observaciones o contenido.</p>",
            editorConfig: {
                toolbar: {
                    items: [
                        "heading",
                        "bold",
                        "italic",
                        "bulletedList",
                        "numberedList",
                        "link",
                        "inserttable",
                    ],
                },
            },
            menu: false,
            menu: "",
            menu2: false,
            menu2: "",
            estados_potencial: [],
            estado_potencial: { nombre: null },
            potencial: {
                id: "",
                pvp: "",
                pvp_gasto: 0,
                es_kit: 0,
                usuario: {
                    id: "",
                    user_id: localStorage.getItem("user_id"),
                    nombre: "",
                    nombre_fiscal: "",
                    cif: "",
                    telefono: "",
                    email: "",
                    role: 2,
                    direccion: "",
                    codigo_postal: "",
                    localidad: "",
                    provincia_id: "",
                    cuenta: null,
                    fecha_alta: new Date().toISOString().substr(0, 10),
                    observaciones: null,
                    avatar: "",
                },
                estado_id: this.$route.meta.estado,
                servicio: {},
                fecha_alta: new Date().toISOString().substr(0, 10),
                detalle_servicio: null,
                detalles_gasto: null,
                archivos: [],
                tipo_proyecto: null,
                empleados: [],
            },
            servicio: { id: 1 },
            estado: { id: 1 },
            roles: [
                { id: 1, role: "Administrador" },
                { id: 2, role: "Cliente" },
                { id: 3, role: "Empleado" },
            ],
            campanas: [],
            usuarios: [],
            files: [],
            imagePreview: [],
            csrf: document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute("content"),
            user_id: localStorage.getItem("user_id"),
            rules: {
                number_rule: (value) => /^\d+$/.test(value) || "Campo numérico",
            },
        };
    },
    created() {
        this.getUsuarios();
        this.getEstadosPotencial();
        this.generateHoras();
        this.getCampanas();
        this.getEmpleados();
        if (this.$route.query.id) {
            this.getPotencialById(this.$route.query.id);
        } else {
            this.getSeguimientos();
        }
    },
    methods: {
        getEmpleados() {
            axios.post("api/get-usuarios-empleados-all").then((res) => {
                this.empleados = res.data.users;
            });
        },
        getCampanas() {
            const self = this;
            axios
                .get(`webhook/get-all-formularios?id=104140814678268`)
                .then(function (response) {
                    self.campanas = response.data;
                });
        },
        generateHoras() {
            this.horas = [];
            for (let val = 9; val <= 19; val += 0.5) {
                let str = val.toString().split(".");
                let txt = "";
                if (str.length > 1) {
                    txt = str[0].toString().padStart(2, "0") + ":30";
                } else {
                    txt = val.toString().padStart(2, "0") + ":00";
                }
                this.horas.push({ text: txt, value: val });
            }
        },
        getSeguimientos() {
            const self = this;
            axios
                .get(`api/get-tareas-proyecto?cliente=${this.potencial.id}`)
                .then(
                    (res) => {
                        self.Seguimientos = res.data;
                    },
                    (err) => {
                        this.$toast.error("Error consultando Seguimientos");
                    }
                );
        },
        deleteSeguimiento(item) {
            axios.post("api/cancel-tareas-proyecto", { id: item.id }).then(
                (res) => {
                    this.$toast.sucs("Seguimiento eliminado");
                    this.dialog = false;
                    this.getSeguimientos();
                },
                (err) => {
                    this.$toast.error("Error eliminando Seguimiento");
                }
            );
        },
        saveSeguimiento() {
            const self = this;
            this.seguimiento.id_proyecto = this.potencial.id;
            axios
                .post("api/save-tareas-proyecto", this.seguimiento)
                .then(function (response) {
                    self.seguimiento = {};
                    self.getSeguimientos();
                });
        },
        updateEstadoS(index) {
            this.estado_potencial = this.estados_potencial[index];
        },
        deleteEstado(id) {
            axios.get(`api/delete-estado-potencial/${id}`).then(
                (res) => {
                    this.getEstadosPotencial();
                    this.delete_dialog = false;
                    this.potencial.id_estado_potencial = null;
                },
                (res) => {}
            );
        },
        //funciones del Crud de estado
        updatethisEstado(id) {
            this.potencial.id_estado_potencial = id;
        },
        createEstado() {
            axios.post("api/save-estado-potencial", this.estado_potencial).then(
                (res) => {
                    this.getEstadosPotencial();
                },
                (res) => {}
            );
        },
        updateEstado() {
            console.log(this.estado);
            axios.post("api/save-estado-potencial", this.estado_potencial).then(
                (res) => {
                    this.getEstados();
                },
                (res) => {}
            );
        },
        getEstadosPotencial() {
            axios.get("api/get-all-estados-potencial").then((res) => {
                this.estados_potencial = res.data;
            });
        },
        getPotencialById(potencial_id) {
            axios.get(`api/get-potencial-by-id/${potencial_id}`).then(
                (res) => {
                    this.potencial = res.data;
                    this.getSeguimientos();
                },
                (res) => {
                    this.$toast.error("Error consultando potencial");
                }
            );
        },
        async savePotencial() {
            try {
                this.user_id = localStorage.getItem("user_id");
                this.potencial.user_id = this.user_id;
                var formData = new FormData();
                formData.append("potencial", JSON.stringify(this.potencial));
                let archivos = this.potencial.archivos.filter(
                    (archivo) => !archivo.id
                );
                archivos.forEach((item, i) =>
                    formData.append("itemsFiles[" + i + "]", item.file)
                );
                await axios.post("api/save-potencial", formData);
                this.$toast.sucs("Proyecto guardado con exito");
                this.volver();
            } catch (error) {
                this.$toast.error(
                    "Error guardando Potencial, Compruebe todos los campos requeridos"
                );
            }
        },
        volver() {
            if (this.$route.query.seguimiento == "1") {
                let fecha = "";
                if (this.$route.query.fecha != null) {
                    fecha = `?fecha=${this.$route.query.fecha}`;
                }
                this.$router.push(`/calendario-seguimiento${fecha}`);
            } else if (this.$route.query.seguimiento_cliente == "1") {
                let fecha = "";
                if (this.$route.query.fecha != null) {
                    fecha = `?fecha=${this.$route.query.fecha}`;
                }
                this.$router.push(`/calendario-seguimiento-cliente${fecha}`);
            } else {
                this.$router.push(`/${this.$route.meta.listado}`);
            }
        },
        getUsuarios() {
            axios.get(`api/get-usuarios`).then(
                (res) => {
                    this.usuarios = res.data.users;
                },
                (err) => {
                    this.$toast.error("Error consultando clientes");
                }
            );
        },
        getUsuarioById(usuario_id) {
            axios.get(`api/get-usuario-by-id/${usuario_id}`).then(
                (res) => {
                    this.usuario = res.data.user;
                },
                (res) => {
                    this.$toast.error("Error consultando Usuario");
                }
            );
        },
        fileChanged(base_64) {
            this.potencial.usuario.avatar = base_64;
        },
        defaultAvatar() {
            this.potencial.usuario.avatar = null;
        },
    },
    computed: {
        role() {
            let role = localStorage.getItem("role");
            console.log(role);
            return role;
        },
        isloading() {
            return this.$store.getters.getloading;
        },
        errors() {
            return this.$store.getters.geterrors;
        },
    },
};
</script>
<style>
div.v-messages.theme--light {
    margin-top: -1px !important;
    margin-bottom: -1px !important;
    padding-top: -1px !important;
    padding-bottom: -1px !important;
}

div.v-text-field__details {
    margin-top: -1px !important;
    margin-bottom: -1px !important;
    padding-top: -1px !important;
    padding-bottom: -1px !important;
}
</style>
