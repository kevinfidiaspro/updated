<template>
    <v-container>
        <v-row color="grey lighten-4">
            <v-col v-for="item in computedheaders" :key="item.text" cols="12" md="4">
                <div class="my-card">
                    <v-card :to="{path: item.path}" class="mx-auto justify-center">
                        <v-card-title class="justify-center">
                            <span class="title text-uppercase">{{item.text}}</span>
                        </v-card-title>
                        <v-card-text class="headline font-weight-bold justify-center text-center">
                            <v-icon color="blue" large center justify-center class="justify-center">
                                {{item.icon}}
                            </v-icon>
                        </v-card-text>
                    </v-card>
                </div>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
    import {
        menu_items_mixin
    } from '../../global_mixins/menu_items_mixin'

    export default {
        mixins: [menu_items_mixin],
        mounted(){
            axios.get(`api/index-app/${localStorage.getItem('user_id')}`).then(res => {
              
            }, err => {
               
            })
        }
    }
</script>

<style>
    .my-card {
        transition: all 0.2s;
    }

    .v-card {
        transition: all 0.2s;
    }

    .my-card:hover .v-card .v-card__text i {
        color: black !important;
        font-weight: bold !important;
        transform: scale(1.1) !important;
        
    }

    .my-card:hover .v-card .v-card__title span {
        color: black !important;
        font-weight: bold !important;
        transform: scale(1.1) !important;
    }
    
</style> 