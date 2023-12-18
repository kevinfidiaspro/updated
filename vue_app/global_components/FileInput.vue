<template>
  <!-- accept=".svg" -->
  <div>
        <!--div class="input-group bg-primary" >
          <input :value="getFilesName()" class="form-control" type="text"  placeholder="" readonly >
          <span class="input-group-btn">

            <button v-if="files.length" class="btn btn-danger" type="button"  @click="$emit('file-clear')" >
                <i class="fa fa-ban"></i>
              </button>
              <button class="btn btn-primary" type="button" @click="showFilePicker">
                <i class="fa fa-paperclip"></i>
              </button>
          </span>
          
        </div-->
        <input type="file" ref="file"  @change="onChange" multiple>

       <!--  <spam>
          
            <img :src="getImagesP()">
        </spam>
         -->

    </div>

</template>


<script >
  export default{
    props: {
      
    },
    data:() => ({
      files: [],
      showPreview: '',
      imagePreview:[],
     
    }),
    mounted(){
       this.getImagesP()
       
    },
    methods:{

      showFilePicker(){
          this.$refs.file.click()

      },
       getImagesP(){
          let photo = (this.imagePreview.length > 100) ? '' :  this.imagePreview;
          
          return photo;
      },
      onChange(e){

        
        const filesPreview = e.currentTarget.files;

        // for(let h = 0; h<filesPreview.length; h++){

        //  if(filesPreview[h].size<=100000){
        //        Object.keys(filesPreview).forEach(i => {
        //             const file = filesPreview[i];
        //             const reader = new FileReader();
        //             reader.onload = (e) => {
        //                 this.imagePreview.push(reader.result);
        //             }
        //             this.imagePreview = []
        //             reader.readAsDataURL(file);
        //             return
        //         });
              
        // }else{
        //     alert('La imagen que está eligiendo para el producto, no puede superar los 100 kb')
        //     return
        // }     
      

        // }

        Object.keys(filesPreview).forEach(i => {
            const file = filesPreview[i];
            const reader = new FileReader();
            reader.onload = (e) => {
                this.imagePreview.push(reader.result);
            }
            this.imagePreview = []
            reader.readAsDataURL(file);
           
        });



        this.files = e.target.files[0] //subir una
        let files = e.target.files //subir varias
        this.$emit('file-change', files)
       
        
      },

      getFilesName (){
        let filesName = []

        if (this.files.length > 0) {
          for (let file of this.files) {
              filesName.push(file.name)
          }
        }

        return filesName.join(", ")

      },
      clearFiles(){
        
        this.files = []
        this.disableUploadButtonImage = true

        
       },


      
      limpiar(){
        
          const input = this.$refs.file
          input.type = 'text'
          input.type = 'file'
      }



      
    }
  }
</script>