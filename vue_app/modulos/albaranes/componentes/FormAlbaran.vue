<template>

    <v-container>

        <v-custom-title text="Guardar / Editar Albaran"></v-custom-title>

        <loader v-if="isloading"></loader>

        <v-form class="mt-5">

            <v-row dense>
                <v-col cols="12" md="2">
                    <v-menu ref="menu" v-model="menu" :close-on-content-click="false" :return-value.sync="albaran.fecha" transition="scale-transition" offset-y min-width="290px">

                        <template v-slot:activator="{ on, attrs }">
                            <v-text-field filled :error-messages="errors.errors.fecha ? errors.errors.fecha[0] : null" v-model="albaran.fecha" label="Fecha" append-icon="mdi-calendar" readonly v-bind="attrs" v-on="on">
                            </v-text-field>
                        </template>

                        <v-date-picker v-model="albaran.fecha" no-title scrollable>
                            <v-spacer></v-spacer>

                            <v-btn text color="primary" @click="menu = false">
                                Cancel
                            </v-btn>

                            <v-btn text color="primary" @click="$refs.menu.save(albaran.fecha)">
                                OK
                            </v-btn>
                        </v-date-picker>

                    </v-menu>
                </v-col>

                <v-col cols="12" md="3">
                    <v-select filled 
                    v-model="albaran.proveedor_id" 
                    :error-messages="errors.errors.proveedor_id ? errors.errors.proveedor_id[0] : null" 
                    :items="proveedores" 
                    item-text="nombre" 
                    item-value="id" 
                    label="Proveedor"></v-select>
                </v-col>

            </v-row>

            <v-row>
                <v-col cols="12" md="5">
                    <v-text-field filled v-model="albaran.descripcion" label="Descripción"></v-text-field>
                </v-col>
            </v-row>

            <v-row>
                <v-col cols="12" md="5">
                    <v-file-input filled prepend-icon="mdi-file-image" ref="file" accept="image/*" label="Imagen" v-on:change="handleFileUpload"></v-file-input>
                </v-col>
            </v-row>

            <v-row v-if="albaran.id != null">
                <v-col cols="12" md="5">
                    <v-text-field filled readonly :value="albaran.imagen_name" label="Nombre Imagen"></v-text-field>
                </v-col>
            </v-row>

            <v-row>
                <v-col v-if="isloading" cols="12" md="5">
                    <v-progress-linear v-model="uploadPercentage" height="25">
                        <strong>{{ Math.ceil(uploadPercentage) }}%</strong>
                    </v-progress-linear>
                </v-col>
            </v-row>

            <v-row class="mt-3">
                <v-col cols="12">
                    <v-btn rounded depressed @click="saveAlbaran" :disabled="isloading" color="success" class="white--text">Guardar</v-btn>
                </v-col>
            </v-row>

        </v-form>

    </v-container>

</template>

<script>
    export default {
        data() {
            return {
                menu: false,
                uploadPercentage: 0,
                proveedores: [],
                albaran: {
                    id: null,
                    fecha: new Date().toISOString().substr(0, 10),
                    descripcion: '',
                    proveedor_id: '',
                    imagen: '',
                    path: '',
                    imagen_name: '',
                    user_id: localStorage.getItem('user_id')
                },
            }
        },

        created() {
            if (this.$route.query.id) {
                this.getAlbaranById(this.$route.query.id)
            }
            this.getProveedores()
        },

        methods: {
            getProveedores() {
                
                axios.get(`api/get-proveedores/`+ this.albaran.user_id).then(res => {
                    this.proveedores = res.data
                }, res => {
                    this.$toast.error('Error consultando Proveedores')
                })
            },

            getAlbaranById(albaran_id) {
                axios.get(`api/get-albaran-by-id/${albaran_id}`).then(res => {
                    this.albaran = res.data.albaran
                     this.proveedores = res.data.proveedores
                }, res => {
                    this.$toast.error('Error consultando albaran')
                })
            },

            saveAlbaran() {
                let formData = new FormData();

                formData.append('id', this.albaran.id)
                formData.append('user_id', this.albaran.user_id)
                formData.append('fecha', this.albaran.fecha)
                formData.append('descripcion', this.albaran.descripcion)
                formData.append('proveedor_id', this.albaran.proveedor_id)
                formData.append('imagen', this.albaran.imagen)

                axios.post('api/save-albaran', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    },
                    onUploadProgress: function(progressEvent) {
                        this.uploadPercentage = parseInt(Math.round((progressEvent.loaded / progressEvent.total) * 100))
                    }.bind(this)
                }).then(res => {
                    this.$toast.sucs('Albaran guardado con exito')
                    this.$router.push('/lista-albaranes')
                }, res => {
                    this.$toast.error('Error guardando albaran')
                })
            },
            handleFileUpload(file) {
                this.albaran.imagen = file
            },
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