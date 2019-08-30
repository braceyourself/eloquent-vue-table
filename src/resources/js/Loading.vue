<template>
    <div>
        <div class="title text-center" v-if="condition">
            loading
        </div>
        <slot v-else></slot>
    </div>
</template>

<script>
    export default {
        name: "Loading",
        props:{
            condition:{
                type:Boolean,
                default:false
            }
        }
    }
</script>

<style scoped>

</style>
