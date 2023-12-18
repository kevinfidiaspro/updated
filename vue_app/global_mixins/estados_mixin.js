export const estados_mixin = {
  created(){
    if(this.estados.length == 0){
      this.getEstados()
    }
  },

  methods:{
    getEstados(){
      axios.get(`api/get-estados`).then(res => {
          this.$store.dispatch('setEstados', res.data)
      }, res => {
         this.$toast.error('Error cargando estados')
      })
    }
  },

  computed: {
    estados() {
      return this.$store.getters.get_estados
    }
  }
}
