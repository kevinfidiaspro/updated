<template>
	<v-card class="mb-4 mt-4" v-if="dragOn">
          <v-card-title>
               <v-text-field v-model="drag.drag" label="Título">
                    
               </v-text-field> 
               
          </v-card-title>
           <v-card-actions>                
               <v-btn
                   x-small
                   color="success"
                   @click="newDrag">                   
                  <v-icon color="white">mdi-plus</v-icon>Añadir
               </v-btn>
               <v-btn
                   x-small
                   color="red" class="white--text mx-2" 
                   @click="closeNewDrag()">
                   <v-icon color="white">mdi-close</v-icon> Cancelar
               </v-btn>
           </v-card-actions>
     </v-card>
</template>
<script>
    
	export default {
        props: {
        	
          drag: Object,
          dragOn: Boolean,
          dragglables: Array,
          closeNewDrag: Function,
          captureUriId: Number

        },
      	data () {
           	return {
                    show: false,
                   
                   
           	}
      	},
      	methods:{

               newDrag(){
                    if(this.drag.drag == "" || this.drag.drag == null){
                         alert("El nombre de la nueva sección no puede estar vacío")
                         return
                    }
                    // if(this.drag.drag.length > 3){
                    //      alert("El nombre de la nueva sección debe ser mayor a tres(3) caracteres")
                    //      return
                    // }
                    axios.post(`api/new-drag/`+ this.captureUriId, this.drag ).then(res => {
                         if(res.status*1 == 200){
                              let newDragResponse = res.data.drag
                             
                             
                              
                              this.dragglables.push(newDragResponse)
                              this.closeNewDrag()
                         }else {
                            this.closeNewDrag()
                         }
                    }, err => {
                         this.$toast.error('Error al guardar tareas')
                    })
               },
               
      	}
    };
</script>