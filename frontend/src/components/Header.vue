<template>
  <div id="Header">
    <button id="menu-but" @click="toggleMenu()">
      <svg id="menu-img" class="rotate" width="50px" height="50px" viewBox="0 0 24 24" stroke="white" fill="white" xmlns="http://www.w3.org/2000/svg">
        <path d="M3.83827 18.5097L11.1284 5.54947C11.5107 4.86982 12.4893 4.86982 12.8716 5.54947L20.1617 18.5097C20.5367 19.1763 20.055 20 19.2902 20H4.70985C3.94502 20 3.46331 19.1763 3.83827 18.5097Z" 
              stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
      </svg>
    </button>
    <RouterLink to="/home">
      <h1 id="rvtgtr-text">RVTGTR</h1>
    </RouterLink>
    <RouterLink to="/profile">
      <button id="profile-but" v-if="profileData">{{ profileData.name }}</button>
    </RouterLink>
  </div>
  <div id="sidebar" ref="sidebar" :class="{ 'show-menu': showMenu }">
    <div class="home">
      <RouterLink to="/home">
        <button class="button">Home</button>
      </RouterLink>
    </div>
    <div class="Forums">
      <RouterLink to="/forums_hub">
        <button class="button">Forums</button>
      </RouterLink>
    </div>
    <div class="DownloadPage">
      <RouterLink to="/DownloadPage">
        <button class="button">Download</button>
      </RouterLink>
    </div>
    <div class="log-out">
      <button class="button" @click="logout">Log Out</button>
    </div>
  </div>
  <div v-if="showMenu" class="overlay" @click="toggleMenu"></div>
  <div class="horizontal-line"></div>
</template>

<script>
import { RouterLink } from 'vue-router';
import axios from 'axios';

export default {
  name: "Header",
  data() {
    return {
      profileData: {},
      showMenu: false
    };
  },
  mounted() {
    const authToken = localStorage.getItem('authToken');
    if (authToken) {
      this.fetchProfile(authToken);
    } else {
      this.$router.push('/login');
    }
  },
  methods: {
    toggleMenu() {
      this.showMenu =!this.showMenu;
      const sidebar = this.$refs.sidebar;
      const menuImg = document.getElementById('menu-img');
      
      if (this.showMenu) {
        sidebar.classList.add('show-menu');
        sidebar.classList.remove('hide-menu');
        menuImg.classList.add('rotate-back');
        menuImg.classList.remove('rotate');
      } else {
        sidebar.classList.remove('show-menu');
        sidebar.classList.add('hide-menu');
        menuImg.classList.remove('rotate-back');
        menuImg.classList.add('rotate');
      }
    },
    fetchProfile(authToken) {
      axios.get('/api/getUserProfile', {
        headers: {
          Authorization: `Bearer ${authToken}`
        }
      }).then((response) => {
        this.profileData = response.data.userData;
        console.log("Profile Data:", this.profileData);
      }).catch((error) => {
        console.error(error.message);
      });
    },
    logout() {
      localStorage.removeItem('authToken');
      this.$router.push('/login');
    }
  },
  components: { RouterLink },
};
</script>

<style scoped>
.horizontal-line {
  position: fixed;
  top: 5vh;
  left: 0;
  height: 3px;
  background-color: white;
  width: 100%;
}

#Header {
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: 5vh;
  background-color: #373737;
  box-shadow: 0px 0px 100px 20px rgba(0, 0, 0, 0.5);
  text-align: center;
}

#main-content {
  margin-top: 5vh;
  background-color: white;
  min-height: calc(100vh - 5vh);
  padding: 20px;
}

#sidebar {
  height: calc(100vh - 5vh);
  width: 300px;
  background-color: var(--color-element);
  z-index: 2;
  top: 5vh;
  left: 0;
  border-bottom-right-radius: 25px;
  position: absolute;
  transform: translateX(-100%);
}

#rvtgtr-text {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  width: 150px;
  font-family: "Inter", sans-serif;
  color: #00FF29;
  font-weight: 600;
  font-size: 3.5vh;
}

#menu-but {
  position: absolute;
  top: 0;
  left: 0;
  height: 5vh;
  width: 50px;
  padding: 0;
  background: transparent;
  border: 0;
}

#menu-img {
  height: 100%;
  width: 100%;
  transition: transform 0.5s;
}

#profile-but {
  position: absolute;
  right: 10px;
  top: 0;
  height: 5vh;
  background: transparent;
  border: none;
  color: white;
  max-width: 30ch;
  text-align: left;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  font-family: "Inter", sans-serif;  
}

.show-menu {
  animation: slideIn 0.5s forwards;
}

.hide-menu {
  animation: slideOut 0.5s forwards;
}

@keyframes slideIn {
  from {
    transform: translateX(-100%);
  }
  to {
    transform: translateX(0);
  }
}

@keyframes slideOut {
  from {
    transform: translateX(0);
  }
  to {
    transform: translateX(-100%);
  }
}

.button {
  height: 100px;
  width: 100%;
  position: relative;
  color: white;
  background-color: var(--color-element);
  padding: 10px;
  text-align: left;
  border: none;
  font: 'Inter';
  font-size: 18px;
  text-align: center;
}

.rotate {
  transform: rotate(90deg);
}

.rotate-back {
  transform: rotate(0deg);
}

.overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  background-color: rgba(0, 0, 0, 0.5);
  z-index: 1;
}
</style>
