<template>
  <div>
    <h2>All Users</h2>
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Username</th>
          <th>Email</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="user in users" :key="user.id">
          <td>{{ user.id }}</td>
          <td>{{ user.username }}</td>
          <td>{{ user.email }}</td>
          <td>
            <button @click="editUser(user.id)">Edit</button>
            <button @click="deleteUser(user.id)">Delete</button>
          </td>
        </tr>
      </tbody>
    </table>
    <router-link to="/EditProfileView">Go back</router-link>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      users: [],
    };
  },
  methods: {
    fetchAllUsers() {
      axios.get('/api/getAllUsers', {
        headers: {
          Authorization: `Bearer ${localStorage.getItem('authToken')}`
        }
      })
      .then(response => {
        this.users = response.data.users;
      })
      .catch(error => {
        console.error('Error fetching users:', error);
      });
    },
    deleteUser(userId) {
      axios.delete(`/api/deleteUserProfile/${userId}`, {
        headers: {
          Authorization: `Bearer ${localStorage.getItem('authToken')}`
        }
      })
        .then(response => {
          alert('User profile deleted successfully');
          this.fetchAllUsers(); // Refresh the user list after deletion
        })
        .catch(error => {
          console.error('Error deleting user profile:', error);
          alert('Problem deleting user profile');
        });
    },
    editUser(userId) {
      // Redirect to the edit user page or perform any other action for editing a user
    }
  },
  mounted() {
    this.fetchAllUsers();
  },
};
</script>

<style scoped>
/* Add your CSS styles here */
</style>
