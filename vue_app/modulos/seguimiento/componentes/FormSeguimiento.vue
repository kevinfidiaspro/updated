<template>
    <v-container>
       
        <v-card shaped class="mx-4 my-4 pa-4">
            <v-row>
                <v-col cols="12">
                    <v-toolbar
                        flat
                        color="#1d2735"
                        dark
                        style="border-radius: 5px"
                    >
                        <v-icon class="white--text" style="font-size: 45px"
                            >mdi-account-supervisor-circle</v-icon
                        >
                        <pre><v-toolbar-title><h2>Crear Seguimiento</h2></v-toolbar-title></pre>
                    </v-toolbar>
                    <v-form class="mt-3">
                        <v-row>
                            <v-col cols = "12" md="6">
                                <v-autocomplete
                                label="Proyecto"
                                :items="proyectos"
                                item-text="usuario.nombre"
                                item-value="id"
                                v-model="seguimiento.id_proyecto"
                            >

                            </v-autocomplete>
                            </v-col>
                            <v-col cols="12" md="6">  
            <date-select v-model="seguimiento.fecha" label="Fecha">

            </date-select>
        </v-col>
        <v-col cols="12" >  
            <v-textarea
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
                                    >Guardar</v-btn
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
        </v-card>
   
  

        
    </v-container>
</template>

<script>

export default {
    data() {
        return {
            proyectos:[],

            seguimiento:{
                
            },
        };
    },
    watch: {
      
   
    },
    created() {
       this.getProyectos();
        if (this.$route.query.id) {
         
            this.getSeguimiento(this.$route.query.id);
        }
      
    },
    methods:{
        getSeguimiento(id){
            const self = this;
            axios.get(`api/get-tarea-proyecto?id=${id}`).then(function(response){
                self.seguimiento = response.data;
            })
        },
        saveSeguimiento(){
            const self = this;
            console.log(this.seguimiento);
            axios.post('api/save-tareas-proyecto',this.seguimiento).then(function(response){
                self.$router.push('lista-seguimiento');
            })
        },
        getProyectos(){
            const self = this;
            axios.get('api/get-all-potenciales').then(function(response){
                self.proyectos = response.data;
            })
        },
        createSeguimiento(){

        }
    },
    filters: {
      
    },

  
};
</script>
