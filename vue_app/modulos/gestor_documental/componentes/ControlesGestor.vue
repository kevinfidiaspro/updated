<template>
    <div>
        <v-system-bar window>
        <span class="mr-4"
            v-if="tree.length == 1 && tree[0].isFolder" style="cursor:pointer" @click="openDialogCrearCarpeta()">
            <v-icon style="cursor:pointer" color="amber accent-4">mdi-folder-plus </v-icon> Carpeta Nueva
        </span>
        <span 
            v-if="tree.length == 1 && tree[0].isFolder" style="cursor:pointer" @click="openDialogUploadFile()">
            <v-icon style="cursor:pointer" color="blue">mdi-cloud-upload</v-icon> Cargar Archivo
        </span>        
        <v-spacer></v-spacer>
        <a v-if="localStorageRole == 1"
            :href="`#/gestor-documentos/`+localStorageUser" class="action-buttons mr-4">
            <span style="cursor:pointer">
                <v-icon color="black">mdi-arrow-left</v-icon>Atras
            </span>
        </a>        
        <span class="mr-4"
            style="cursor:pointer" @click="reload()">
            <v-icon color="black">mdi-reload</v-icon>Actualizar
        </span>
        <a :href="'#/visor-documentos/'+captureUriId + '/' +tree[0].name + '/' +tree[0].file + '/' + encodeURIComponent(tree[0].path)" 
            v-if="tree.length == 1 && !tree[0].isFolder" class="mr-2">
            <span style="cursor:pointer">
                <v-icon color="black">mdi-eye</v-icon> Ver
            </span>
        </a>
        <span class="mr-4"
            v-if="tree.length>0" style="cursor:pointer" @click="modalDeleteFile()">
            <v-icon color="red" class="mx-1">mdi-delete</v-icon> Eliminar
        </span>
        <span class="mr-4"  
            v-if="tree.length>0 " style="cursor:pointer" @click="downloadMultiple()"> 
            <v-icon color="blue-grey">mdi-cloud-download</v-icon>Descargar
        </span>
        </v-system-bar>
    </div>
</template>
<script>
    export default {
        props: {
            dialogCrearCarpeta:Boolean,
            fileOrDiretoryOptions: Boolean,
            reload: Function,
            openDialogCrearCarpeta:  Function,
            modalDeleteFile: Function,
            openDialogUploadFile: Function,
            captureUriId: Number,
            tree:Array           
        },

        data() {
            return {
            download : '',
            fileNameA :''
            }
        },

        computed:{
        localStorageRole(){
            return localStorage.role
        },

        localStorageUser(){
            return localStorage.user_id
        },
        },

        created() {          
        },

        methods:{
            downloadFiles(url, filename) {
                fetch(url).then(function(t) {
                    return t.blob().then((b)=>{
                            var a = document.createElement("a");
                            a.href = URL.createObjectURL(b);
                            a.setAttribute("download", filename);
                            a.click();
                        }
                    );
                });
            },        

            downloadMultiple(){               
                let arrayChildrens = []
                let saveArrayDoc = []
                for (var i = 0; i < this.tree.length; i++) {
                    let obtainPath = ''
                    if (this.tree[i].isFolder == true) {
                        for (var n = 0; n < this.tree[i].children.length; n++) {
                            if (this.tree[i].children[n].isFolder == false) {
                            arrayChildrens[arrayChildrens.length] = this.tree[i].children[n].name
                            }
                        }
                        var pathFolder = this.tree[i].path.split('/')
                        for (var i = 0; i < pathFolder.length; i++) {
                            if(pathFolder[i] == 'storage'){
                                for (var n = i; n < pathFolder.length; n++) {
                                    if(pathFolder[n] == 'app' || pathFolder[n] == 'public'){
                                    }else{
                                        obtainPath = obtainPath + pathFolder[n] + '/'
                                    }
                                }
                            }
                        }
                        for (var k = 0; k < arrayChildrens.length; k++) {
                            var downloadPath = obtainPath + arrayChildrens[k]
                            console.log(downloadPath)
                            this.downloadFiles(downloadPath, arrayChildrens[k])  
                        }
                        break
                    }
                    if (this.tree[i].isFolder == false) {
                        var dowPath = ''
                        var splitPathDecode = this.tree[i].path.split('/')
                        for (var ii = 0; ii < splitPathDecode.length; ii++) {
                            if(splitPathDecode[ii] == 'storage'){
                                for (var t = ii; t < splitPathDecode.length; t++) {
                                    if(splitPathDecode[t] == 'app' || splitPathDecode[t] == 'public'){
                                    }else{
                                        dowPath = dowPath + splitPathDecode[t] + '/'
                                    }
                                }
                            }
                        }
                        this.downloadFiles(dowPath + '/' +this.tree[i].name, this.tree[i].name) 
                    }
                }
            }
        }
    };    
</script>