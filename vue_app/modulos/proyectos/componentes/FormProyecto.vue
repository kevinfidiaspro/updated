<template>
    <v-container>
        <loader v-if="isloading"></loader>
        <v-card>
            <v-toolbar flat color="#1d2735" dark>
                <v-toolbar-title>Guardar / Editar Proyecto</v-toolbar-title>
            </v-toolbar>
            <v-tabs horizontal>
                <!-- Pestañas -->
                <v-tab
                    ><v-icon left>mdi-book-variant-multiple</v-icon
                    >Proyecto</v-tab
                >
                <v-tab v-if="role == 1 || role == 5 || role == 9"
                    ><v-icon left>mdi-account</v-icon>Cliente</v-tab
                >
                <v-tab v-if="role == 1 || role == 5"
                    ><v-icon left>mdi-list-status</v-icon>Estado del
                    Proyecto</v-tab
                >
                <v-tab v-if="role == 1 || role == 5 || role == 9"
                    ><v-icon left>mdi-folder-multiple-outline</v-icon
                    >Archivo</v-tab
                >
                <v-tab v-if="role == 1 || role == 5"
                    ><v-icon left>mdi-account-group</v-icon>Usuarios
                    Asignados</v-tab
                >
                <v-tab v-if="role == 1 || role == 5 || role == 9"
                    ><v-icon left>mdi-calendar</v-icon>Seguimiento</v-tab
                >
                <v-tab v-if="role == 1 || role == 5"
                    ><v-icon left>mdi-cash</v-icon>Resumen Pagos</v-tab
                >

                <!-- Pestañas -->
                <!-- Form Proyecto -->
                <v-tab-item class="pa-3 ma-1">
                    <v-card flat>
                        <v-row dense>
                            <v-text-field
                                dense
                                outlined
                                :error-messages="
                                    errors.errors['nombre']
                                        ? errors.errors['nombre'][0]
                                        : null
                                "
                                v-model="proyecto.nombre"
                                label="Nombre"
                            >
                            </v-text-field>
                        </v-row>
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
                                    v-model="proyecto.servicio_id"
                                    :items="servicios"
                                    item-text="nombre"
                                    item-value="id"
                                    label="Producto Contratado"
                                >
                                </v-select>
                            </v-col>
                            <v-col cols="12" md="6" class="pl-3 pt-0 pb-0">
                                <v-menu
                                    ref="menu2"
                                    v-model="menu2"
                                    :close-on-content-click="false"
                                    :return-value.sync="proyecto.fecha_alta"
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
                                            v-model="proyecto.fecha_alta"
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
                                        v-model="proyecto.fecha_alta"
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
                                                    proyecto.fecha_alta
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
                                    v-model="proyecto.detalle_servicio"
                                    :config="editorConfig"
                                ></ckeditor>
                            </v-col>
                        </v-row>
                        <v-row dense v-if="role == 1 || role == 5">
                            <v-col cols="12" md="3" class="mx-0 my-0 px-0 py-0">
                                <v-col cols="12" md="12">
                                    <v-select
                                        dense
                                        outlined
                                        :error-messages="
                                            errors.errors['estado_id']
                                                ? errors.errors['estado_id'][0]
                                                : null
                                        "
                                        v-model="proyecto.estado_id"
                                        :items="estados"
                                        item-text="nombre"
                                        item-value="id"
                                        label="Estado"
                                    >
                                    </v-select>
                                </v-col>
                            </v-col>
                            <v-col cols="12" md="2" class="mx-0 my-0 px-0 py-0">
                                <v-col cols="12" md="12">
                                    <v-text-field
                                        dense
                                        outlined
                                        :error-messages="
                                            errors.errors['pvp']
                                                ? errors.errors['pvp'][0]
                                                : null
                                        "
                                        v-model="proyecto.pvp"
                                        label="Precio Proyecto"
                                    >
                                    </v-text-field>
                                </v-col>
                            </v-col>
                            <v-col cols="12" md="2" class="mx-0 my-0 px-0 py-0">
                                <v-col cols="12" md="12">
                                    <v-text-field
                                        dense
                                        outlined
                                        v-model="proyecto.pvp_gasto"
                                        label="Gasto Externo"
                                    ></v-text-field>
                                </v-col>
                            </v-col>
                            <v-col cols="12" md="5" class="mx-0 my-0 px-0 py-0">
                                <v-col cols="12" md="12">
                                    <v-text-field
                                        dense
                                        outlined
                                        v-model="proyecto.porc_realizado"
                                        label="% Realizado"
                                    ></v-text-field>
                                </v-col>
                            </v-col>
                            <v-col cols="12" md="6" class="mx-0 my-0 px-0 py-0">
                                <v-col cols="12" md="12">
                                    <div style="display: flex">
                                        <v-text-field
                                            dense
                                            outlined
                                            v-model="proyecto.minutos_estimados"
                                            label="Minutos Estimados"
                                        ></v-text-field>
                                        <v-btn-toggle
                                            color="primary"
                                            shaped
                                            v-model="proyecto.semanal"
                                            background-color="white"
                                        >
                                            <v-btn :value="2" text>
                                                Mensual
                                            </v-btn>
                                            <v-btn :value="1" text>
                                                Semanal
                                            </v-btn>
                                            <v-btn :value="0" text>
                                                Total
                                            </v-btn>
                                        </v-btn-toggle>
                                    </div>
                                </v-col>
                            </v-col>
                            <v-col cols="12" md="6">
                                <v-btn-toggle
                                    color="primary"
                                    shaped
                                    v-model="proyecto.tipo_proyecto"
                                    background-color="white"
                                >
                                    <v-btn :value="1" text> Único </v-btn>
                                    <v-btn :value="2" text> Mensual </v-btn>
                                </v-btn-toggle>
                            </v-col>
                            <v-col cols="12" md="3">
                                <v-checkbox
                                    label="Bloquear Mail"
                                    v-model="proyecto.no_mail"
                                ></v-checkbox>
                            </v-col>
                            <v-col cols="12" md="3">
                                <v-checkbox
                                    label="¿Es Kit?"
                                    v-model="proyecto.es_kit"
                                ></v-checkbox>
                            </v-col>
                        </v-row>
                        <v-row dense v-if="role == 1 || role == 5">
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
                                    v-model="proyecto.detalles_gasto"
                                    :config="editorConfig"
                                ></ckeditor>
                            </v-col>
                        </v-row>
                    </v-card>
                </v-tab-item>
                <!-- Form Proyecto -->
                <!-- Form Cliente -->
                <v-tab-item class="pa-3 ma-1">
                    <form-usuario
                        :usuarios="usuarios"
                        :provincias="provincias"
                        :editorConfig="editorConfig"
                        :editor="editor"
                        :editorData="editorData"
                        :errors="errors"
                        :proyecto="proyecto"
                    >
                    </form-usuario>
                </v-tab-item>
                <!-- Form Cliente -->
                <!-- Form Estado del Proyecto -->
                <v-tab-item class="pa-3 ma-1">
                    <v-card flat>
                        <div class="font-weight-bold mb-3 black--text">
                            PROGRESO DEL PROYECTO
                        </div>
                        <v-row dense>
                            <v-col cols="12" md="6" class="mt-2">
                                <dynamic_select
                                    v-model="estado.id_estado"
                                    title="Estados"
                                    show="nombre"
                                    item-value="id"
                                    :estados="proyecto_estados"
                                    @delete="deleteProyectoEstado"
                                    @create="saveproyectoEstado"
                                    @clear="save_estado = {}"
                                    @update="saveproyectoEstado"
                                    @getEstado="
                                        (index) => {
                                            save_estado =
                                                proyecto_estados[index];
                                        }
                                    "
                                >
                                    <v-row>
                                        <v-col cols="12">
                                            <v-text-field
                                                label="Nombre"
                                                v-model="save_estado.nombre"
                                            ></v-text-field>
                                        </v-col>
                                    </v-row>
                                </dynamic_select>
                            </v-col>
                            <v-col cols="12" md="6">
                                <v-menu
                                    ref="menu3"
                                    v-model="menu3"
                                    :close-on-content-click="false"
                                    :return-value.sync="fecha"
                                    transition="scale-transition"
                                    :error-messages="
                                        errors.errors.fecha
                                            ? errors.errors.fecha[0]
                                            : null
                                    "
                                    offset-y
                                    min-width="290px"
                                >
                                    <template v-slot:activator="{ on, attrs }">
                                        <v-text-field
                                            v-model="fecha"
                                            label="Fecha"
                                            append-icon="mdi-calendar"
                                            v-bind="attrs"
                                            v-on="on"
                                        ></v-text-field>
                                    </template>
                                    <v-date-picker
                                        color="#1d2735"
                                        first-day-of-week="1"
                                        v-model="fecha"
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
                                            @click="$refs.menu3.save(fecha)"
                                            ><strong>OK</strong></v-btn
                                        >
                                    </v-date-picker>
                                </v-menu>
                            </v-col>
                            <v-col md="6">
                                <v-switch
                                    v-if="finalizado == false && descripcion"
                                    v-model="finalizado"
                                    label="En Progreso"
                                    color="light-green accent-4"
                                ></v-switch>
                                <v-switch
                                    v-if="finalizado == true && descripcion"
                                    v-model="finalizado"
                                    label="Finalizado"
                                    color="light-green accent-4"
                                ></v-switch>
                            </v-col>
                            <!--  -->
                            <v-col md="6">
                                <v-switch
                                    v-if="proyecto.activo == false"
                                    v-model="proyecto.activo"
                                    label="Proyecto Inactivo"
                                    color="light-green accent-4"
                                ></v-switch>
                                <v-switch
                                    v-if="proyecto.activo == true"
                                    v-model="proyecto.activo"
                                    label="Proyecto Activo"
                                    color="light-green accent-4"
                                ></v-switch>
                            </v-col>
                            <!--  -->
                        </v-row>
                        <div class="mb-5">
                            <v-btn
                                v-if="estado.id_estado"
                                @click="addStatus()"
                                color="primary"
                                class="white--text"
                                rounded
                                >Agregar progreso</v-btn
                            >
                        </div>
                        <v-data-table
                            :headers="headers"
                            :items="itemsEstado"
                            :items-per-page="5"
                            class="elevation-1"
                        >
                            <template v-slot:item.descripcion="{ item }">{{
                                item.estado == null
                                    ? item.descripcion
                                    : item.estado.nombre
                            }}</template>
                            <template v-slot:item.fecha="{ item }">{{
                                item.fecha.substr(0, 10)
                            }}</template>
                            <template v-slot:item.finalizado="{ item }">
                                <v-chip
                                    dense
                                    @click="changeFinalizado(item)"
                                    class="ma-2 white--text"
                                    :color="item.finalizado ? 'green' : 'red'"
                                    >{{
                                        item.finalizado
                                            ? "finalizado"
                                            : "en progreso"
                                    }}
                                </v-chip>
                            </template>
                            <template v-slot:item.action="{ item }">
                                <v-icon @click="editItem(item)" color="primary"
                                    >mdi-pencil</v-icon
                                >
                                <v-icon @click="deleteItem(item)" color="red"
                                    >mdi-delete</v-icon
                                >
                            </template>
                        </v-data-table>
                    </v-card>
                </v-tab-item>
                <!-- Form Estado del Proyecto -->
                <!-- Form Archivo -->
                <v-tab-item class="pa-3 ma-1">
                    <tab-archivo
                        tipo="potencial"
                        :item="proyecto"
                    ></tab-archivo>
                </v-tab-item>
                <!-- Form Archivo -->
                <!-- Form Usuarios Asignados -->
                <v-tab-item class="pa-3 ma-1">
                    <v-card flat>
                        <div class="font-weight-bold mb-3 black--text">
                            AGREGAR USUARIO A PROYECTO
                        </div>
                        <v-row dense>
                            <v-col cols="12" md="4">
                                <v-autocomplete
                                    dense
                                    outlined
                                    prepend-icon="mdi-account-search-outline"
                                    v-model="user_to_pick"
                                    :error-messages="
                                        errors.errors['empleado.nombre']
                                            ? errors.errors[
                                                  'enmpleado.nombre'
                                              ][0]
                                            : null
                                    "
                                    return-object
                                    :items="empleados"
                                    item-value="id"
                                    item-text="nombre"
                                    label="Seleccione o Cree empleado Nuevo"
                                >
                                </v-autocomplete>
                            </v-col>
                        </v-row>
                        <div class="mb-5">
                            <v-btn
                                v-if="user_to_pick"
                                @click="addUserList()"
                                color="primary"
                                class="white--text"
                                rounded
                                >Agregar Usuario</v-btn
                            >
                        </div>
                        <v-data-table
                            :headers="headers_usuarios"
                            :items="itemsUsuarios"
                            :items-per-page="5"
                            class="elevation-1"
                        >
                            <template v-slot:item.fecha="{ item }">{{
                                item.fecha.substr(0, 10)
                            }}</template>
                            <template v-slot:item.role="{ item }">
                                <template v-if="item.usuario != null">
                                    {{
                                        item.usuario.role == 1
                                            ? "Administrador"
                                            : item.usuario.role == 2
                                            ? "Cliente"
                                            : item.usuario.role == 3
                                            ? "Empleado"
                                            : "Potencial"
                                    }}
                                </template>
                                <template v-else> Eliminado </template>
                            </template>
                            <template v-slot:item.action="{ item }">
                                <v-icon
                                    @click="deleteItemUsers(item)"
                                    color="red"
                                    >mdi-delete</v-icon
                                >
                            </template>
                        </v-data-table>
                    </v-card>
                </v-tab-item>
                <!-- Form Usuarios Asignados -->
                <!-- Form Seguimiento -->
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
                                        <v-col cols="12">
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
                <!-- Form Seguimiento -->

                <!-- Resumen Pagos del Proyecto -->
                <v-tab-item class="pa-3 ma-1">
                    <v-card flat>
                        <div class="font-weight-bold mb-4 black--text">
                            FACTURAS DEL PROYECTO
                        </div>
                        <v-data-table
                            :headers="headers_proyectos"
                            :items="facturasProyectos"
                            :items-per-page="5"
                            class="elevation-4"
                        >
                            <template v-slot:item.fecha="{ item }">{{
                                item.fecha.substr(0, 10)
                            }}</template>
                            <template v-slot:item.total="{ item }"
                                >{{ item.total }} €</template
                            >
                            <template
                                v-slot:item.total_pagos_ingresos="{ item }"
                                >{{ item.total_pagos_ingresos }} €</template
                            >
                        </v-data-table>
                    </v-card>
                    <div class="font-weight-bold my-4 black--text">
                        RESUMEN CONTABLE DEL PROYECTO
                    </div>
                    <v-card class="px-2 py-2 my-4 elevation-4">
                        <span class="font-weight-bold"
                            >Total Proyecto: {{ proyecto.pvp }} €</span
                        ><br />
                        <span class="font-weight-bold"
                            >Total Pagado:
                            {{ this.total_pagos_ingresos }} €</span
                        ><br />
                        <span class="font-weight-bold"
                            >Total Pendiente: {{ this.total_pendiente }} €</span
                        >
                    </v-card>
                </v-tab-item>
                <!-- Resumen Pagos del Proyecto -->
            </v-tabs>
        </v-card>
        <!-- Botones Proyecto 1a linea -->
        <v-row class="mt-3" v-if="role == 1 || role == 5">
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
                <v-tooltip top>
                    <template v-slot:activator="{ on, attrs }">
                        <v-btn
                            fab
                            :to="{
                                path: `/registrr-facturas?cliente=${proyecto.usuario_id}`,
                            }"
                            :loading="isloading"
                            :disabled="isloading"
                            class="mx-2"
                            v-bind="attrs"
                            v-on="on"
                            readonly
                            style="background-color: #1d2735 !important"
                        >
                            <v-icon class="white--text">mdi-calculator</v-icon>
                        </v-btn>
                    </template>
                    <span>Crear Factura</span>
                </v-tooltip>
                <v-tooltip top>
                    <template v-slot:activator="{ on, attrs }">
                        <v-btn
                            fab
                            :to="{
                                path: `/registrr-facturas-pro?cliente=${proyecto.usuario_id}`,
                            }"
                            :loading="isloading"
                            :disabled="isloading"
                            class="mx-2"
                            v-bind="attrs"
                            v-on="on"
                            readonly
                            style="background-color: #1d2735 !important"
                        >
                            <v-icon class="white--text"
                                >mdi-calculator-variant</v-icon
                            >
                        </v-btn>
                    </template>
                    <span>Crear Factura Proforma</span>
                </v-tooltip>
                <v-tooltip top>
                    <template v-slot:activator="{ on, attrs }">
                        <v-btn
                            fab
                            @click="saveProyecto"
                            :loading="isloading"
                            :disabled="isloading"
                            class="mx-2"
                            v-bind="attrs"
                            v-on="on"
                            readonly
                            style="background-color: #1d2735 !important"
                        >
                            <v-icon class="white--text">mdi-file-pdf</v-icon>
                        </v-btn>
                    </template>
                    <span>Ver Facturas Enviadas</span>
                </v-tooltip>
                <v-tooltip top>
                    <template v-slot:activator="{ on, attrs }">
                        <v-btn
                            fab
                            @click="saveProyecto"
                            :loading="isloading"
                            :disabled="isloading"
                            color="amber accent-3"
                            class="mx-2"
                            v-bind="attrs"
                            v-on="on"
                            readonly
                        >
                            <v-icon class="white--text">mdi-at</v-icon>
                        </v-btn>
                    </template>
                    <span>Enviar Mail</span>
                </v-tooltip>
            </v-col>
        </v-row>
        <!-- Botones Proyecto 1a linea -->
        <!-- Botones Proyecto 2a linea -->
        <v-row class="mt-3">
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
                            @click="saveProyecto"
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
                    <span>Guardar Proyecto</span>
                </v-tooltip>
            </v-col>
        </v-row>
        <!-- Botones Proyecto 1a linea -->
        <WhatsAppDialog
            :name="proyecto.usuario.nombre"
            v-model="dialog_whatsapp"
            :to="proyecto.usuario.telefono"
        ></WhatsAppDialog>
    </v-container>
