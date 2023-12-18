export const date_mixin = {
  filters: {
      format_date_filter(created_at) {
          return moment(created_at).format('DD-MM-Y')
      },
      
    
  }
}
