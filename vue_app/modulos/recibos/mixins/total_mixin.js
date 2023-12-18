export const total_mixin = {
  methods:{
    calcularTotales(lista_servicios) {
        let sub_total = lista_servicios.reduce((acc, servicio) => {
            return acc + parseFloat(servicio.importe)
        }, 0)
        let porcentaje_descuento = this.recibo.porcentaje_descuento
        let total_descuento = 0
        if (porcentaje_descuento > 0) {
            total_descuento = (sub_total * porcentaje_descuento) / 100
        }
        let iva = (this.recibo.has_iva == false || this.tipo == 'nota') ? 0 : parseFloat(((sub_total * 21) / 100)).toFixed(2)
        let total = parseFloat(sub_total) + parseFloat(iva) - parseFloat(total_descuento)
        this.recibo.sub_total = sub_total
        this.recibo.iva = iva
        this.recibo.total_descuento = parseFloat(total_descuento).toFixed(2)
        this.recibo.total = parseFloat(total).toFixed(2)
    }
  }
}
