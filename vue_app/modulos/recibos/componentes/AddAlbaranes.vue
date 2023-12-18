<template>
  <v-row>
    <v-dialog v-model="modalAddAlbaranes" width="400" transition="dialog-bottom-transition" persistent> 
      <v-card class="mx-auto" style="overflow-x: hidden;">
        <v-toolbar dark color="primary">
          <v-toolbar-title><h5>Seleccione albaran para asociar a factura</h5></v-toolbar-title>
          <v-spacer></v-spacer>
        </v-toolbar>
        <v-row class="ml-2 my-2">
            <v-list-item three-line v-for="albaran,index in albaranesEnviados" :key="index">
                <v-list-item-avatar>
                  <v-checkbox :id="albaran.nro_factura" v-model="checkbox[index]" :value="albaran.nro_factura"
                    @change="valueChanged($event,albaran)">
                  </v-checkbox>
                </v-list-item-avatar>
              <v-list-item-content>                
                <v-list-item-subtitle>
                  <strong><v-icon color="green">mdi-file-document</v-icon> ALBARAN : </strong> {{albaran.nro_factura}}
                </v-list-item-subtitle> 
                <v-list-item-subtitle>                  
                  <strong><v-icon color="green">mdi-account-circle</v-icon> CLIENTE : </strong> {{albaran.cliente.nombre}}
                </v-list-item-subtitle>
                <v-list-item-subtitle>                  
                  <strong><v-icon>mdi-currency-eur</v-icon> IMPORTE : </strong> {{albaran.importe}} € 
                </v-list-item-subtitle>                 
                <v-list-item-subtitle>                  
                  <v-icon color="red">mdi-file-pdf-outline</v-icon><strong> PDF </strong>                     
                  <a target="_blank" :href="'/storage/albaranes/enviados/' +albaran.url">
                  <v-icon color="blue">mdi-magnify</v-icon> Ver </a>
                </v-list-item-subtitle>                    
              </v-list-item-content>
            </v-list-item>
        </v-row>
        <v-card-actions>
          <div class="my-2">
            <v-btn color="error" @click="closeModalAlbaranes(checkbox)">Cancelar Selección</v-btn>
            <v-btn color="success" @click="closeModalAlbaranesListo">Listo</v-btn>
          </div>            
         </v-card-actions>
      </v-card>
    </v-dialog>
  </v-row>
</template>

<script>
  export default {
    props: {
      modalAddAlbaranes: Boolean,
      closeModalAlbaranes: Function,
      albaranesEnviados: Array,
      closeModalAlbaranesListo: Function,
      addAlbaranAlaLista: Function,
      deleteItem: Function,
      obtenerServicio: Function,
      checkbox: Array
    },
    data () {
      return {
        dialog: false,
        notifications: false,
        sound: true,
        widgets: false,        
      }
    },
    methods:{
      valueChanged(event, albaran) {
        // Comprobamos si esta marcado el checkbox
        if (event === null || event.length === 0)
        { 
          let servicio = this.obtenerServicio(albaran.nro_factura)
          this.deleteItem(servicio,albaran.nro_factura)
        }
        else
        {
          this.addAlbaranAlaLista(albaran)
        }
      },
    }
  }
</script>