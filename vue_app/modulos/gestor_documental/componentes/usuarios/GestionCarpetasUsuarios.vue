<template>
    <div class="background my-container">
      <v-custom-title text="Gestor Documental de Usuario "></v-custom-title>

        <loader v-if="isloading"></loader>
        <v-row justify="center">
            <v-col cols="12" xs="12" sm="12" md="12" lg="12" xl="12">
                 <controles-gestor 
                        :captureItem="captureItem"
                        :openDialogCrearCarpeta="openDialogCrearCarpeta"
                        :fileOrDiretoryOptions="fileOrDiretoryOptions"
                        :reload="reload"
                        :modalDeleteFile="modalDeleteFile"
                        :openDialogUploadFile="openDialogUploadFile"
                        :captureUriId="captureUriId">
                    </controles-gestor>
                    
                <v-treeview
                    v-model="tree"
                    :open="initiallyOpen"
                    :items="filesFilter"
                    item-text=""
                    item-key="name"
                    activatable
                    open-on-click>

                    <template v-slot:prepend="{ item, open }"   >
                        <v-icon v-if="!item.file"  @click="folderClick(item)">
                            {{ open ? 'mdi-folder-open' : 'mdi-folder' }}
                        </v-icon>
                        <v-icon v-else>
                            {{ files[item.file] }}
                        </v-icon>
                        <span @click="folderClick(item)" style="position:absolute; width:100%; padding:0px 20px 0px 20px">{{item.name}}</span>
                        <!--a :href="computedPathViewDocument" target="_blank" v-if="fileOrDiretoryOptions == true">
                            <span 
                                    style="cursor:pointer">
                                <v-icon  color="black">mdi-eye</v-icon>Ver
                            </span -->
                    
                        <v-spacer></v-spacer>
                    </template>
                </v-treeview>
            </v-col>

            <v-col cols="12" xs="12" sm="12" md="12" lg="12" xl="12" >
               
                
            </v-col>
        </v-row>

            <dialog-create-folder   :dialogCrearCarpeta="dialogCrearCarpeta" 
                                    :openDialogCrearCarpeta="openDialogCrearCarpeta"
                                    :carpetas="carpetas"
                                    :reload="reload"
                                    :closeDialogCrearCarpeta="closeDialogCrearCarpeta"
                                    :captureUriId="captureUriId"></dialog-create-folder>

            <dialog-delete-file     :dialogDeleteFile="dialogDeleteFile"
                                    :captureItem="captureItem"
                                    :closeDialogDeleteFile="closeDialogDeleteFile"
                                    :captureUriId="captureUriId"
                                    :carpetas="carpetas"
                                    :setResteCaptureItem="setResteCaptureItem"></dialog-delete-file>

            <dialog-upload-file     :dialogUploadFile="dialogUploadFile"
                                    :closeDialogUploadFile="closeDialogUploadFile"
                                    :reload="reload"
                                    :captureUriId="captureUriId"
                                    :carpetas="carpetas"
                                    :files="files"
                                    :initiallyOpen="initiallyOpen"></dialog-upload-file>
    </div>
