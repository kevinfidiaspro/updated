<template>
    <v-container>
        <loader v-if="isloading"></loader>
        <v-form>
            <v-card flat>
                <v-toolbar flat color="#1d2735" dark>
                    <v-toolbar-title>Guardar / Editar Promoción</v-toolbar-title>
                </v-toolbar>
                <v-card-text>
                        <v-row dense>
                            <v-col cols="12" md="4" class="pt-3 pl-0 pb-0">
                                <v-text-field dense outlined :error-messages="errors.errors.nombre ? errors.errors.nombre[0] : null" v-model="promocion.promocion.nombre" label="Nombre de la promoción" required></v-text-field>
                            </v-col>
                            <v-col cols="12" md="4" class="pt-3 pl-0 pb-0">
                                <v-text-field dense outlined disabled v-model="promocion.promocion.promocion_name" label="Nombre Archivo" required></v-text-field>
                            </v-col> 
                        </v-row>
                        <v-col v-if="isloading" cols="12" md="4" class="pt-3 pl-0 pb-0" offset="1">
                            <v-progress-linear v-model="uploadPercentage" height="25" class="pa-1 ma-0">
                                <strong>{{ Math.ceil(uploadPercentage) }} %</strong>
                            </v-progress-linear>
                        </v-col>
                        <v-row dense>
                            <v-col cols="12" md="3" class="pt-3 pl-0 pb-0">
                                <h4 class="ml-2"><small><strong>Vista Previa</strong></small></h4>                                 
                                <source :src="promocion.promocion.path" type="image">
                                <img dense :src="promocion.promocion.path" style="width: 250px; height: 150px" label="Vista Previa">
                            </v-col>
                            <v-col cols="12" md="4" class="pt-3 pl-0 pb-0 mt-4">
                                <v-file-input dense filled prepend-icon="mdi-file-image" ref="file" accept="" label="Archivo" v-on:change="handleFileUpload"></v-file-input>
                            </v-col>
                            <v-col cols="12" md="4" class="pt-3 pl-0 pb-0 ml-2 mt-4">
                                <v-checkbox
                                    dense
                                    label="Activo"
                                    color="primary"
                                    v-model="promocion.promocion.active"
                                ></v-checkbox>
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
                                @click="$router.push('/lista-promociones')"
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
                                @click="guardarPromocion"
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
                        <span>Guardar Promoción</span>
                    </v-tooltip>
                </v-col>
            </v-row>
        </v-form>
    </v-container>
</template>

<script>
    import FileInput from '../../../global_components/FileInput.vue'
    import VFileComponent from '../../../global_components/VFileComponent.vue';
    export default {
        components: { 'file-input' : FileInput, VFileComponent },
        data() {
            return {
                promocion: {
                    promocion: {
                        id: null,
                        nombre: null,
                        active: false,
                        file_name: null,
                        promocion_name: null,
                        path: null,
                    },
                },
                imagePreview: [],
                uploadPercentage: 0
            }
        },
        created() {
            if (this.$route.query.id) { this.getPromocionesById(this.$route.query.id)}
        },
        methods: {
            getPromocionesById(promocion_id) {
                axios.get(`api/get-promocion-by-id/${promocion_id}`).then(res => {
                    this.promocion = res.data
                }, res => {
                    this.$toast.error('Algo ha salido mal')
                })
            },
            handleFileUpload(file) {
                this.promocion.promocion.file_name = file
                this.promocion.promocion.promocion_name = file.name
                this.createImage(file)
            },
            createImage: function(file) {
                let reader = new FileReader()
                reader.onload = (file) => {
                    this.imagePreview.push(reader.result)
                    this.promocion.promocion.path = this.imagePreview[0]
                };
                reader.readAsDataURL(file)
                this.imagePreview = []
            },
            guardarPromocion() {
                let formData = new FormData();
                formData.append('id', this.promocion.promocion.id)
                formData.append('nombre', this.promocion.promocion.nombre)
                formData.append('file_name', this.promocion.promocion.file_name)
                formData.append('promocion_name', this.promocion.promocion.promocion_name)
                formData.append('path', this.promocion.promocion.path)
                formData.append('active', this.promocion.promocion.active)
                axios.post('api/save-promocion',
                    formData, {
                        headers: {'Content-Type': 'multipart/form-data'},
                        onUploadProgress: function(progressEvent) {
                            this.uploadPercentage = parseInt(Math.round((progressEvent.loaded / progressEvent.total) * 100))
                         }.bind(this)
                    }
                ).then(res => {
                    this.$toast.sucs('Promocion guardada');
                    this.$router.push('/lista-promociones')
                }, res => {
                    this.$toast.error('Algo ha salido mal')
                })
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
