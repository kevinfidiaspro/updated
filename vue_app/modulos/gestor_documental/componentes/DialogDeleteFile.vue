<template>
    <div>
        <v-row justify="center">
            <v-dialog v-model="dialogDeleteFile" persistent max-width="50%">
            <v-card>
                <div class="tittlecard">
                    <v-custom-title text="Eliminar elementos" 
                        class="black--text mx-4">
                    </v-custom-title>
                </div>
                <v-card-text>
                    <strong>
                        <h3>Se eliminarán los siguientes elementos</h3>                     
                        <h3>
                        <span v-for="(arbol, i ) in tree"  :key="i">
                            <ul><li>{{arbol.newName ? arbol.newName : arbol.name}}</li></ul>
                        </span>
                        </h3> 
                    </strong>                   
                </v-card-text>
                <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="red" class="white--text" rounded @click="deleteFile()"> Si, Eliminar </v-btn>
                <v-btn color="green" class="white--text" rounded @click="closeDialogDeleteFile()"> No, Cerrar proceso
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
           dialogDeleteFile: Boolean,
           closeDialogDeleteFile: Function,
           captureUriId: Number,
           carpetas: Array,
           tree: Array,
        },

        data() { return { } },
        created() { },

        methods:{
            deleteFile(){                
                let objDelete = new FormData()
                objDelete.append('user_id', this.captureUriId)
                objDelete.append('tree', JSON.stringify(this.tree))
                axios.post(`api/delete-documentos`, objDelete).then(res => {                       
                    this.closeDialogDeleteFile()
                    this.$toast.sucs('Archivo/Carpeta Eliminado')
                }, err => {
                    this.$toast.error('Error eliminando documento')
                })
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