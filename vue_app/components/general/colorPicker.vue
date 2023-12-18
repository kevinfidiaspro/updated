<template>
    <v-menu
        ref="menu"
        :close-on-content-click="false"
        transition="scale-transition"
        offset-y
        min-width="auto"
    >
        <template v-slot:activator="{ on, attrs }">
            <div style="display: flex">
                <div
                    :style="`border-radius: 500000px; background-color:${color.hex};width:3rem;height:3rem ; margin:2rem`"
                    v-bind="attrs"
                    v-on="on"
                ></div>
            </div>
        </template>
        <v-color-picker v-model="color" mode="hexa">
            <v-spacer></v-spacer>
            <v-btn text color="primary" @click="menu = false"> Cancel </v-btn>
            <v-btn text color="primary" @click="$refs.menu.save(color)">
                OK
            </v-btn>
        </v-color-picker>
    </v-menu>
</template>
<script>
export default {
    props: ["label", "value", "outlined", "type"],
    data() {
        return { color: { hex: "#000000" } };
    },
    created() {
        if (this.value != null) {
            this.color.hex = this.value;
        }
    },

    watch: {
        value() {
            this.color.hex = this.value;
        },
        "color.hex"(value) {
            const me = this;
            setTimeout(() => {
                me.$emit("input", this.color.hex);
            }, 500);
        },
    },

    methods: {},
};
</script>
