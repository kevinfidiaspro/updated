<template>  
    <v-dialog v-model="dialogEditTask" transition="dialog-top-transition" max-width="50%" persistent>
      <v-card v-if="dialogEditTask">
        <v-card-title class="headline">
               <h4>{{captureTask.task.titulo}}</h4>
        </v-card-title>
        <v-card-text>
          <v-row>
            <v-col cols="12" xm="12" sm="12" md="12" lg="12" xl="12" class="pa-1">
                <label class="ml-1">Colaboradores</label>
                <span v-for="admin,i in seleccionados" :key="i">
                  <v-tooltip left>
                    <template v-slot:activator="{ on, attrs }">
                      <v-btn  icon rounded v-bind="attrs" v-on="on"> {{admin.name.charAt(0)}} </v-btn>
                    </template>
                    <span> <span> {{ admin.name}} , {{admin.email}} </span> </span>
                  </v-tooltip>
                </span>                
            </v-col>
            <v-col cols="12" xm="12" sm="12" md="6" lg="6" xl="6" class="pa-2">
              <v-text-field v-model="captureTask.task.titulo" label="Título" :counter="50"></v-text-field>
            </v-col>
            <v-col cols="12" xm="12" sm="12" md="12" lg="12" xl="12" class="pa-2">
                <label>Descripción</label>
                <ckeditor style="width:100%; heigth:400px" :editor="editor"
                  v-model="captureTask.task.descripcion" :config="editorConfig">
                </ckeditor>
            </v-col>
            <v-col cols="12" xm="12" sm="12" md="12" lg="12" xl="12" class="pa-2">                
                <v-select v-model="selectAdmin" item-text="name" :items="administradores"
                  label="Asignar tarea a" return-object multiple>
                <template v-slot:prepend-item>
                  <v-list-item ripple @click="toggle">
                    <v-list-item-action>
                      <v-icon :color="selectAdmin.length > 0 ? 'indigo darken-4' : ''">
                        {{ icon }}
                      </v-icon>
                    </v-list-item-action>
                    <v-list-item-content>
                      <v-list-item-title> Seleccionar a todos </v-list-item-title>
                    </v-list-item-content>
                  </v-list-item>
                  <v-divider class="mt-2"></v-divider>
                </template>
              </v-select>
              <!-- <span style="position:absolute; opacity:0">{{seleccionados}}</span> -->
            </v-col>
          </v-row>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
            <v-btn x-small color="success" @click="updateTask">
              <v-icon color="white">mdi-refresh</v-icon>Actualizar
            </v-btn>
            <v-btn x-small color="red" class="white--text mx-2" @click="closeDialogUpdateTask()">
                <v-icon color="white">mdi-close</v-icon> Cancelar
            </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>  
</template>

<script>
 import ClassicEditor from '@ckeditor/ckeditor5-build-classic';
export default {
     props: {
           dialogEditTask: Boolean,
           closeDialogUpdateTask: Function,
           captureTask: Object,
           captureUriId: Number,
           administradores: Array,
           tareasAminis: Array,
     },
     data () {
          return {
               selectAdmin: [],
               editor: ClassicEditor,
               editorData: '<p>Escriba la descrición para esta tarea.</p>',
               editorConfig: {
                    toolbar: {
                          items: [
                              'heading',
                              'bold',
                              'italic',
                              'bulletedList',
                              'numberedList',
                              'link',
                              'inserttable',
                              // 'imageInsert',
                          ],
                    },
                    height: '500px'
                 // extraPlugins: null,
               },               
          }
     },
     computed:{
          likesAllFruit () {
            return this.selectAdmin.length === this.administradores.length
          },

          likesSomeFruit () {
            return this.selectAdmin.length > 0 && !this.likesAllFruit
          },

          icon () {
            if (this.likesAllFruit) return 'mdi-close-box'
            if (this.likesSomeFruit) return 'mdi-minus-box'
            return 'mdi-checkbox-blank-outline'
          },

          administradoresComputed(){
            let array = []
            for (var i = 0; i < this.tareasAminis.length; i++) {
              if (this.tareasAminis[i].tarea_id == this.captureTask.task.id) {
                for (var n = 0; n < this.administradores.length; n++) {
                  if (this.administradores[n].id == this.tareasAminis[i].user_id) {
                      array[array.length] = this.administradores[n]
                  }
                }                 
              }
            }
            return array
          },

          seleccionados (){
            return this.selectAdmin = this.administradoresComputed
          }
     },

     mounted(){ },
     methods:{
      toggle () {
        this.$nextTick(() => {
          if (this.likesAllFruit) {
            this.selectAdmin = []
          } else {
            this.selectAdmin = this.administradores.slice()
          }
        })
      },

      updateTask(){
        let obj;
        obj = {
            'task' : this.captureTask.task,
            'user_id': this.captureUriId,
            'draggable': this.captureTask.dragg,
            'administradores': this.selectAdmin
        }
        axios.put(`api/update-tasks/`+this.captureTask.task.id, obj).then(res => {
          if(res.status*1 == 200){
                this.$toast.sucs('Tarea Actualizada')
                // this.list.unshift(res.data.task)
                this.closeDialogUpdateTask()
          }    
        }, err => {
            this.$toast.error('Error al Actualizar tarea')
        })
      }
    }
};
</script>