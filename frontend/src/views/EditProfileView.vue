<template>
  <div>
    <Header />
    <div id="profile-box">
      <div class="profile-info-box">
        <div class="profile-section">
          <div class="profile-pic-container">
            <!-- Profile picture content here -->
          </div>
        </div>
        <div class="profile-details">
          <input type="text" class="change-username" v-model="profileData.username" placeholder="Change Username">
          <textarea class="bio-box" rows="5" cols="50" v-model="profileData.bio" placeholder="Bio"></textarea>
        </div>
      </div>
      <button class="save-button" @click="updateProfile">Save Changes</button>
      <!-- Conditionally render the button based on user ID -->
      <button v-if="profileData.id === 1" class="edit-users-button" @click="navigateToUserManagement">Edit Users adminam</button>
      <button class="delete-account-button" @click="deleteProfile">Delete Account</button>
    </div>
  </div>
</template>

<script>
import Header from '../components/Header.vue'
import axios from 'axios'

export default {
  name: "EditProfileView",
  components: {
    Header,
  },
  data() {
    return {
      profileData: {},
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
    fetchProfile(authToken){
      axios.get('/api/getUserProfile', {
        headers: {
          Authorization: `Bearer ${authToken}`
        }
      }).then((response) => {
        this.profileData = response.data.userData;
      }).catch((error) => {
        console.log(error.message);
      });
    },
    updateProfile() {
      const authToken = localStorage.getItem('authToken');
      if (!authToken) {
        console.error('Authentication token not found.');
        return;
      }
      axios.post('/api/updateProfile', {
        bio: this.profileData.bio,
        username: this.profileData.username
      }, {
        headers: {
          Authorization: `Bearer ${authToken}`
        }
      }).then(response => {
        alert("Changes saved");
        this.$router.push('/profile');
      }).catch(error => {
        console.error(error);
      });
    },
    deleteProfile(){
      const authToken = localStorage.getItem('authToken');
      localStorage.removeItem('authToken');
      axios.delete('/api/deleteProfile').then(response => {
        console.log(response.data.message);
        alert('Account deleted successfully');
        this.$router.push('/login');
      }).catch(error => {
        console.error('Error deleting profile:', error);
        alert('Problem deleting profile');
      });
    },
    navigateToUserManagement() {
      this.$router.push('/UserManagementView');
    }
  },
};
</script>
<style scoped>
#profile-box {
  background-color: #373737;
  width: 90%;
  max-width: 600px;
  height: auto; /* Set height to auto */
  max-height: 80vh; /* Set maximum height */
  padding: 20px;
  border-radius: 20px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  margin: auto;
  box-sizing: border-box;
  overflow-y: scroll; /* Make profile box scrollable */
}

.profile-info-box {
  width: 100%;
  display: flex;
  flex-direction: column;
  align-items: center;
  margin-bottom: 20px;
}

.profile-section {
  display: flex;
  justify-content: center;
  margin-bottom: 20px;
}

.profile-pic-container {
  height: 0;
  width: 150px;
  padding-top: 150px; /* Maintain a 1:1 aspect ratio */
  border: 2px solid white;
  border-radius: 50%;
  background-color: none;
  margin-right: 20px;
  overflow: hidden; /* Hide overflow to prevent content distortion */
}

.profile-details {
  display: flex;
  flex-direction: column;
  width: 100%;
  align-items: center;
}

.change-username,
.bio-box {
  width: 80%;
  max-width: 400px;
  border-radius: 25px;
  border: 1px solid var(--color-text); /* Add thin border */
  font-size: 16px;
  color: var(--color-text);
  background-color: var(--color-light-dark-red);
  margin-bottom: 20px;
  padding: 10px;
  box-sizing: border-box;
}

.save-button,
.edit-users-button,
.delete-account-button {
  height: 40px;
  width: 80%;
  max-width: 300px;
  border: 1px solid var(--color-text); /* Add thin border */
  border-radius: 25px;
  background-color: var(--color-red);
  color: var(--color-text);
  font: "Inter";
  font-size: 16px;
  font-weight: 550;
  margin-top: 20px;
  align-self: center;
  text-align: center;
}

.delete-account-button {
  color: red;
}

@media (min-width: 768px) {
  #profile-box {
    width: 70%;
    max-width: 600px;
    height: auto;
    padding: 40px;
    border-radius: 50px;
  }

  .profile-info-box {
    flex-direction: row;
    align-items: center;
  }

  .profile-section {
    margin-right: 20px;
  }

  .profile-pic-container {
    height: 150px;
    width: 150px;
    margin-right: 20px;
  }

  .profile-details {
    align-items: flex-start;
    width: auto;
  }

  .change-username,
  .bio-box {
    width: 100%;
    max-width: none;
    font-size: 20px;
  }

  .save-button,
  .edit-users-button,
  .delete-account-button {
    width: 150px;
    max-width: none;
    margin-left: 0;
    margin-right: 20px;
  }
}
</style>
