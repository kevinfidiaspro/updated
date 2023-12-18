<template>
	
	<div>
		
		 <v-row>
		 	
		 	<file-input  
                    :files="files" 
                    v-on:file-change="setFiles" 
                    file-clear="clearFiles"
                    id="inputFile">
                    
            </file-input>

            <div v-if="botonEnviar">
            	<v-btn @click="uploadFile">
            		Escanear Foto
            	</v-btn>
            </div>
		 </v-row>

        <v-row>
    		<v-col cols="12" sm="12" md="6" >
               <span v-if="imagePreview">
                 <v-tooltip bottom>
                   <template v-slot:activator="{ on, attrs }">
                    <v-btn
                        x-small
                        v-model="clearFileButton"
                        color="error"
                        v-bind="attrs"
                        v-on="on"
                        @click="resetInput()"
                        type="button"
                        class="float-left">
                              <v-icon>mdi-close</v-icon>
                    </v-btn>
                     </template>
                        <span>Quitar Imagen</span>
                     </v-tooltip>

                     <v-row v-for="img,i,key in imagePreview" :key="i">
                         <img  width="100%" style="height:100%" :src="img"  class="img-responsive">
                     </v-row>
                    
               </span>
             	<!--    <span v-if="editMode==true">

                    <img v-if="ocultarImagen" width="200" height="200" :src="'/assets/imagenes/tiendas/'+ obtenerImage"  class="img-responsive">
               </span> -->
          </v-col>
           
           <v-col cols="12" sm="12" md="6" >
           	<div style="overflow-y: scroll!important; height:100%;">
           		<pre>
           			{{responseScan}}
           		</pre>
           	</div>
           		
           </v-col>

        </v-row>
	</div>	
</template>

<script>
    import FileInput from '../../../../global_components/FileInput.vue'
   
     export default {
        components: {
            'file-input' : FileInput,
           

        },
		data() {
            return {
               files : [],
               imagePreview: null,
               levelSelect: null,
                mandar: '',
               clearFileButton: false,
               ocultarImagen: false,
               botonEnviar: false,

               responseScan : ''
            }
        },
        computed:{
           
            captureUriId(){
                let hashUri =  window.location.hash
                let splithash = hashUri.split('/')
                let resUri =  splithash[2]
                return resUri*1

            },
        },
        created() {
          
            
            
        },
        methods:{
            
           resetInput(){
               // this.ocultarImagen = true
               this.functioResetInput()
               // this.form.nombreImagen = 'Selecciona un logo para tu tienda'

           },
           functioResetInput(){

           		var input = document.getElementById("inputFile")
                input.children[0].type = 'text'
                input.children[0].type = "file"
                this.files = []

                // const input = this.$refs.fileInput
                // input.type = 'text'
                // input.type = 'file'
                this.imagePreview =  []
                this.responseScan = ''
                // this.clearFileButton = false
           },
            uploadFile(){
               

                // if(this.files.length == 0){
                //     alert("Debe seleccionar al menos un archivo")
                //     return 
                // }

                console.log(this.files)
               
                let formSendFiles = new FormData()
                 for (let fileSave of this.files) {
                    formSendFiles.append('imagen[]', fileSave, fileSave.name)

                }
                formSendFiles.append('user_id', this.captureUriId)
                formSendFiles.append('carpeta', 'ocr')
                formSendFiles.append('parentPholder', 'ocr')


                for (var file in this.files.length) {
                    
                    formSendFiles.append('imagen[]', file, file.name)
                }
                
               
                axios.post(`api/ocr`, formSendFiles).then(res => {
                
                    let respuestOk = res.status
                    if(respuestOk*1 == 200){
                        this.$toast.sucs('Documento cargado')
                        
                        this.responseScan = res.data.ocr
                       	console.log(this.responseScan )
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
                this.botonEnviar = true
                console.log(this.imagePreview)

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
                this.responseScan = ''
            }

            
           
        }
     };
     
</script>