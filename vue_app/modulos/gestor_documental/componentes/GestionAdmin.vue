<template>
    <div>
        <v-custom-title text="Gestor de Clientes" 
            class="black--text col-xs-6 col-sm-6 col-md-3 col-lg-3 col-xl-3 mx-4 px-4">
         </v-custom-title>
        <div class="background my-container col-10 offset-1 mt-4">
            <loader v-if="isloading"></loader>
            <v-row justify="center">
                <v-col cols="12" class="col-xs-6 col-sm-6 col-md-4 col-lg-4 col-xl-4 
                       offset-xs-6 offset-sm-6 offset-md-8 offset-lg-8 offset-xl-8 mb-1 pb-1">
                <v-text-field 
                        single-line
                        outlined
                        :loading="loadingSearch"
                        color="success"
                        append-icon="mdi-account-search"
                        v-model="search" 
                        label=" Usuario a buscar...">
                    </v-text-field>
                </v-col>
                <v-col class="col-xs-12 col-sm-10 col-md-10 col-lg-10 col-xl-10 mt-1 pt-1 mb-4">
                    <v-data-table
                        dense
                        :headers="headers" 
                        :items="users" 
                        :search="search"  
                        hide-default-footer 
                        item-key="id" 
                        class="elevation-2">
                        <template v-slot:item.action="{item}">
                            <v-tooltip top>
                                <template v-slot:activator="{ on, attrs }">
                                    <a :href="`#/gestor-documentos/${item.id}`" class="action-buttons" v-bind="attrs" v-on="on">
                                        <v-icon small class="mr-2" color="yellow darken-4">
                                            mdi-folder
                                        </v-icon>
                                    </a>
                                </template>
                                <span>Administrador de archivos</span>
                            </v-tooltip>       
                            <v-tooltip top>
                                <template v-slot:activator="{ on, attrs }">
                                    <a  :href="`#/administrador-tareas/${item.id}`" class="action-buttons"  v-bind="attrs" v-on="on">
                                        <v-icon small class="mr-2" color="teal lighten-1">
                                        mdi-clipboard-text
                                        </v-icon>
                                    </a>
                                </template>
                                <span>Administrador de tareas</span>
                            </v-tooltip>
                            <!--v-icon @click="deleteUsuario(item)" small class="mr-2" color="red">
                                mdi-trash-can
                            </v-icon-->
                        </template>
                    </v-data-table>
                </v-col>
            </v-row>
        </div>
    </div>
</template>

<script>
     export default {
        props: {
          
        },
        data() {
            return {
                 headers: [
                    {
                        text: 'Nombre',
                        value: 'name',
                        align: 'center'
                    },
                    {
                        text: 'Email',
                        value: 'email',
                        align: 'center'
                    },
                    {
                        text: 'Telefono',
                        value: 'telefono',
                        align: 'center'
                    },
                    {
                        text: 'Ciudad',
                        value: 'ciudad',
                        align: 'center'
                    },
                    {
                        text: 'Archivos / Tareas',
                        value: 'action',
                        align: 'center',
                        sortable: false
                    },
                ],
                search:'',
                searchQuery: null,
                users: [],
                loading: false
                
            }
        },
        computed:{
            usersFilter(){
                if(this.searchQuery){
                    return this.users.filter((item)=>{
                        this.load = true
                        return this.searchQuery.toLowerCase().split(' ').every(v => item.name.toLowerCase().includes(v), v => item.email.toLowerCase().includes(v))
                    })
                }else{
                    return this.users;
                }
            },

            loadingSearch(){
                if(this.searchQuery == null || this.searchQuery == ""){
                    return this.loading = false
                }else{
                    return this.loading = true
                }
            },

            isloading: function() {
                return this.$store.getters.getloading
            },

            localStorageComputers(){
                return localStorage.user_id
            },
        },
        created() {           
           this.getUserForAdmin()
        },

        methods:{
            getUserForAdmin(){
                axios.get(`api/gestor-admin`).then(res => {
                   
                    if(res.status == 200){
                        this.users = res.data.users.data
                       
                        this.$toast.sucs('Carga Completa')
                    }
                    
                }, err => {
                    this.$toast.error('Error de servidor')
                })
            }
        }
     };
    
</script>

<style>
    label{
        color: black !important;
    }

    .v-icon{
        color:black !important;
        font-weight: bolder;
    }

    th {
        font-family: Calibri !important;
        font-size: 18px !important;
        color: black !important;
        line-height: 1.4 !important;
        text-transform: uppercase;
    }

    td {
        border: none !important;
        color: black !important;
    }
</style>