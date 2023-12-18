<template>
    <v-container>
        <loader v-if="isloading"></loader>
        <v-card>
            <v-toolbar flat color="#1d2735" dark>
                <v-toolbar-title>Guardar / Editar Gasto</v-toolbar-title>
            </v-toolbar>
            <v-card-text>
                <v-row dense>
                    <v-col cols="12" md="3">
                        <v-autocomplete
                            outlined
                            prepend-icon="mdi-account-search-outline"
                            v-model="gasto.proyecto_id"
                            :error-messages="
                            errors.errors['gasto.proyecto_id'] ? errors.errors['gasto.proyecto_id'][0] : null"
                            :items="proyectos"
                            item-value="id"
                            item-text="nombre"
                            label="Seleccione un proyecto"
                        >
                        </v-autocomplete>
                        <!--<v-text-field class="my-input" filled :error-messages="errors.errors.codigo ? errors.errors.codigo[0] : null" v-model="gasto.codigo" label="Codigo" required></v-text-field>-->
                    </v-col>
                    <v-col cols="12" md="2">
                        <v-text-field filled
                            :error-messages="errors.errors.importe ? errors.errors.importe[0] : null"
                            v-model="gasto.importe"
                            @change="validaImporte"
                            label="Importe"
                            required
                            prefix=""></v-text-field>
                        <small v-if="errorImporte">
                            {{errorImporte}}
                        </small>
                    </v-col>
                    <v-col cols="12" md="3">
                        <v-select filled :items="tipos" item-value="id" item-text="nombre" v-model="gasto.tipo_gasto_id" label="Tipo"></v-select>
                    </v-col>
                </v-row>
                <v-row>
                    <v-col cols="12" md="5">
                        <v-menu ref="menu" v-model="menu" :close-on-content-click="false" :return-value.sync="gasto.fecha" transition="scale-transition" offset-y min-width="290px">
                            <template v-slot:activator="{ on, attrs }">
                                <v-text-field filled :error-messages="errors.errors.fecha ? errors.errors.fecha[0] : null" v-model="gasto.fecha" label="Fecha" append-icon="mdi-calendar" readonly v-bind="attrs" v-on="on">
                                </v-text-field>
                            </template>
                            <v-date-picker v-model="gasto.fecha" no-title scrollable>
                                <v-spacer></v-spacer>
                                <v-btn text color="primary" @click="menu = false">
                                    Cancel
                                </v-btn>
                                <v-btn text color="primary" @click="$refs.menu.save(gasto.fecha)">
                                    OK
                                </v-btn>
                            </v-date-picker>
                        </v-menu>
                    </v-col>
                    <v-col cols="12" md="5">
                        <v-file-input filled prepend-icon="mdi-file-image" ref="file" accept="" label="Archivo" v-on:change="handleFileUpload" ></v-file-input>
                        <v-col v-if="isloading" cols="12" md="5">
                            <v-progress-linear v-model="uploadPercentage" height="25">
                                <strong>{{ Math.ceil(uploadPercentage) }}%</strong>
                            </v-progress-linear>
                        </v-col>
                    </v-col>
                </v-row>
                <!--<v-row class="mb-3" dense>
                    <a>
                        {{ fileName == '' ? gasto.archivo : fileName}}
                    </a>
                </v-row>-->
                <v-row dense>
                    <v-col cols="12" md="5">
                        <v-textarea v-model="gasto.descripcion" label="Descripción"></v-textarea>
                    </v-col>
                </v-row>
            </v-card-text>
        </v-card>
        <v-row class="mt-3">
            <!-- Botones Navegacion -->
            <v-col cols="12">
                <v-tooltip top>
                    <template v-slot:activator="{ on, attrs }">
                        <v-btn
                            fab
                            @click="$router.push('/lista-gastos')"
                            :loading="isloading"
                            :disabled="isloading"
                            color="blue"
                            class="mx-2"
                            v-bind="attrs"
                            v-on="on"
                        >
                            <v-icon class="white--text">mdi-arrow-left-bold-outline</v-icon>
                        </v-btn>
                    </template>
                    <span>Volver</span>
                </v-tooltip>
                <v-tooltip top>
                    <template v-slot:activator="{ on, attrs }">
                        <v-btn
                            fab
                            @click="updateGasto"
                            :loading="isloading"
                            :disabled="isloading"
                            color="success"
                            class="mx-2"
                            v-bind="attrs"
                            v-on="on"
                        >
                            <v-icon class="white--text">mdi-content-save-all</v-icon>
                        </v-btn>
                    </template>
                    <span>Actualizar Gasto</span>
                </v-tooltip>
            </v-col>
        </v-row>
        <!--<v-custom-title text="Guardar Gasto"></v-custom-title>
        <v-form class="mt-5">

            <v-row dense>
                <v-col cols="12" md="3">
                    <v-text-field class="my-input" filled :error-messages="errors.errors.codigo ? errors.errors.codigo[0] : null" v-model="gasto.codigo" label="Codigo" required></v-text-field>
                </v-col>

                <v-col cols="12" md="2">
                    <v-text-field filled

                        :error-messages="errors.errors.importe ? errors.errors.importe[0] : null"
                        v-model="gasto.importe"
                        @change="validaImporte"
                        label="Importe"
                        required
                        prefix=""></v-text-field>

                        <small v-if="errorImporte">
                            {{errorImporte}}
                        </small>
                </v-col>

                 <v-col cols="12" md="3">
                    <v-select filled :items="tipos" item-value="id" item-text="nombre" v-model="gasto.tipo_gasto_id" label="Tipo"></v-select>
                </v-col>
            </v-row>

            <v-row>

                <v-col cols="12" md="5">
                     <v-menu ref="menu" v-model="menu" :close-on-content-click="false" :return-value.sync="gasto.fecha" transition="scale-transition" offset-y min-width="290px">

                        <template v-slot:activator="{ on, attrs }">
                            <v-text-field filled :error-messages="errors.errors.fecha ? errors.errors.fecha[0] : null" v-model="gasto.fecha" label="Fecha" append-icon="mdi-calendar" readonly v-bind="attrs" v-on="on">
                            </v-text-field>
                        </template>

                        <v-date-picker v-model="gasto.fecha" no-title scrollable>
                            <v-spacer></v-spacer>

                            <v-btn text color="primary" @click="menu = false">
                                Cancel
                            </v-btn>

                            <v-btn text color="primary" @click="$refs.menu.save(gasto.fecha)">
                                OK
                            </v-btn>
                        </v-date-picker>

                    </v-menu>
                </v-col>

                <v-col cols="12" md="5">
                    <v-file-input filled prepend-icon="mdi-file-image" ref="file" accept="" label="Archivo" v-on:change="handleFileUpload" ></v-file-input>

                     <v-col v-if="isloading" cols="12" md="5">
                        <v-progress-linear v-model="uploadPercentage" height="25">
                            <strong>{{ Math.ceil(uploadPercentage) }}%</strong>
                        </v-progress-linear>
                    </v-col>
                </v-col>
            </v-row>

            <v-row class="mb-3" dense>
                <a >
                    {{ fileName == '' ? gasto.archivo : fileName}}
                </a>
            </v-row>

            <v-row dense>
                <v-col cols="12" md="5">
                    <v-textarea v-model="gasto.descripcion" label="Descripción"></v-textarea>
                </v-col>
            </v-row>

            <v-row class="mt-3">
                <v-col cols="12" md="6">
                    <v-btn rounded depressed @click="updateGasto" :disabled="isloading" color="success" class="white--text">Actualizar</v-btn>

                     <router-link :to="{ path: `/lista-gastos` }" class="action-buttons">

                          <v-btn rounded depressed  color="warning" class="white--text">Volver</v-btn>
                     </router-link>
                </v-col>


            </v-row>

        </v-form>-->
    </v-container>
