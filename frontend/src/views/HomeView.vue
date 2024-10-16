<template>
  <Header />
  <div class="container">
    <h1 class="title">JAUNĀ RVTGTR VERSIJA IR KLĀT!</h1>
    <h2>KAS JAUNS? ŠAJĀ VERSIJĀ IR JAUNI KLOĶĪŠI KO PAGROZĪT, KAS MAINA SKAŅU!</h2>
  </div>
</template>

<style scoped>
.container {
  display: flex;
  flex-direction: column;
  align-items: center; /* Center horizontally */
  justify-content: center; /* Center vertically */
  text-align: center; /* Center text */
}

.title {
  font-size: 10vh;
  white-space: nowrap; /* Prevent line break */
}

/* Override scrollbar hiding rules for HomeView.vue */
::v-deep ::-webkit-scrollbar {
  display: auto; /* Show scrollbar for WebKit browsers */
}
::v-deep * {
  scrollbar-width: auto; /* Show scrollbar for Firefox */
}

#app {
  max-width: 1280px;
  margin: 0 auto;
  padding: 0;
  font-weight: normal;
}

@media (min-width: 1024px) {
  body {
    display: flex;
    place-items: center;
  }
  html,
  body {
    max-width: 100%;
    overflow-x: hidden;
  }
  #app {
    display: grid;
    padding: 0 2rem;
  }
}
</style>

<script>
import Header from '../components/Header.vue'

export default {
  name: "Home",
  components:{
      Header
  }
}
</script>
