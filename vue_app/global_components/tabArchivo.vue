<template id="">
    <v-card flat>
        <v-row>
            <v-col cols="12" md="10">
                <v-card class="elevation-0">
                    <v-row dense>
                        <v-col cols="12" md="8">
                            <v-file-input
                                hide-details
                                prepend-icon
                                outlined
                                label="Archivos"
                                multiple
                                @change="HandlerAdd"
                            ></v-file-input>
                        </v-col>
                    </v-row>
                    <v-row dense>
                        <v-col cols="12" md="8">
                            <v-list v-if="item.archivos.length > 0">
                                <v-list-group :value="true">
                                    <template v-slot:activator>
                                        <v-list-item-title
                                            >Archivos</v-list-item-title
                                        >
                                    </template>

                                    <v-list-item
                                        v-for="(item, i) in item.archivos"
                                        :key="i"
                                        link
                                    >
                                        <v-list-item-title>
                                            <a
                                                :href="
                                                    item.id
                                                        ? `storage/${tipo}/${item.url}`
                                                        : '#'
                                                "
                                                :target="
                                                    item.id ? '_blank' : '_self'
                                                "
                                                >{{ item.file_name }}</a
                                            >
                                            <span
                                                class="grey--text text--darken-2"
                                                >{{ item.created_at }}</span
                                            >
                                        </v-list-item-title>

                                        <v-list-item-icon>
                                            <v-icon @click="deleteFile(item)"
                                                >mdi-close-outline</v-icon
                                            >
                                        </v-list-item-icon>
                                    </v-list-item>
                                </v-list-group>
                            </v-list>
                        </v-col>
                    </v-row>
                </v-card>
            </v-col>
        </v-row>
    </v-card>
</template>

<script>
export default {
    props: ["item", "tipo"],

    methods: {
        HandlerAdd(fileList) {
            if (!fileList.length) return;
            for (const file of fileList) {
                this.item.archivos.push({
                    id: null,
                    id_item: null,
                    file_name: file.name,
                    url: null,
                    file: file,
                    created_at: moment().format("YYYY-M-DD H:m:ss"),
                });
            }
        },

        deleteFile(item) {
            if (!item.id) {
                return this.deleteFromList(item);
            }
            this.deleteFromDatabase(item);
        },

        deleteFromDatabase(item) {
            axios.get(`api/delete-file/${this.tipo}/${item.id}`).then(
                (res) => {
                    this.$toast.sucs("Archivo eliminado");
                    this.deleteFromList(item);
                },
                (res) => {
                    this.$toast.error("Error eliminando archivo");
                }
            );
        },

        deleteFromList(item) {
            return this.item.archivos.splice(
                this.item.archivos.indexOf(item),
                1
            );
        },
    },
};
</script>