</template>

<script>
    export default {
        data() {
            return {
                menu: false,
                tipos: [],
                proyectos: [],
                menu: false,
                uploadPercentage: 0,
                gasto: {
                    id: '',
                    codigo: '',
                    fecha: new Date().toISOString().substr(0, 10),
                    importe: '',
                    descripcion: '',
                    archivo: null,
                    nombre_archivo: null,
                    path: null,
                    tipo_gasto_id: '',
                    user_id: localStorage.getItem('user_id'),
                    proyecto_id: '',
                },

                errorImporte : '',
                fileName : ''
            }
        },

        created() {
            this.getGastoById()
            this.getTiposGasto()
            this.getAllProyectos()
        },

        methods: {
            getTiposGasto(){
                axios.get(`api/get-tipos-gasto/${localStorage.getItem('user_id')}`).then(res => {
                    this.tipos = res.data

                }, res => {
                    this.$toast.error('Error consultando tipos de gasto')
                })
            },
            getAllProyectos() {
                axios.get(`api/get-all-proyectos`).then(
                    (res) => {
                        this.proyectos = res.data;
                    },
                    (res) => {
                        this.$toast.error("Error consultando proyectos");
                    }
                );
            },
            getGastoById() {
                axios.get(`api/get-gasto-by-id/` + this.captureGstoId).then(res => {
                    this.gasto = res.data.gasto
                    this.gasto.fecha = this.formatDate(res.data.gasto.fecha)
                }, res => {
                    this.$toast.error('Error consultando Gasto')
                })
            },

            updateGasto() {
                let formData = new FormData();
                formData.append('id', this.gasto.id)
                formData.append('codigo', this.gasto.codigo)
                formData.append('importe', this.gasto.importe)
                formData.append('descripcion', this.gasto.descripcion)
                formData.append('imagen[]', this.gasto.archivo)
                formData.append('nombreArchivo', this.fileName)
                formData.append('tipo_gasto_id', this.gasto.tipo_gasto_id)
                formData.append('fecha', this.gasto.fecha)
                formData.append('user_id', this.gasto.user_id)
                formData.append('proyecto_id', this.gasto.proyecto_id)

                axios.post('api/update-gasto', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    },
                    onUploadProgress: function(progressEvent) {
                        this.uploadPercentage = parseInt(Math.round((progressEvent.loaded / progressEvent.total) * 100))
                    }.bind(this)}).then(res => {
                        this.$toast.sucs('Gasto actualizado')
                        this.$router.push('/lista-gastos')
                    }, res => {
                        this.$toast.error('Error actualizado  Gasto')
                    })
            },
            handleFileUpload(file) {

                this.fileName = file.name
                this.gasto.archivo = file
            },



            validaImporte(valor) {
              this.errorImporte=''
              //// garces// console.log (valor)
              var RE = /^\d*\.?\d*$/;
              if (RE.test(valor)) {
                   this.errorImporte = ''
              } else {
                  this.errorImporte = 'Inserte un numero entero o decimal'
                  this.gasto.importe= ''
              }

              if (this.gasto.importe.length>10) {
                // alert('El numero es mu grande')
                this.errorImporte = 'Inserte un valor de importe válido'
              }

            },
            formatDate(date) {
                if (!date) return null;
                const newDate = date.substr(0, 10)
                return newDate
                /*const [year, month, day] = newDate.split("-");
                return `${day}/${month}/${year}`;*/
            },
        },
        computed: {

            isloading() {
                return this.$store.getters.getloading
            },

            errors() {
                return this.$store.getters.geterrors
            },
            captureGstoId(){
                let gastoId  = window.location.hash

                let splitGastoid = gastoId.split('/')
                return splitGastoid[splitGastoid.length-1]

            },
        }
    };
</script>

<style media="screen">
    .my-input input {
        text-transform: uppercase;
    }
</style>
