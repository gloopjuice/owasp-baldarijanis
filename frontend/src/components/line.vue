<template>
<div id="line"></div>
</template>

<style>
    #line{
        height: 3px;
        background-color: white;
        border-radius: 25px;
        padding-top: 3x;
    }
</style>

<script>
import { RouterLink } from 'vue-router';

export default{
    name: "Line",
    components: { RouterLink }
};

</script>