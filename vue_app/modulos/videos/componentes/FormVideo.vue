<template>

    <v-container>

        <v-custom-title text="Guardar / Editar Video"></v-custom-title>

        <loader v-if="isloading"></loader>

        <br>
        <router-link :to="{ path: `/lista-videos` }">
            volver
        </router-link>
        <br>

        <v-form>

            <v-row>
                <v-col cols="12" md="4">
                    <v-text-field filled :error-messages="errors.errors.nombre ? errors.errors.nombre[0] : null" v-model="video.nombre" label="Nombre" required></v-text-field>
                </v-col>
            </v-row>

            <v-row v-if="video.id">
                <v-col cols="12" md="4">
                    <v-text-field filled disabled v-model="video.video_name" label="Nombre Archivo" required></v-text-field>
                </v-col>
            </v-row>

            <v-row dense>
                <v-col cols="12" md="4">
                    <v-file-input filled prepend-icon="mdi-camera" ref="file" accept="video/*" label="Archivo" v-on:change="handleFileUpload"></v-file-input>
                </v-col>
            </v-row>

            <v-row>
                <v-col cols="12" md="4">
                    <v-checkbox color="green" v-model="video.active" label="activo"></v-checkbox>
                </v-col>
            </v-row>

            <v-row>
                <v-col v-if="isloading" cols="12" md="4">
                    <v-progress-linear v-model="uploadPercentage" height="25">
                        <strong>{{ Math.ceil(uploadPercentage) }}%</strong>
                    </v-progress-linear>
                </v-col>
            </v-row>

            <v-row>
                <v-col cols="12">
                    <v-btn rounded depressed @click="guardarVideo" :disabled="isloading" color="success" class="white--text">Guardar</v-btn>
                </v-col>
            </v-row>

        </v-form>

    </v-container>

</template>

<script>
    import {
        videoPlayer
    } from 'vue-md-player'
    import 'vue-md-player/dist/vue-md-player.css'
    export default {
        components: {
            videoPlayer
        },
        data() {
            return {
                video: {
                    id: null,
                    nombre: '',
                    active: true,
                    file_name: '',
                },
                uploadPercentage: 0
            }
        },
        created() {
            if (this.$route.query.id) {
                this.getVideoById(this.$route.query.id)
            }
        },
        methods: {
            getVideoById(video_id) {
                axios.get(`api/get-video-by-id/${video_id}`).then(res => {
                    this.video = res.data
                }, res => {
                    this.$toast.error('Algo ha salido mal')
                })
            },

            handleFileUpload(file) {
                this.video.file_name = file
            },

            guardarVideo() {

                let formData = new FormData();

                formData.append('id', this.video.id)
                formData.append('nombre', this.video.nombre)
                formData.append('file_name', this.video.file_name)
                formData.append('active', this.video.active)

                axios.post('api/save-video',
                    formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        },
                        onUploadProgress: function(progressEvent) {
                            this.uploadPercentage = parseInt(Math.round((progressEvent.loaded / progressEvent.total) * 100))
                        }.bind(this)
                    }
                ).then(res => {
                    this.$router.push('/lista-videos')
                }, res => {
                    this.$toast.error('Algo ha salido mal')
                })

            }
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