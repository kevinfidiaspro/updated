<template>
    <div>
        <v-custom-title text="Gestor Documental" 
            class="black--text col-xs-6 col-sm-6 col-md-3 col-lg-3 col-xl-3 mx-4 px-4">
        </v-custom-title>
        <div class="background my-container col-10 offset-1 mb-4">
            <loader v-if="isloading"></loader>
            <v-row justify="center" class="mb-4">
                <v-col cols="12" xs="12" sm="12" md="12" lg="12" xl="12" class="mb-4">
                    <!--v-text-field
                            color="success"
                            :loading="loadingSearch"
                            label="Buscar Directorio"
                            v-model="searchQuery"
                            prepend-icon="mdi-search"
                            clear-icon="mdi-close-circle-outline"
                    ></v-text-field-->
                    <controles-gestor 
                        :openDialogCrearCarpeta="openDialogCrearCarpeta"
                        :fileOrDiretoryOptions="fileOrDiretoryOptions"
                        :reload="reload"
                        :modalDeleteFile="modalDeleteFile"
                        :openDialogUploadFile="openDialogUploadFile"
                        :captureUriId="captureUriId"
                        :tree="tree">
                    </controles-gestor>
                    <v-treeview
                        :selection-type="selectionType"
                        v-model="tree"
                        :open="initiallyOpen"
                        :items="carpetas"
                        item-text=""
                        active-class="v-treeview-node--active"
                        item-key="name" 
                        :active="tree"
                        rounded
                        hoverable
                        selectable
                        shaped
                        return-object
                    >
                        <!-- activatable -->
                        <template v-slot:prepend="{ item, open }"   @click="folderClick(item)" >                        
                        <!--     <span >
                                <v-icon  color="success" v-if="item.select" @click="folderClick(item)">
                                mdi-checkbox-marked
                                </v-icon>
                                <v-icon v-else @click="folderClick(item)">
                                    
                                    mdi-checkbox-blank-outline
                                </v-icon>
                            </span> -->
                            <v-icon v-if="!item.file"  @click="folderClick(item)" >
                                {{ open ? 'mdi-folder-open' : 'mdi-folder' }}
                            </v-icon>
                            <v-icon v-else>
                                {{ files[item.file] }}
                            </v-icon>
                            <span
                                v-if="!item.file"
                                activatable @click="folderClick(item)" 
                                style="position:absolute; 
                                        width:100%; 
                                        cursor:pointer; padding:0px 20px 0px 20px;">
                                {{item.name}} 
                            </span>
                            <span 
                                v-else
                                activatable @click="folderClick(item)" 
                                style="position:absolute; 
                                        width:100%; 
                                        cursor:pointer; 
                                        padding:0px 20px 0px 20px;">
                                {{item.newName}}
                            </span>        
                            <v-spacer></v-spacer>
                        </template>
                    </v-treeview>
                </v-col>
            </v-row>

                <dialog-create-folder   
                    :dialogCrearCarpeta="dialogCrearCarpeta" 
                    :tree="tree"
                    :openDialogCrearCarpeta="openDialogCrearCarpeta"
                    :carpetas="carpetas"
                    :reload="reload"
                    :closeDialogCrearCarpeta="closeDialogCrearCarpeta"
                    :captureUriId="captureUriId"
                    :folderClick="folderClick"
                ></dialog-create-folder>

                <dialog-delete-file     
                    :dialogDeleteFile="dialogDeleteFile"
                    :tree="tree"
                    :closeDialogDeleteFile="closeDialogDeleteFile"
                    :captureUriId="captureUriId"
                    :carpetas="carpetas"
                ></dialog-delete-file>

                <dialog-upload-file    
                    :dialogUploadFile="dialogUploadFile"
                    :tree="tree"
                    :closeDialogUploadFile="closeDialogUploadFile"
                    :reload="reload"
                    :captureUriId="captureUriId"
                    :files="files"
                    :folderClick="folderClick">
                </dialog-upload-file>
        </div>        
    </div>   
</template>

<script>
import axios from 'axios'
import ControlesGestor from './ControlesGestor.vue'
import DialogCreateFolder from './DialogCreateFolder.vue'
import DialogDeleteFile from './DialogDeleteFile.vue'
import DialogUploadFile from './DialogUploadFile.vue'
import GestionAdmin from './GestionAdmin.vue'