</template>
<script>
    import ControlesGestor from '../ControlesGestor.vue'
    import DialogCreateFolder from '../DialogCreateFolder.vue'
    import DialogDeleteFile from '../DialogDeleteFile.vue'
    import DialogUploadFile from '../DialogUploadFile.vue'
    import GestionAdmin from '../GestionAdmin.vue'

    export default {
        components :{
            'controles-gestor' : ControlesGestor,
            'dialog-create-folder': DialogCreateFolder,
            'dialog-delete-file':   DialogDeleteFile,
            'dialog-upload-file':   DialogUploadFile,
            'gestion-admin':   GestionAdmin
        },
        props: {
          
        },
        data() {
            return {
            
                files: {
                    html: 'mdi-language-html5',
                    js: 'mdi-nodejs',
                    json: 'mdi-code-json',
                    md: 'mdi-language-markdown',
                    pdf: 'mdi-file-pdf',
                    png: 'mdi-file-image',
                    jpg: 'mdi-file-image',
                    jpeg: 'mdi-file-image',
                    txt: 'mdi-file-document-outline',
                    xls: 'mdi-file-excel',
                },
                documentos:[],
                tree: [],
                
                carpetas: [],
                initiallyOpen: ['Documentación General'],
                captureItem: {},
                fileOrDiretoryOptions: false,
                dialogCrearCarpeta: false,
                dialogUploadFile: false,

                todosLosArchivos: [],

                dialogDeleteFile: false,

            }
        },
        computed:{
            isloading: function() {
                return this.$store.getters.getloading
            },
            localStorageComputers(){
                return localStorage.user_id
            },
            captureUriId(){
                let hashUri =  window.location.hash
                let splithash = hashUri.split('/')
                let resUri =  splithash[2]
                return resUri*1

            },
            filesFilter(){
                if(this.searchQuery){
                        return this.carpetas.filter((item)=>{
                            this.load = true
                            return this.searchQuery.toLowerCase().split(' ').every(v => item.name.toLowerCase().includes(v), v => item.file.toLowerCase().includes(v))
                            
                        })
                }else{
                        return this.carpetas;
                }
            },
        },
        created() {
            
           this.getDocumentos()
        },
        methods:{
             setResteCaptureItem(){
                this.captureItem =  {}
            },
             folderClick(carpeta){
                console.log(carpeta)
                let carpetasPrincipales = [
                    'Documentación General',
                    'Facturas Emitidas',
                    'Facturas Recibidas'
                ]

                if(carpetasPrincipales[0] == carpeta.name || carpetasPrincipales[1] == carpeta.name || carpetasPrincipales[2] == carpeta.name){
                    this.fileOrDiretoryOptions = false  
                }else{
                    this.captureItem = carpeta
                    this.fileOrDiretoryOptions = true
                     
                }
                
                
            },
            openDialogUploadFile(){
                 this.dialogUploadFile = true
            },
            closeDialogUploadFile(){
                this.dialogUploadFile = false
            },
            
            closeDialogDeleteFile(){
                this.dialogDeleteFile = false
            },
            modalDeleteFile(){
                this.dialogDeleteFile = true
            },
            closeDialogCrearCarpeta(){
                this.dialogCrearCarpeta = false
            },
            openDialogCrearCarpeta(){
                this.dialogCrearCarpeta = true
            },
            getDocumentos(){
                axios.get(`api/get-documentos/`+ this.captureUriId).then(res => {
                    this.documentos = res.data.documentos
                    this.facturasEmitidas = res.data.factFiles
                    this.carpetas =  res.data.carpetas
                    this.todosLosArchivos = res.data.todosLosArchivos
                   
                    
                    for (let i = 0; i <  this.carpetas.length; i++) {
                        this.carpetas[i].children = JSON.parse(this.carpetas[i].children)
                        for (let n = 0; n < this.carpetas[i].children.length; n++) {
                            for (let x = 0; x <  this.carpetas[i].children[n].children.length; x++) {
                                this.carpetas[i].children[n].children = JSON.parse(this.carpetas[i].children[n].children)
                                
                            }
                            //  console.log(this.carpetas[i].children)
                            // return 
                            // this.carpetas[i].children[n] =  Array()
                            // if(JSON.parse(this.carpetas[i].children[n]).length == 0){
                            //     this.carpetas[i].children[n] =  Array()
                            // }
                        }
                        // if(JSON.parse(this.carpetas[i].children).length == 0){
                        //     this.carpetas[i].children = Array()
                        //     // for (let n = 0; n < this.carpetas[i].children.length; n++) {
                        //     //     if(JSON.parse(this.carpetas[i].children[n]).length == 0){
                        //     //         this.carpetas[i].children[n] =  Array()
                        //     //     }
                        //     // }
                        // }
                        
                        
                    }
                    //  console.log(this.items)
                    //     console.log(this.carpetas)
                    //     return 
                   
                  
                    for (let c = 0; c < this.carpetas.length; c++) {
                        //Push documetos generales
                        if(this.carpetas[c].name == 'Documentación General'){
                            for (let d = 0; d < this.documentos.length; d++) {
                               for (let i = 0; i < this.documentos[d].href.length; i++) {
                                    const documento = this.documentos[d].href[i]
                                    let split = this.documentos[d].href[i].split('.')
                                    
                                    this.carpetas[c].children.push({
                                        name : documento,
                                        file : split[1],
                                        path : this.documentos[d].path
                                    })
                                    
                                } 
                            } 
                        }
                        //End Push documetos generales

                        //Push facturas emitidas
                        if(this.carpetas[c].name == 'Facturas Emitidas'){
                            for (let d = 0; d < this.facturasEmitidas.length; d++) {
                               for (let i = 0; i < this.facturasEmitidas[d].href.length; i++) {
                                    const factura = this.facturasEmitidas[d].href[i]
                                    this.carpetas[c].children.push({
                                        name : factura,
                                        path : this.documentos[d].path
                                    })
                                } 
                            } 
                        }
                       

                        //end facturas emitidas

                        if(this.carpetas[c].name == 'Facturas Recibidas'){
                            // for (let d = 0; d < this.facturasEmitidas.length; d++) {
                            //    for (let i = 0; i < this.facturasEmitidas[d].href.length; i++) {
                            //         const factura = this.facturasEmitidas[d].href[i]
                            //         this.carpetas[c].children.push({
                            //             name : factura,
                            //             file : 'pdf'
                            //         })
                            //     } 
                            // } 
                        }
                    }

                    for (let c = 0; c < this.carpetas.length; c++) {
                        
                        for (let i = 0; i <  this.carpetas[c].children.length; i++) {
                           let carpetaName = this.carpetas[c].children[i].name

                           for (let j = 0; j < this.todosLosArchivos.length; j++) {
                               if(this.todosLosArchivos[j].nombreCarpeta == carpetaName){
                                   for (let k = 0; k < this.todosLosArchivos[j].href.length; k++) {
                                       let formatAll =  this.todosLosArchivos[j].href[k].split('.')
                                       this.carpetas[c].children[i].children.push({
                                            name : this.todosLosArchivos[j].href[k],
                                            file :  formatAll[1],
                                            path :  this.todosLosArchivos[j].path
                                        })  
                                   } 
                               }
                           }
                        }
                        
                    }
                    
                   

                }, err => {
                    this.$toast.error('Error consultando documentos')
                })
            },
             reload(){
                
                this.getDocumentos()
            }
            
        }
     };
    
</script>