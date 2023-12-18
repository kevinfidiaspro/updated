<template>
    <div>
        <v-row justify="center">
            <v-dialog v-model="dialogDeleteTask" persistent max-width="50%">
            <v-card>
                <div class="tittlecard">
                    <v-custom-title text="Eliminar Tarea" 
                        class="black--text mx-4">
                    </v-custom-title>
                </div>
                <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="red" class="white--text" rounded @click="deleteFile()"> Si, Eliminar </v-btn>
                <v-btn color="green" class="white--text" rounded @click="closedialogDeleteTask()"> No, Cerrar proceso
                </v-btn>
                </v-card-actions>
            </v-card>
            </v-dialog>
        </v-row>
    </div>
</template>
<script>
     export default {
        props: {
           dialogDeleteTask: Boolean,
           closedialogDeleteTask: Function,
           captureTask: Object,
        },

        data() { 
            return {
                dragg: null,            
                task: null,    
            } 
        },

        created() {
            
        },

        methods:{
            deleteFile(){         
                for (var i = 0; i < this.captureTask.dragg.list.length; i++) {
                    if (this.captureTask.dragg.list[i].id == this.captureTask.task.id) {
                        const indice =   this.captureTask.dragg.list.indexOf(this.captureTask.task)                                 
                        axios.delete(`api/delete-tasks/`+ this.captureTask.task.id, this.captureTask.task).then(res => {
                            if(res.status*1 == 200){
                                    this.captureTask.dragg.list.splice(indice, 1)
                                    this.$toast.sucs('Tarea Eliminada')
                                    this.closedialogDeleteTask()
                            }
                        }, err => {
                            this.$toast.error('Error eliminar')
                        }) 
                    }
                }   
            },
        }
     };
</script>

<style>
    .tittlecard {
        padding-top: 15px !important;
        padding-bottom: 15px !important;
        margin-bottom: 30px !important;
        background-color: rgb(222, 222, 222) !important;
    }
</style>