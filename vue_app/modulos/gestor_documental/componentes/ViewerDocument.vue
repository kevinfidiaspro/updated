<template>
    <div  class="background my-container">
      <v-custom-title :text="'Documento ' + captureUriObj.documento "></v-custom-title>
      <span v-if="captureUriObj.format == 'zip'">Su archivo se esta descargando...</span>
        <loader v-if="isloading"></loader>
        <v-row >

           
            <v-system-bar window>
       
                <a
                    :href="`#/gestor-documentos/`+captureUriObj.user_id" class="action-buttons" >
                    <span 
                            style="cursor:pointer"
                            >
                        <v-icon  color="black">mdi-arrow-left</v-icon>Atras
                    </span>
                </a>
                
                <v-spacer></v-spacer>
        </v-system-bar>

        </v-row>
        <v-row>
            
            <span v-for="(img, i) in images" :key="i" style="text-align: center">
                <img  
                    v-if="captureUriObj.format == img" 
                    :src="realPathImage" style="width: 80%; height: 80%">
            </span>
            

            <iframe 
                v-if="captureUriObj.format == 'pptx' ||
                      captureUriObj.format == 'xlsx' ||
                      captureUriObj.format == 'docx' "
                :src="'https://docs.google.com/gview?url='+realPathImage + '&embedded=true'" 

                style="width:100%; height:100vh"
               >

            </iframe>

             <iframe 
                v-if="

                      captureUriObj.format == 'pdf'"
                :src="realPathImage " 
                style="width:100%; height:100vh"
               >
            </iframe>
           
        </v-row>
    </div>
</template>
<script>
    import ControlesGestor from './ControlesGestor.vue'

     export default {
        components :{
            'controles-gestor' : ControlesGestor,
        },
        
        data() {
            return {
                image : '',
                pathP: null,
                pathDecode: '',
                realPathImage:  '',


                images : [
                    'jpg',
                    'png',
                    'jpeg',
                    'JPG',
                    'JPEG',
                    'PNG',
                ]
            }
        },
        computed:{
             captureUriObj(){

                let hashUri =  window.location.hash
                let splithash = hashUri.split('/')
                let resUriId =  splithash[2]
                let resUriDocument =  splithash[3]
                let resUriFormat =  splithash[4]

                let objUri = {
                    'user_id' :resUriId*1,
                    'documento' : resUriDocument,
                    'format' : resUriFormat
                }
                return objUri

            },
           
            isloading: function() {
                return this.$store.getters.getloading
            },
            localStorageComputers(){
                return localStorage.user_id
            },
        },
        created() {
            
            // this.getViewDocument()
            this.pathP = window.origin
            this.decodeUri()
            this.splitPath()
        },
        methods:{
            splitPath(){
                let splitPathDecode = this.pathDecode.split('/')
               
                let obtainPath = ''
                for (let i = 0; i < splitPathDecode.length; i++) {
                    if(splitPathDecode[i] == 'storage'){
                        for (let n = i; n < splitPathDecode.length; n++) {
                            if(splitPathDecode[n] == 'app' || splitPathDecode[n] == 'public'){
                               
                                
                            }else{
                                 obtainPath = obtainPath + splitPathDecode[n] + '/'
                            }
                        }

                       
                    }
                }

                this.realPathImage = window.location.origin +'/'+obtainPath + this.captureUriObj.documento
                console.log( this.realPathImage)
               
            },
            decodeUri(){
                let hashUri =  window.location.hash
                let splithash = hashUri.split('/')
                let resUriId =  splithash
                let pathImg = resUriId[resUriId.length-1]
                let rep = decodeURIComponent(pathImg)
                this.pathDecode = rep
            },
           
        }
     };
    
</script>