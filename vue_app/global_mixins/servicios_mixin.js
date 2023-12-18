export const servicios_mixin = {
  created(){
    if(this.servicios.length == 0){
      this.getServicios()
    }
  },

  methods:{
    getServicios(){
      axios.get(`api/get-servicios`).then(res => {
          this.$store.dispatch('setServicios', res.data)
      }, res => {
         this.$toast.error('Error cargando servicios')
      })
    }
  },

  computed: {
    servicios() {
      return this.$store.getters.get_servicios
    }
  }
}
