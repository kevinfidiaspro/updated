<template>
    <div class="background my-container">

        <v-custom-title text="Lista Videos"></v-custom-title>

        <v-btn v-if="user.role == 1" rounded depressed color="blue" class="mt-5 white--text" to="guardar-video">nuevo</v-btn>

        <loader v-if="isloading"></loader>

        <v-row>
            <v-col cols="12" md="4">
                <v-text-field prepend-icon="mdi-account-search" v-model="search" label="busqueda"></v-text-field>
            </v-col>
        </v-row>

        <v-data-table :headers="headers" :search="search" disable-pagination hide-default-footer :items="videos" item-key="id" class="elevation-1">

            <template v-slot:item.id="{ item }">
                <video class="mt-2" height="80" controls>
                    <source :src="item.path" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            </template>

            <template v-slot:item.active="{ item }">
                <v-chip v-if="user.role == 1" @click="changeActive(item)" class="ma-2 white--text" :color="item.active ? 'green' : 'red'">
                    {{ item.active ? 'activo' : 'inactivo' }}
                </v-chip>
                <v-chip v-if="user.role == 2" class="ma-2 white--text" :color="item.active ? 'green' : 'red'">
                    {{ item.active ? 'activo' : 'inactivo' }}
                </v-chip>
            </template>

            <template v-slot:item.action="{ item }">
                <router-link v-if="user.role == 1" :to="{ path: `/guardar-video?id=${item.id}` }" class="action-buttons">
                    <v-icon small class="mr-2" color="blue">
                        mdi-pencil
                    </v-icon>
                </router-link>

                <v-icon v-if="user.role == 1 || user.role == 2" @click="deleteVideo(item)" small class="mr-2" color="red">
                    mdi-trash-can
                </v-icon>
            </template>

        </v-data-table>

    </div>

</template>

<script>
    export default {
        data() {
            return {
                search: '',
                videos: [],
                headers: [{
                        text: 'Video',
                        align: 'left',
                        value: 'id',
                    }, {
                        text: 'Nombre',
                        value: 'nombre',
                    },
                    {
                        text: 'Activo',
                        align: 'center',
                        value: 'active'
                    },
                    {
                        text: 'Acciones',
                        value: 'action',
                        sortable: false
                    },
                ],
            }
        },
        mounted() {
            this.getAllVideos()
        },
        methods: {
            getAllVideos() {
                axios.get('api/get-all-videos').then(res => {
                    this.videos = res.data
                }, err => {
                    this.$toast.error('Error consultando clientes')
                })
            },
            deleteVideo(item) {
                axios.get(`api/delete-video/${item.id}`).then(res => {
                    this.videos.splice(this.videos.indexOf(item), 1)
                    this.$toast.sucs('Video eliminando con exito')
                }, res => {
                    this.$toast.error('Error eliminando video')
                })
            },
            changeActive(item) {
                axios.get(`api/change-video-active/${item.id}`).then(res => {
                    item.active = !item.active
                    this.$toast.sucs('Video actualizado')
                }, err => {
                    this.$toast.error('Error consultando clientes')
                })
            }
        },
        computed: {
            isloading: function() {
                return this.$store.getters.getloading
            },

            user: function() {
                return this.$store.getters.getuser
            }
        }
    }
</script>