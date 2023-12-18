<template>
    <v-card outlined class="mx-auto">

        <v-toolbar color="blue" flat dark>
            <v-toolbar-title>
                <slot name="title"></slot>
            </v-toolbar-title>
        </v-toolbar>

        <v-container fluid>
            <slot name="content"></slot>
        </v-container>

    </v-card>
</template>

<script>
    export default {}
</script>