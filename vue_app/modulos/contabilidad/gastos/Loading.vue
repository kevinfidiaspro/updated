<template>
  <div class="text-center">
   
    <v-dialog
      v-model="dialog"
      
      persistent
      width="300">
      <v-card
        color="primary"
        dark>
        <v-card-text>

          
          Escaneando archivos ...
          <v-progress-linear
            indeterminate
            color="white"
            class="mb-0"
          ></v-progress-linear>
        </v-card-text>
      </v-card>
    </v-dialog>
  </div>
</template>


<script>
  export default {
    data () {
      return {
        dialog: true,
      }
    },

  };
</script>