<template>
    <span>{{ current_value }}</span>
</template>

<script>
    export default {
        props: ["resourceName", "field"],

        data() {
            return {
                current_value: "-",
            };
        },

        /**
         * Mount the component.
         */
        mounted() {
            this.current_value = this.field.value.join(", ");
        },
    };
</script>
