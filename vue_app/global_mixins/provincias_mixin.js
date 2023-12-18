export const provincias_mixin = {
  created(){
    if(this.provincias.length == 0){
      this.getProvincias()
    }
  },

  methods:{
    getProvincias(){
      axios.get(`api/get-provincias`).then(res => {
          this.$store.dispatch('setProvincias', res.data)
      }, res => {
         this.$toast.error('Error cargando provincias')
      })
    }
  },

  computed: {
    provincias() {
      return this.$store.getters.get_provincias
    }
  }
}
