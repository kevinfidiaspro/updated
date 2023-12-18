<template>
    <div>
        <v-row justify="center">
            <v-dialog v-model="dialogUploadFile" persistent max-width="50%">
            <v-card>
                <div class="tittlecard">
                    <v-custom-title text="Cargar Archivo" 
                        class="black--text mx-4">
                    </v-custom-title>
                </div>
                <v-card-text>                
                    <div class="form-group">
                        <file-input :files="files" v-on:file-change="setFiles"
                            file-clear="clearFiles" id="inputFile">
                        </file-input>
                    </div>
                    </v-card-text>
                    <v-card-actions>
                    <v-spacer></v-spacer>
                        <v-btn color="green" class="white--text" rounded @click="uploadFile()">Cargar</v-btn>
                        <v-btn color="red" class="white--text" rounded @click="closeDialogUploadFile()">Cerrar proceso</v-btn>
                    </v-card-actions>
            </v-card>
            </v-dialog>
        </v-row>
    </div>
</template>
<script>
    import FileInput from '../../../global_components/FileInput.vue'
    import ProgressBar from '../../../global_components/ProgressBar.vue'
     export default {
        components: {
            'file-input' : FileInput,
            ProgressBar,
            // ClassicEditor,

        },
        props: {
           
            dialogUploadFile: Boolean,
            closeDialogUploadFile: Function,
            reload: Function,
            captureUriId: Number,
            folderClick: Function,
            tree: Array
          
        },
        data() {
            return {
               files : [],
               imagePreview: null,
               levelSelect: null,
                mandar: ''
               
            }
        },
        computed:{
           
            
        },
        created() {
          
            if(this.dialogUploadFile == true){
                // this.levelFolders()
                
            }
            
        },
        methods:{
            
           
            uploadFile(){
               

                if(this.files.length == 0){
                    alert("Debe seleccionar al menos un archivo")
                    return 
                }

               
                let formSendFiles = new FormData()
                 for (let fileSave of this.files) {
                    formSendFiles.append('imagen[]', fileSave, fileSave.name)

                }
                formSendFiles.append('user_id', this.captureUriId)
                formSendFiles.append('carpeta', this.tree[0].name)
                formSendFiles.append('path', this.tree[0].path)
                formSendFiles.append('parentPholder', this.tree[0].parentPholder)
               
                axios.post(`api/save-documents`, formSendFiles).then(res => {
                
                    let respuestOk = res.status
                    if(respuestOk*1 == 200){
                        this.$toast.sucs('Documento cargado')
                        this.closeDialogUploadFile()
                        this.limpiar()
                        this.tree[0].path = res.data.destination
                        this.reload()
                       
                    }
                  
                }, err => {
                    this.$toast.error('Error cargando documento(s)')
                })
            },
             setFiles(files) {
                
                const filesPreview = files

                Object.keys(filesPreview).forEach(i => {
                    const file = filesPreview[i];
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.imagePreview.push(reader.result);
                    }
                    this.imagePreview = []
                    reader.readAsDataURL(file);

                });


                if (files !== undefined) {
                    this.files = files
                    this.disableUploadButtonImage = false
                }
            },

           
             limpiar(){
        
                // const input = this.$refs.file
                // input.type = 'text'
                // input.type = 'file'

                var input = document.getElementById("inputFile")
                input.children[0].type = 'text'
                input.children[0].type = "file"
                this.files = []
            }

            
           
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