</template>
<script>
import WhatsAppDialog from "../../potenciales/componentes/WhatsAppDialog.vue";
import { provincias_mixin } from "../../../global_mixins/provincias_mixin";
import { servicios_mixin } from "../../../global_mixins/servicios_mixin";
import { estados_mixin } from "../../../global_mixins/estados_mixin";
import FileInput from "../../../global_components/FileInput.vue";
import VFileComponent from "../../../global_components/VFileComponent.vue";
import ClassicEditor from "@ckeditor/ckeditor5-build-classic";
import formUsuario from "./formUsuario";
import tabArchivo from "../../../global_components/tabArchivo";

export default {
    components: {
        "file-input": FileInput,
        VFileComponent,
        formUsuario,
        tabArchivo,
        WhatsAppDialog,
    },
    mixins: [provincias_mixin, servicios_mixin, estados_mixin],
    data() {
        return {
            save_estado: {},

            estado: { id_estado: null },
            dialog_whatsapp: false,
            proyecto_estados: [],
            seguimiento: {},
            Seguimientos: [],
            seguimientos_headers: [
                { text: "Nombre", value: "proyecto.nombre", sortable: false },
                { text: "comentario", value: "comentario", sortable: false },
                { text: "Fecha", value: "fecha", sortable: false },
                { text: "Acciones", value: "action", sortable: false },
            ],
            user_to_pick: null,
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
            menu3: false,
            menu3: "",
            menu4: false,
            menu4: "",
            proyecto: {
                id: "",
                pvp: "",
                semanal: 1,
                pvp_gasto: 0,
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
                    provincia_id: 35,
                    cuenta: "00000000000000000000",
                    fecha_alta: new Date().toISOString().substr(0, 10),
                    observaciones: null,
                    avatar: null,
                },
                estado_id: 2,
                servicio: {},
                fecha_alta: new Date().toISOString().substr(0, 10),
                detalle_servicio: null,
                detalles_gasto: null,
                nombre: "",
                archivos: [],
                porc_realizado: 0,
                activo: false,
            },
            clienteId: null,
            servicio: { id: 1 },
            estado: { id: 1 },
            descripcion: "",
            fecha: new Date().toISOString().substr(0, 10),
            estado_proyecto_id: "",
            id_items_proyecto: [],
            finalizado: false,
            total_pagos_ingresos: 0, //
            total_pendiente: 0, //
            headers: [
                { text: "Descripcion", value: "descripcion", sortable: false },
                { text: "Fecha", value: "fecha", sortable: false },
                { text: "Estado", value: "finalizado", sortable: false },
                { text: "Acciones", value: "action", sortable: false },
            ],
            headers_usuarios: [
                { text: "id", value: "id_usuario", sortable: false },
                { text: "Nombre", value: "usuario.nombre", sortable: false },
                {
                    text: "Nombre Fiscal",
                    value: "usuario.nombre_fiscal",
                    sortable: false,
                },
                { text: "Rol", value: "role", sortable: false },
                { text: "Acciones", value: "action", sortable: false },
            ],
            headers_proyectos: [
                {
                    text: "Num. Factura",
                    value: "nro_anio_factura",
                    sortable: false,
                },
                { text: "Fecha Factura", value: "fecha", sortable: false },
                { text: "Importe Factura", value: "total", sortable: false },
                {
                    text: "Ingresos Totales Factura",
                    value: "total_pagos_ingresos",
                    sortable: false,
                },
            ],
            roles: [
                { id: 1, role: "Administrador" },
                { id: 2, role: "Cliente" },
            ],
            usuarios: [],
            files: [],
            imagePreview: [],
            csrf: document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute("content"),
            user_id: localStorage.getItem("user_id"),
            base_64_image: null,
            items: [],
            itemsEstado: [],
            itemsUsuarios: [],
            empleados: [],
            ingresosProyecto: [],
            facturasProyectos: [], //
        };
    },
    created() {
        this.getUsuarios();
        this.getEmpleados();
        this.generateHoras();

        if (this.$route.query.id) {
            this.getProyectoById(this.$route.query.id);
        }
        this.clienteid = this.$route.query.clienteid;
        this.fillClienteId();
    },

    methods: {
        getEstados() {
            axios.get("api/get-proyecto-estados").then((res) => {
                this.proyecto_estados = res.data;
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
        getingresosByProyecto(proyecto_id) {
            this.total_pagos_ingresos = 0;
            this.total_pendiente = 0;
            axios.get(`api/get-ingreso-by-pro_id/${proyecto_id}`).then(
                (res) => {
                    this.ingresosProyectos = res.data;
                    this.total_pagos_ingresos = parseFloat(
                        this.ingresosProyectos.total_pagos_ingresos
                    ).toFixed(2);
                    this.total_pendiente = parseFloat(
                        this.proyecto.pvp -
                            this.ingresosProyectos.total_pagos_ingresos
                    ).toFixed(2);
                    this.getFacturasByProyecto(proyecto_id);
                },
                (err) => {
                    this.$toast.error(
                        "Error consultando Ingresos de proyectos"
                    );
                }
            );
        },
        getFacturasByProyecto(proyecto_id) {
            axios.get(`api/get-facturas-by-proyecto/${proyecto_id}`).then(
                (res) => {
                    this.facturasProyectos = res.data;
                    this.facturasProyectos[0].total_pagos_ingresos =
                        this.total_pagos_ingresos;
                },
                (err) => {
                    this.$toast.error("Error consultando Facturas");
                }
            );
        },
        //adaptamos el getProyectoById para traernos los items de estados en la edición
        async getProyectoById(proyecto_id) {
            try {
                const response = await axios.get(
                    `api/get-proyecto-by-id/${proyecto_id}`
                );
                this.proyecto = response.data;
                console.log(this.proyecto);
                this.itemsEstado = response.data.estados_proyecto;
                this.itemsUsuarios = response.data.usuarios ?? [];
                this.pushItemsEstadoId(this.itemsEstado);
                this.getingresosByProyecto(proyecto_id);
                this.getSeguimientos();
            } catch (error) {
                console.log(error);
            }
        },
        getSeguimientos() {
            const self = this;
            axios
                .get(`api/get-tareas-proyecto?cliente=${this.proyecto.id}`)
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
            console.log(this.seguimiento);
            this.seguimiento.id_proyecto = this.$route.query.id;
            axios
                .post("api/save-tareas-proyecto", this.seguimiento)
                .then(function (response) {
                    self.seguimiento = {};
                    self.getSeguimientos();
                });
        },
        fillClienteId() {
            if (this.$route.query.clienteid) {
                axios
                    .get(`api/get-usuario-by-id/${this.clienteid}`)
                    .then((res) => {
                        this.proyecto.usuario = res.data.user;
                    });
            }
        },
        deleteFile(item) {
            const index = this.items.findIndex((val) => val.name == item.name);
            if (index > -1) {
                this.items.splice(index, 1);
            }
        },
        pushItemsEstadoId(items) {
            const itemsPreview = items;
            Object.keys(itemsPreview).forEach((i) => {
                this.id_items_proyecto.push(itemsPreview[i].id);
            });
        },
        async saveProyecto() {
            try {
                if (this.proyecto.activo) {
                    this.proyecto.activo = true;
                } else {
                    this.proyecto.activo = false;
                }

                var formData = new FormData();
                formData.append("proyecto", JSON.stringify(this.proyecto));
                formData.append(
                    "itemsEstado",
                    JSON.stringify(this.itemsEstado)
                );
                formData.append(
                    "idItemsEstado",
                    JSON.stringify(this.id_items_proyecto)
                );
                formData.append("usuarios", JSON.stringify(this.itemsUsuarios));

                let archivos = this.proyecto.archivos.filter(
                    (archivo) => !archivo.id
                );
                archivos.forEach((item, i) =>
                    formData.append("itemsFiles[" + i + "]", item.file)
                );
                let res = await axios.post("api/save-proyecto", formData);
                this.proyecto = res.data.proyecto;
                this.$toast.sucs("Proyecto guardado con exito");
            } catch (error) {
                this.$toast.error(
                    "Error guardando Proyecto, Compruebe todos los campos requeridos"
                );
            }
        },
        volver() {
            // volver al crear
            let tipo = "";
            if (
                this.$route.query.tipo != null &&
                this.$route.query.tipo != "null"
            ) {
                tipo = "&tipo=" + this.$route.query.tipo;
            }
            if (this.clienteid) {
                this.$router.push(
                    `/${this.$route.meta.to}?id=${this.clienteid}${tipo}`
                );
            }
            // volver al editar
            else {
                this.$router.push(
                    `/${this.$route.meta.to}?id=${this.proyecto.usuario.id}${tipo}`
                );
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
        setFiles(files) {
            const filesPreview = files;
            Object.keys(filesPreview).forEach((i) => {
                const file = filesPreview[i];
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.imagePreview.push(reader.result);
                };
                this.imagePreview = [];
                reader.readAsDataURL(file);
            });
            if (files !== undefined) {
                this.files = files;
                this.disableUploadButtonImage = false;
            }
        },
        getMethodsForm() {
            axios.get(`api/get-methods-form`).then(
                (res) => {
                    this.provincias = res.data.provincias;
                },
                (res) => {
                    this.$toast.error("Error consultando Usuario");
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
        getEmpleados() {
            axios.post(`api/get-usuarios-empleados`, this.filtros).then(
                (res) => {
                    this.empleados = res.data.users.data;
                    this.empleados.unshift("");
                },
                (err) => {
                    this.$toast.error("Error consultando empleados");
                }
            );
        },
        addUserList() {
            // this.getEmpleados();
            // this.user_to_pick = this.empleados;
            if (this.user_to_pick != null) {
                // console.log(this.user_to_pick);
                let finusuraio = this.itemsUsuarios.find(
                    (element) => element.id_usuario == this.user_to_pick.id
                );
                if (finusuraio == null) {
                    let elemento = {
                        id_usuario: this.user_to_pick.id,
                        usuario: this.user_to_pick,
                    };

                    this.itemsUsuarios.push(elemento);
                }
            }
            this.user_to_pick = null;
        },
        deleteProyectoEstado(id) {
            axios
                .post("api/delete-proyecto-estados", { id: id })
                .then((res) => {
                    this.getEstados();
                });
        },
        saveproyectoEstado() {
            axios
                .post("api/save-proyecto-estados", this.save_estado)
                .then((res) => {
                    this.getEstados();
                    this.save_estado = {};
                });
        },
        addStatus() {
            if (this.estado.id_estado != "") {
                let str = this.proyecto_estados.find(
                    (element) => element.id == this.estado.id_estado
                );
                const estado = {
                    estado: str,
                    id_estado: this.estado.id_estado,
                    fecha: this.fecha,
                    finalizado: this.finalizado,
                    id:
                        this.estado_proyecto_id != ""
                            ? this.estado_proyecto_id
                            : Date.now() + "a",
                };
                let idstring = estado.id.toString();
                if (idstring.includes("a")) {
                    this.itemsEstado.push(estado);
                    this.id_items_proyecto.push(estado.id);
                } else {
                    const search = this.itemsEstado.findIndex(
                        (val) => (val.id = estado.id)
                    );
                    this.itemsEstado[search].descripcion = estado.descripcion;
                    this.itemsEstado[search].fecha = estado.fecha;
                    this.itemsEstado[search].finalizado = estado.finalizado;
                }
            }
            this.estado_proyecto_id = "";
            this.descripcion = "";
            this.finalizado = false;
            this.fecha = new Date().toISOString().substr(0, 10);
        },
        changeFinalizado(itemE) {
            axios.get(`api/update-project-status/${itemE.id}`).then(
                (res) => {
                    itemE.finalizado = !itemE.finalizado;
                    this.$toast.sucs("Estado actualizado");
                },
                (err) => {
                    this.$toast.error(
                        "Debe guardar/actualizar antes de poder cambiar el estado"
                    );
                }
            );
        },
        deleteItem(item) {
            let idstring = item.id.toString();
            if (idstring.includes("a")) {
                const search = this.itemsEstado.findIndex(
                    (val) => val.id === idstring
                );
                if (search > -1) {
                    this.itemsEstado.splice(search, 1);
                    this.id_items_proyecto.splice(search, 1);
                }
            } else {
                const search = this.itemsEstado.findIndex(
                    (val) => val.id == item.id
                );
                if (search > -1) {
                    this.itemsEstado.splice(search, 1);
                    this.id_items_proyecto.splice(search, 1);
                }
                axios.get(`api/delete-proyecto-estado/${item.id}`).then(
                    (res) => {
                        this.$toast.sucs(
                            "Estado existente borrado exitosamente"
                        );
                    },
                    (err) => {
                        this.$toast.error("Error al borrar estado");
                    }
                );
            }
        },
        deleteItemUsers(item) {
            console.log(item);
            const search = this.itemsUsuarios.findIndex(
                (val) => val.id_usuario === item.id_usuario
            );
            if (search > -1) {
                this.itemsUsuarios.splice(search, 1);
            }
        },
        editItem(item) {
            this.estado_edit.index = this.itemsEstado.indexOf(item);
            this.estado_edit.id = item.id;
            this.estado_edit.descripcion = item.descripcion;
            this.estado_edit.fecha = item.fecha.substr(0, 10);
            this.estado_edit.finalizado = item.finalizado;
            this.dialog_estado = 1;
        },
        closeDialog() {
            this.dialog_estado = 0;
        },
        editElement() {
            let elemento = this.itemsEstado[this.estado_edit.index];
            elemento.descripcion = this.estado_edit.descripcion;
            elemento.fecha = this.estado_edit.fecha;
            elemento.finalizado = this.estado_edit.finalizado;
            this.closeDialog();
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
