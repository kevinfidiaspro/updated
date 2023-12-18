<template>
    <div>
         <v-row justify="center">
            <v-dialog v-model="dialogCrearCarpeta" persistent max-width="50%">
                <v-card>
                    <div class="tittlecard">
                        <v-custom-title text="crear carpeta"
                            class="black--text mx-4">
                        </v-custom-title>
                    </div>
                    <v-card-text>
                        <v-text-field class="mt-3 mb-0" label="Nombre de la carpeta" v-model="newCarpeta" outlined>
                        </v-text-field>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="green" class="white--text" rounded @click="crearCarpeta"> Crear Carpeta </v-btn>
                        <v-btn color="red" class="white--text" rounded @click="closeDialogCrearCarpeta()"> Cerrar </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
        </v-row>
            
    </div>
</template>
<script>
     export default {
        props: {
            dialogCrearCarpeta: Boolean,
            closeDialogCrearCarpeta: Function,
            captureUriId:Number,
            reload: Function,
            carpetas: Array,
            folderClick: Function,
            tree: Array
        },
        data() {
            return {
                newCarpeta: '',
                levelSelect: ''
            }
        },
        created() {
            // this.levelSelect = '/'        
        },
        computed:{
            levelFolders(){
                let levels = []
                let level = ''
                levels.push('/')
                for (let i = 0; i < this.carpetas.length; i++) {
                   
                    levels[levels.length] = this.carpetas[i].name + '/'
                   
                }
                
                return levels
                
            }
        },
        methods:{

            crearCarpeta(){

                this.levelSelect = this.tree[0].name

                if(this.newCarpeta == ""){
                    alert("El nombre de la carpeta no puede estar vacío")
                    return 
                }
                for (let i = 0; i < this.carpetas.length; i++) {
                   if(this.carpetas[i].name == this.newCarpeta){
                    alert("El nombre de la carpeta ya existe en su directorio")
                    return 
                   }
                }
                
                //Enviar petición para crear carpeta
                let formCreateFolder = new FormData()
                formCreateFolder.append('folderName', this.newCarpeta)
                formCreateFolder.append('level', this.levelSelect)
                formCreateFolder.append('user_id', this.captureUriId)
                formCreateFolder.append('path', this.tree[0].path)
                formCreateFolder.append('original', this.tree[0].original)
                
               
                axios.post(`api/create-folder`, formCreateFolder).then(res => {
                    if(res.status == 200){
                        this.reload()
                        this.closeDialogCrearCarpeta()
                        let LevelPass = res.data.level
                        for (let e = 0; e < this.carpetas.length; e++) {
                            if(this.carpetas[e].name == LevelPass){
                                
                            }
                        }
                        // let newFolderPass = res.data.newFolder.name//Nueva carpeta creada. Respuesta controlador
                        // let newIdPass = res.data.newFolder.id //id Nueva carpeta creada. Respuesta controlador
                        // //let folderNewId = this.carpetas[this.carpetas.length -1].id +(1)
                        // let objCarpetaOnChildren = {
                        
                        //     name: newFolderPass,
                        //     children: [
                            
                        //     ],
                        // }
                        // let objCarpeta = {
                        //     id : newIdPass,
                        //     name: newFolderPass,
                        //     children: [
                            
                        //     ],
                        // }

                        // for (let p = 0; p <  this.carpetas.length; p++) {
                        //     if(LevelPass ==  this.carpetas[p].name + '/' && LevelPass != '/'){
                        //         this.carpetas[p].children.push(objCarpeta)
                        //     }
                            
                        // }

                        // if(LevelPass == '/'){
                        //     this.carpetas.push(objCarpeta)
                        // }
                        
                        this.newCarpeta = ''
                        this.$toast.sucs('Carpeta creada')
                    }
                }, err => {
                    this.$toast.error('Error creando carpeta')
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