// require ('./formats.js');
    export default {
        components :{
            'controles-gestor' :    ControlesGestor,
            'dialog-create-folder': DialogCreateFolder,
            'dialog-delete-file':   DialogDeleteFile,
            'dialog-upload-file':   DialogUploadFile,
            'gestion-admin':        GestionAdmin
        },
        data() {
            return {
                selectionType : 'independent', //leaf
                dialogUploadFile :false,
                captureItem: {},
                fileOrDiretoryOptions: false,
                dialogCrearCarpeta: false,
                dialogDeleteFile: false,
                search: '',
                initiallyOpen: ['Documentación General'],

                // "pdf", "PDF","png","PNG","html","HTML", "webp","WEBP","jpg", "JPG","jpeg","JPEG", "xlsx","XLSX", "xls","XLS", "svg","SVG", "docx", "DOXC","doc","DOC","ppt","PPT","pptx","PPTX","txt","TXT", "gif","GIF","csv","CSV","zip","ZIP","gz","GZ","rar","RAR","tar","TAR","xml","XML","xmls","XMLS", "mp3", "mp4", "json", "epub", "EPUB", "ibook", "IBOOK", "awz"
                files: {
                    html: 'mdi-language-html5',
                    HTML: 'mdi-language-html5',
                    js: 'mdi-nodejs',
                    json: 'mdi-code-json',
                    md: 'mdi-language-markdown',
                    pdf: 'mdi-file-pdf',
                    PDF: 'mdi-file-pdf',
                    png: 'mdi-file-image',
                    PNG: 'mdi-file-image',
                    jpg: 'mdi-file-image',
                    JPG:  'mdi-file-image',
                    jpeg: 'mdi-file-image',
                    JPEG: 'mdi-file-image',
                    txt: 'mdi-file-document-outline',
                    TXT: 'mdi-file-document-outline',
                    xls: 'mdi-file-excel',
                    XLS: 'mdi-file-excel',
                    xlsx: 'mdi-file-excel',
                    XLSX: 'mdi-file-excel',
                    doc:  'mdi-file-word',
                    DOC:  'mdi-file-word',
                    docx: 'mdi-file-word',
                    DOCX: 'mdi-file-word',
                    pptx: 'mdi-file-powerpoint',
                    PPT: 'mdi-file-powerpoint',
                    PPTX: 'mdi-file-powerpoint',
                    ppt: 'mdi-file-powerpoint',
                    text: 'mdi-clipboard-text',
                    XML: 'mdi-clipboard-text',
                    xml: 'mdi-clipboard-text',
                    xmls: 'mdi-clipboard-text',
                    XMLS: 'mdi-clipboard-text',
                    json: 'mdi-clipboard-text',
                    epub: 'mdi-clipboard-text',
                    EPUB: 'mdi-clipboard-text',
                    EBOOK: 'mdi-clipboard-text',
                    ebook: 'mdi-clipboard-text',
                    awz: 'mdi-clipboard-text',
                    webp: 'mdi-file-image',
                    WEBP: 'mdi-file-image',
                    SVG: 'mdi-svg',
                    svg: 'mdi-svg',
                    GIF: 'mdi-play',
                    gif: 'mdi-play',
                    csv: 'mdi-clipboard-text',
                    CSV: 'mdi-clipboard-text',
                    ZIP: 'mdi-zip-box',
                    zip: 'mdi-zip-box',
                    gz: 'mdi-zip-box',
                    GZ: 'mdi-zip-box',
                    RAR: 'mdi-zip-box',
                    rar: 'mdi-zip-box',
                    tar: 'mdi-zip-box',
                    TAR: 'mdi-zip-box',
                },
                documentos:[],
                facturasEmitidas : [],
                facturasRecibidas: [],
                tree: [],
                carpetas: [],
                searchQuery: null,
                loading: false,
                todosLosArchivos: [],
                activarTre: false,
                filesAndDirectories: [],
            }
        },
         computed: {
            captureUriId(){
                let hashUri =  window.location.hash
                let splithash = hashUri.split('/')
                let resUri =  splithash[2]
                return resUri*1
            },

            isloading: function() {
                return this.$store.getters.getloading
            },

            localStorageComputers(){
                return localStorage.user_id
            },

            filesFilter(){
                    if(this.searchQuery){
                        return this.carpetas.filter((item)=>{
                            this.load = true
                            return this.searchQuery.toLowerCase().split(' ').every(v => item.name.toLowerCase().includes(v), v => item.file.toLowerCase().includes(v))
                        })
                    }
                    else
                    {
                        return this.carpetas;
                    }
            },

            loadingSearch(){
                if(this.searchQuery == null || this.searchQuery == ""){
                    return this.loading = false
                }
                else
                {
                    return this.loading = true
                         
                }
            },
           
        },
        created() {
            this.getDocumentos()
            
        },
        methods: {
            setResteCaptureItem(){
                this.captureItem =  {}
            },

            openDialogUploadFile(){
               
                this.dialogUploadFile = true
            },

            closeDialogUploadFile(){
                this.dialogUploadFile = false
            },
            
            closeDialogDeleteFile(){
                this.dialogDeleteFile = false
                this.reload()
                 
            },

            modalDeleteFile(){
                this.dialogDeleteFile = true
            },
           
            folderClick(carpeta){
                for (var i = 0; i < this.carpetas.length; i++) {

                    this.carpetas[i].select = false
                }
                // for (var i = 0; i < this.carpetas.length; i++) {
                //      for (var j = 0; j < this.carpetas[i].children.length; j++) {
                //         this.carpetas[i].children[j].select = false
                //     }
                // }
                // for (var i = 0; i < this.carpetas.length; i++) {
                //      for (var j = 0; j < this.carpetas[i].children.length; j++) {
                //          for (var k = 0; k < this.carpetas[i].children[j].children.length; k++) {
                //              this.carpetas[i].children[j].children[k].select = false
                //          }
                //     }
                // }
                //  for (var i = 0; i < this.carpetas.length; i++) {
                //      for (var j = 0; j < this.carpetas[i].children.length; j++) {
                //          for (var k = 0; k < this.carpetas[i].children[j].children.length; k++) {
                //             for (var l = 0; l < this.carpetas[i].children[j].children[k].children.length; l++) {
                //                  this.carpetas[i].children[j].children[k].children[l].select = false
                //              }
                //          }
                //     }
                // }
                carpeta.select = true
                this.tree.push(carpeta)
                console.log(carpeta)
                let carpetasPrincipales = [
                    'Documentación General',
                    'Facturas Emitidas',
                    'Facturas Recibidas'
                ]
                this.tree
            },
           
            closeDialogCrearCarpeta(){
                this.dialogCrearCarpeta = false
            },

            openDialogCrearCarpeta(){
                if( this.captureItem == null || this.captureItem == ""){
                    alert("Seleccione Una Carpeta")
                    return 
                }
                this.dialogCrearCarpeta = true
            },

            getDocumentos(){
                axios.get(`api/get-documentos/`+this.captureUriId).then(res => {
                    this.todosLosArchivos = res.data.todosLosArchivos
                    this.carpetas = []
                    //carpetas raiz
                   for (var j = 0; j < this.todosLosArchivos.length; j++) {
                        if (this.todosLosArchivos[j].parentPholder == null) {
                            this.carpetas.push({
                                id : Math.random().toString(36).substring(7),
                                name: this.todosLosArchivos[j].nombreCarpeta,
                                path: this.todosLosArchivos[j].path,
                                parentPholder : this.todosLosArchivos[j].parentPholder,
                                children:  [],
                                original:  this.todosLosArchivos[j].carpetaOriginal,
                                isFolder : true,
                                nivel: 1,
                                select : false
                            })
                        }
                   }
                   //primer  push de carpetas
                    for (var a = 0; a < this.carpetas.length; a++) {
                        for (var b = 0; b < this.todosLosArchivos.length; b++) {
                            if (this.todosLosArchivos[b].parentPholder == this.carpetas[a].original &&
                                this.todosLosArchivos[b].path == this.carpetas[a].path + '/' + this.todosLosArchivos[b].carpetaOriginal ) {
                                this.carpetas[a].children.push({
                                    id : Math.random().toString(36).substring(7),
                                    name: this.todosLosArchivos[b].nombreCarpeta,
                                    parentPholder: this.todosLosArchivos[b].parentPholder,
                                    path: this.todosLosArchivos[b].path,
                                    children: [],
                                    original:  this.todosLosArchivos[b].carpetaOriginal,
                                    isFolder : true,
                                    nivel: 2,
                                    select : false
                                })
                            }
                        }
                    }
                    //segundo push de carpetas
                    for (var a = 0; a <  this.carpetas.length; a++) {
                        for(var b = 0; b <  this.carpetas[a].children.length; b++) {
                           for (var c = 0; c < this.todosLosArchivos.length; c++) {

                                if (this.carpetas[a].children[b].name == this.todosLosArchivos[c].parentPholder  &&
                                    this.todosLosArchivos[c].path == this.carpetas[a].children[b].path + '/' + this.todosLosArchivos[c].carpetaOriginal) {
                                    this.carpetas[a].children[b].children.push({
                                        id : Math.random().toString(36).substring(7),
                                        name:  this.todosLosArchivos[c].nombreCarpeta,
                                        parentPholder: this.todosLosArchivos[c].parentPholder,
                                        path: this.todosLosArchivos[c].path,
                                        children: [],
                                        original:  this.todosLosArchivos[c].carpetaOriginal,
                                        isFolder : true,
                                        nivel: 3,
                                        select : false
                                    })
                                }
                            }
                        }
                    }
                    //tercer push de carpetas
                    for (var a = 0; a <  this.carpetas.length; a++) {
                        for(var b = 0; b <  this.carpetas[a].children.length; b++) {
                            for(var c = 0; c <  this.carpetas[a].children[b].children.length; c++) {
                                for (var d = 0; d < this.todosLosArchivos.length; d++) {
                                    if (this.carpetas[a].children[b].children[c].name == this.todosLosArchivos[d].parentPholder  &&
                                        this.todosLosArchivos[d].path == this.carpetas[a].children[b].children[c].path + '/' + this.todosLosArchivos[d].carpetaOriginal) {
                                        this.carpetas[a].children[b].children[c].children.push({
                                            id : Math.random().toString(36).substring(7),
                                            name:  this.todosLosArchivos[d].nombreCarpeta,
                                            parentPholder: this.todosLosArchivos[d].parentPholder,
                                            path: this.todosLosArchivos[d].path,
                                            children: [],
                                            original:  this.todosLosArchivos[d].carpetaOriginal,
                                            isFolder : true,
                                            nivel: 4,
                                            select : false
                                        })
                                    }
                                }
                            }
                        }
                    }
                    //cuarto push de carpetas
                    for (var a = 0; a <  this.carpetas.length; a++) {
                        for(var b = 0; b <  this.carpetas[a].children.length; b++) {
                            for(var c = 0; c <  this.carpetas[a].children[b].children.length; c++) {
                                for(var d = 0; d <  this.carpetas[a].children[b].children[c].children.length; d++) {
                                    for (var e = 0; e < this.todosLosArchivos.length; e++) {
                                        if (this.carpetas[a].children[b].children[c].children[d].name == this.todosLosArchivos[e].parentPholder  &&
                                            this.todosLosArchivos[e].path == this.carpetas[a].children[b].children[c].children[d].path + '/' + this.todosLosArchivos[e].carpetaOriginal) {
                                            this.carpetas[a].children[b].children[c].children[d].children.push({
                                                id : Math.random().toString(36).substring(7),
                                                name:  this.todosLosArchivos[e].nombreCarpeta,
                                                parentPholder: this.todosLosArchivos[e].parentPholder,
                                                path: this.todosLosArchivos[e].path,
                                                children: [],
                                                original:  this.todosLosArchivos[e].carpetaOriginal,
                                                isFolder : true,
                                                nivel: 5,
                                                select : false
                                            })
                                        }
                                    }
                                }
                            }
                        }
                    }
                    //quinto push de carpetas
                    for (var a = 0; a <  this.carpetas.length; a++) {
                        for(var b = 0; b <  this.carpetas[a].children.length; b++) {
                            for(var c = 0; c <  this.carpetas[a].children[b].children.length; c++) {
                                for(var d = 0; d <  this.carpetas[a].children[b].children[c].children.length; d++) {
                                    for(var e = 0; e <  this.carpetas[a].children[b].children[c].children[d].children.length; e++) {
                                        for (var f = 0; f < this.todosLosArchivos.length; f++) {
                                            if (this.carpetas[a].children[b].children[c].children[d].children[e].name == this.todosLosArchivos[f].parentPholder  &&
                                                this.todosLosArchivos[f].path == this.carpetas[a].children[b].children[c].children[d].children[e].path + '/' + this.todosLosArchivos[f].carpetaOriginal) {
                                                this.carpetas[a].children[b].children[c].children[d].children[e].children.push({
                                                    id : Math.random().toString(36).substring(7),
                                                    name:  this.todosLosArchivos[f].nombreCarpeta,
                                                    parentPholder: this.todosLosArchivos[f].parentPholder,
                                                    path: this.todosLosArchivos[f].path,
                                                    children: [],
                                                    original:  this.todosLosArchivos[f].carpetaOriginal,
                                                    isFolder : true,
                                                    nivel: 6,
                                                    select : false
                                                })
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    //sexto push de carpetas
                    for (var a = 0; a <  this.carpetas.length; a++) {
                        for(var b = 0; b <  this.carpetas[a].children.length; b++) {
                            for(var c = 0; c <  this.carpetas[a].children[b].children.length; c++) {
                                for(var d = 0; d <  this.carpetas[a].children[b].children[c].children.length; d++) {
                                    for(var e = 0; e <  this.carpetas[a].children[b].children[c].children[d].children.length; e++) {
                                        for(var f = 0; f <  this.carpetas[a].children[b].children[c].children[d].children[e].children.length; f++) {
                                            for (var g = 0; g < this.todosLosArchivos.length; g++) {
                                                if (this.carpetas[a].children[b].children[c].children[d].children[e].children[f].name == this.todosLosArchivos[g].parentPholder  &&
                                                    this.todosLosArchivos[g].path == this.carpetas[a].children[b].children[c].children[d].children[e].children[f].path + '/' + this.todosLosArchivos[g].carpetaOriginal) {
                                                    this.carpetas[a].children[b].children[c].children[d].children[e].children[f].children.push({
                                                        id : Math.random().toString(36).substring(7),
                                                        name:  this.todosLosArchivos[g].nombreCarpeta,
                                                        parentPholder: this.todosLosArchivos[g].parentPholder,
                                                        path: this.todosLosArchivos[g].path,
                                                        children: [],
                                                        original:  this.todosLosArchivos[g].carpetaOriginal,
                                                        isFolder : true,
                                                        nivel: 7,
                                                        select : false
                                                    })
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }

                    //  //septimo push de carpetas
                    // for (var a = 0; a <  this.carpetas.length; a++) {
                    //     for(var b = 0; b <  this.carpetas[a].children.length; b++) {
                    //         for(var c = 0; c <  this.carpetas[a].children[b].children.length; c++) {
                    //             for(var d = 0; d <  this.carpetas[a].children[b].children[c].children.length; d++) {
                    //                 for(var e = 0; e <  this.carpetas[a].children[b].children[c].children[d].children.length; e++) {
                    //                     for(var f = 0; f <  this.carpetas[a].children[b].children[c].children[d].children[f].children.length; f++) {
                    //                         for(var g = 0; g <  this.carpetas[a].children[b].children[c].children[d].children[f].children.length; g++) {
                    //                             for (var h = 0; h < this.todosLosArchivos.length; h++) {
                    //                                 if (this.carpetas[a].children[b].children[c].children[d].children[e].children[f].children[g].name == this.todosLosArchivos[e].parentPholder  &&
                    //                                     this.todosLosArchivos[h].path == this.carpetas[a].children[b].children[c].children[d].children[e].children[f].children[g].path + '/' + this.todosLosArchivos[h].carpetaOriginal) {
                    //                                     this.carpetas[a].children[b].children[c].children[d].children[e].children[f].children[g].children.push({
                    //                                         id : Math.random().toString(36).substring(7),
                    //                                         name:  this.todosLosArchivos[h].nombreCarpeta,
                    //                                         parentPholder: this.todosLosArchivos[h].parentPholder,
                    //                                         path: this.todosLosArchivos[h].path,
                    //                                         children: [],
                    //                                         original:  this.todosLosArchivos[h].carpetaOriginal,
                    //                                         isFolder : true,
                    //                                         nivel: 8,
                    //                                         // select : false
                    //                                     })
                    //                                 }
                    //                             }
                    //                         }
                    //                     }
                    //                 }
                    //             }
                    //         }
                    //     }
                    // }

                    //Primer push archivos
                    for (var k = 0; k < this.carpetas.length; k++) {
                        for (var m = 0; m < this.todosLosArchivos.length; m++) {
                            for (var b = 0; b < this.todosLosArchivos[m].href.length; b++) {
                                var splitPiso = this.todosLosArchivos[m].href[b].split('_')
                                var format = this.todosLosArchivos[m].href[b].split('.')
                                var file = ''
                                if (splitPiso[0] == 'FACTURA') {
                                    file = this.todosLosArchivos[m].href[b]
                                }else {
                                    file = splitPiso[0] + '.' + format[1]
                                }
                                if(this.todosLosArchivos[m].parentPholder == null && this.todosLosArchivos[m].nombreCarpeta == this.carpetas[k].name){
                                    this.carpetas[k].children.push({
                                        idtoken : Math.random().toString(36).substring(7),
                                        name: this.todosLosArchivos[m].href[b],
                                        parentPholder: this.todosLosArchivos[m].parentPholder,
                                        newName: file,
                                        children: [],
                                        path: this.todosLosArchivos[m].path,
                                        original:  this.todosLosArchivos[m].carpetaOriginal,
                                        file: format[1],
                                        isFolder : false,
                                        select : false
                                    })
                                }
                            }
                        }
                    }

                    //Segundo push de archivos
                    for (var k = 0; k < this.carpetas.length; k++) {
                        for (var v = 0; v <  this.carpetas[k].children.length; v++) {
                           for (var m = 0; m < this.todosLosArchivos.length; m++) {
                                for (var b = 0; b < this.todosLosArchivos[m].href.length; b++) {
                                    var splitPiso = this.todosLosArchivos[m].href[b].split('_')
                                    var format = this.todosLosArchivos[m].href[b].split('.')
                                    var file = ''
                                    if (splitPiso[0] == 'FACTURA') {
                                        file = this.todosLosArchivos[m].href[b]
                                    }else {
                                        file = splitPiso[0] + '.' + format[1]
                                    }
                                    if(this.carpetas[k].children[v].name == this.todosLosArchivos[m].nombreCarpeta  &&
                                        this.todosLosArchivos[m].path == this.carpetas[k].children[v].path ){

                                        this.carpetas[k].children[v].children.push({
                                            idtoken : Math.random().toString(36).substring(7),
                                            name: this.todosLosArchivos[m].href[b],
                                            parentPholder: this.todosLosArchivos[m].parentPholder,
                                            newName: file,
                                            children: [],
                                            path: this.todosLosArchivos[m].path,
                                            original:  this.todosLosArchivos[m].carpetaOriginal,
                                            file: format[1],
                                            isFolder : false,
                                            select : false
                                        })
                                    }
                                }
                            }
                        }
                    }

                    //tercer push de archivos
                    for (var k = 0; k < this.carpetas.length; k++) {
                        for (var v = 0; v <  this.carpetas[k].children.length; v++) {
                            for (var z = 0; z <  this.carpetas[k].children[v].children.length; z++) {

                                for (var m = 0; m < this.todosLosArchivos.length; m++) {
                                    for (var b = 0; b < this.todosLosArchivos[m].href.length; b++) {
                                        var splitPiso = this.todosLosArchivos[m].href[b].split('_')
                                        var format = this.todosLosArchivos[m].href[b].split('.')
                                        var file = ''
                                        if (splitPiso[0] == 'FACTURA') {
                                            file = this.todosLosArchivos[m].href[b]
                                        }else {
                                            file = splitPiso[0] + '.' + format[1]
                                        }
                                        if(this.carpetas[k].children[v].children[z].name == this.todosLosArchivos[m].nombreCarpeta  &&
                                            this.todosLosArchivos[m].path == this.carpetas[k].children[v].children[z].path ){
                                           
                                            this.carpetas[k].children[v].children[z].children.push({
                                                idtoken : Math.random().toString(36).substring(7),
                                                name: this.todosLosArchivos[m].href[b],
                                                parentPholder: this.todosLosArchivos[m].parentPholder,
                                                newName: file,
                                                children: [],
                                                path: this.todosLosArchivos[m].path,
                                                original:  this.todosLosArchivos[m].carpetaOriginal,
                                                file: format[1],
                                                isFolder : false,
                                                select : false
                                            })
                                        }
                                    }
                                }
                            }
                        }
                    }

                     //cuarto push de archivos
                    for (var k = 0; k < this.carpetas.length; k++) {
                        for (var v = 0; v <  this.carpetas[k].children.length; v++) {
                            for (var z = 0; z <  this.carpetas[k].children[v].children.length; z++) {
                                for (var aa = 0; aa <  this.carpetas[k].children[v].children[z].children.length; aa++) {
                                    for (var m = 0; m < this.todosLosArchivos.length; m++) {
                                        for (var b = 0; b < this.todosLosArchivos[m].href.length; b++) {
                                            var splitPiso = this.todosLosArchivos[m].href[b].split('_')
                                            var format = this.todosLosArchivos[m].href[b].split('.')
                                            var file = ''
                                            if (splitPiso[0] == 'FACTURA') {
                                                file = this.todosLosArchivos[m].href[b]
                                            }else {
                                                file = splitPiso[0] + '.' + format[1]
                                            }
                                            if(this.carpetas[k].children[v].children[z].children[aa].name == this.todosLosArchivos[m].nombreCarpeta  &&
                                                this.todosLosArchivos[m].path == this.carpetas[k].children[v].children[z].children[aa].path ){
                                               
                                                this.carpetas[k].children[v].children[z].children[aa].children.push({
                                                    idtoken : Math.random().toString(36).substring(7),
                                                    name: this.todosLosArchivos[m].href[b],
                                                    parentPholder: this.todosLosArchivos[m].parentPholder,
                                                    newName: file,
                                                    children: [],
                                                    path: this.todosLosArchivos[m].path,
                                                    original:  this.todosLosArchivos[m].carpetaOriginal,
                                                    file: format[1],
                                                    isFolder : false,
                                                    select : false
                                                })
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }

                    //quinto push de archivos
                    for (var k = 0; k < this.carpetas.length; k++) {
                        for (var v = 0; v <  this.carpetas[k].children.length; v++) {
                            for (var z = 0; z <  this.carpetas[k].children[v].children.length; z++) {
                                for (var aa = 0; aa <  this.carpetas[k].children[v].children[z].children.length; aa++) {
                                    for (var bb = 0; bb <  this.carpetas[k].children[v].children[z].children[aa].children.length; bb++) {
                                       for (var m = 0; m < this.todosLosArchivos.length; m++) {
                                            for (var b = 0; b < this.todosLosArchivos[m].href.length; b++) {
                                                var splitPiso = this.todosLosArchivos[m].href[b].split('_')
                                                var format = this.todosLosArchivos[m].href[b].split('.')
                                                var file = ''
                                                if (splitPiso[0] == 'FACTURA') {
                                                    file = this.todosLosArchivos[m].href[b]
                                                }else {
                                                    file = splitPiso[0] + '.' + format[1]
                                                }
                                                if(this.carpetas[k].children[v].children[z].children[aa].children[bb].name == this.todosLosArchivos[m].nombreCarpeta  &&
                                                    this.todosLosArchivos[m].path == this.carpetas[k].children[v].children[z].children[aa].children[bb].path ){
                                                    this.carpetas[k].children[v].children[z].children[aa].children[bb].children.push({
                                                        idtoken : Math.random().toString(36).substring(7),
                                                        name: this.todosLosArchivos[m].href[b],
                                                        parentPholder: this.todosLosArchivos[m].parentPholder,
                                                        newName: file,
                                                        children: [],
                                                        path: this.todosLosArchivos[m].path,
                                                        original:  this.todosLosArchivos[m].carpetaOriginal,
                                                        file: format[1],
                                                        isFolder : false,

                                                        select : false
                                                    })
                                                }
                                            }
                                        } 
                                    }
                                }
                            }
                        }
                    }

                    //sexto push de archivos
                    for (var k = 0; k < this.carpetas.length; k++) {
                        for (var v = 0; v <  this.carpetas[k].children.length; v++) {
                            for (var z = 0; z <  this.carpetas[k].children[v].children.length; z++) {
                                for (var aa = 0; aa <  this.carpetas[k].children[v].children[z].children.length; aa++) {
                                    for (var bb = 0; bb <  this.carpetas[k].children[v].children[z].children[aa].children.length; bb++) {
                                        for (var cc = 0; cc <  this.carpetas[k].children[v].children[z].children[aa].children[bb].children.length; cc++) {
                                            for (var m = 0; m < this.todosLosArchivos.length; m++) {
                                                for (var b = 0; b < this.todosLosArchivos[m].href.length; b++) {
                                                    var splitPiso = this.todosLosArchivos[m].href[b].split('_')
                                                    var format = this.todosLosArchivos[m].href[b].split('.')
                                                    var file = ''
                                                    if (splitPiso[0] == 'FACTURA') {
                                                        file = this.todosLosArchivos[m].href[b]
                                                    }else {
                                                        file = splitPiso[0] + '.' + format[1]
                                                    }
                                                    if(this.carpetas[k].children[v].children[z].children[aa].children[bb].children[cc].name == this.todosLosArchivos[m].nombreCarpeta  &&
                                                        this.todosLosArchivos[m].path == this.carpetas[k].children[v].children[z].children[aa].children[bb].children[cc].path ){
                                                        this.carpetas[k].children[v].children[z].children[aa].children[bb].children[cc].children.push({
                                                            idtoken : Math.random().toString(36).substring(7),
                                                            name: this.todosLosArchivos[m].href[b],
                                                            parentPholder: this.todosLosArchivos[m].parentPholder,
                                                            newName: file,
                                                            children: [],
                                                            path: this.todosLosArchivos[m].path,
                                                            original:  this.todosLosArchivos[m].carpetaOriginal,
                                                            file: format[1],
                                                            isFolder : false,
                                                            select : false
                                                        })
                                                    }
                                                }
                                            } 
                                        }
                                    }
                                }
                            }
                        }
                    }

                    //septimo push de archivos
                    for (var k = 0; k < this.carpetas.length; k++) {
                        for (var v = 0; v <  this.carpetas[k].children.length; v++) {
                            for (var z = 0; z <  this.carpetas[k].children[v].children.length; z++) {
                                for (var aa = 0; aa <  this.carpetas[k].children[v].children[z].children.length; aa++) {
                                    for (var bb = 0; bb <  this.carpetas[k].children[v].children[z].children[aa].children.length; bb++) {
                                        for (var cc = 0; cc <  this.carpetas[k].children[v].children[z].children[aa].children[bb].children.length; cc++) {
                                            for (var dd = 0; dd <  this.carpetas[k].children[v].children[z].children[aa].children[bb].children[cc].children.length; dd++) {
                                                for (var m = 0; m < this.todosLosArchivos.length; m++) {
                                                    for (var b = 0; b < this.todosLosArchivos[m].href.length; b++) {
                                                        var splitPiso = this.todosLosArchivos[m].href[b].split('_')
                                                        var format = this.todosLosArchivos[m].href[b].split('.')
                                                        var file = ''
                                                        if (splitPiso[0] == 'FACTURA') {
                                                            file = this.todosLosArchivos[m].href[b]
                                                        }else {
                                                            file = splitPiso[0] + '.' + format[1]
                                                        }
                                                        if(this.carpetas[k].children[v].children[z].children[aa].children[bb].children[cc].children[dd].name == this.todosLosArchivos[m].nombreCarpeta  &&
                                                            this.todosLosArchivos[m].path == this.carpetas[k].children[v].children[z].children[aa].children[bb].children[cc].children[dd].path ){
                                                           
                                                            this.carpetas[k].children[v].children[z].children[aa].children[bb].children[cc].children[dd].children.push({
                                                                idtoken : Math.random().toString(36).substring(7),
                                                                name: this.todosLosArchivos[m].href[b],
                                                                parentPholder: this.todosLosArchivos[m].parentPholder,
                                                                newName: file,
                                                                children: [],
                                                                path: this.todosLosArchivos[m].path,
                                                                original:  this.todosLosArchivos[m].carpetaOriginal,
                                                                file: format[1],
                                                                isFolder : false,
                                                                select : false
                                                            })
                                                        }
                                                    }
                                                } 
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    ///////////////asta aqui funciona bien#################################################################################
                }, err => {
                    this.$toast.error('Error consultando documentos')
                })
            },
            reload(){
                this.getDocumentos()
            }
        },
    };
</script>

<style>
    .v-treeview-node.v-treeview-node--rounded .v-treeview-node__root {
        margin-top: 0px;
        margin-bottom: 0px;
       /* cursor: pointer;*/
    }
</style>