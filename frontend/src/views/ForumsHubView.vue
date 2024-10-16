<template>
  <div>
    <Header />
    <div class="forum-container">
      <div class="content-wrapper">
        <div class="header-section">
          <h1 id="forums-header" class="header-title" style="padding-right: 5vh;">FORUMS POSTS</h1>
          <button @click="showModal = true" class="create-post-button">Create New Post</button>

          <div class="sort-dropdown" style="padding-left: 5vh;">
            <select v-model="sortBy" @change="sortPosts">
              <option value="oldest">Oldest</option>
              <option value="newest">Newest</option>
            </select>
          </div>
        </div>

        <div class="posts-container">
          <ul class="post-list">
            <li v-for="post in sortedPosts" :key="post.id" class="post-box" @click="navigateToPost(post.id)">
              <h3>{{ post.nosaukums }}</h3>
            </li>
          </ul>
        </div>
      </div>
      
      <div v-if="showModal" class="modal">
        <div class="modal-content">
          <span class="close" @click="showModal = false">&times;</span>
          <form @submit.prevent="createNewPost">

            <div>
              <label for="title"></label>
              <input type="text" v-model="newPostTitle" placeholder="Enter title" class="modal-input" required>
            </div>
            <div>
              <label for="content"></label>
              <textarea v-model="newPostContent" placeholder="Enter content" class="modal-textarea" required></textarea>
            </div>
            <button type="submit" class="post-button">Post</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import Header from '../components/Header.vue';
import axios from 'axios';

export default {
  name: "ForumsHubView",
  components: { Header },
  data() {
    return {
      posts: [],
      sortedPosts: [],
      newPostTitle: '',
      newPostContent: '',
      sortBy: 'oldest',
      showModal: false
    };
  },
  methods: {
    async getForumPosts() {
      try {
        const response = await axios.get('/api/getAllForumPosts', {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('authToken')}`
          }
        });
        this.posts = response.data.data || [];
        this.sortPosts();
      } catch (error) {
        console.error('Error fetching forum posts:', error);
        alert('Error fetching forum posts');
      }
    },
    sortPosts() {
      if (this.sortBy === 'oldest') {
        this.sortedPosts = this.posts.slice().sort((a, b) => new Date(a.updated_at) - new Date(b.updated_at));
      } else if (this.sortBy === 'newest') {
        this.sortedPosts = this.posts.slice().sort((a, b) => new Date(b.updated_at) - new Date(a.updated_at));
      }
    },
    async fetchProfile() {
      try {
        const response = await axios.get('/api/getUserProfile', {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('authToken')}`
          }
        });
        return response.data.userData;
      } catch (error) {
        console.error('Error fetching profile:', error);
        return null;
      }
    },
    async navigateToPost(postId) {
      this.$router.push({ name: 'PostDetails', params: { postId } });
    },
    async createNewPost() {
      try {
        const profileData = await this.fetchProfile();
        if (profileData) {
          const response = await axios.post('/api/createForumPost', {
            nosaukums: this.newPostTitle,
            saturs: this.newPostContent,
            autors: profileData.id
          }, {
            headers: {
              Authorization: `Bearer ${localStorage.getItem('authToken')}`
            }
          });
          if (response.data && response.data.status) {
            alert('Post created successfully');
            this.newPostTitle = '';
            this.newPostContent = '';
            this.showModal = false;
            this.getForumPosts();
          } else {
            alert('Error creating post');
          }
        } else {
          alert('Error fetching profile data');
        }
      } catch (error) {
        console.error('Error creating post:', error);
        alert('Error creating post');
      }
    }
  },
  mounted() {
    this.getForumPosts();
  }
};
</script><style scoped>
/* Existing CSS */
.forum-container {
  margin-top: 10vh;
}

.content-wrapper {
  width: 100%;
  max-width: 1200px;
  margin: 0 auto;
  padding: 2vh;
  box-sizing: border-box;
}

.header-section {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-bottom: 2vh;
}

.header-title {
  white-space: nowrap;
}

.create-post-button {
  padding: 1vh 2vh;
  font-size: 1.6vh;
  background-color: #4CAF50; /* Green */
  border: none;
  color: white;
  border-radius: 5px;
  cursor: pointer;
  transition: background-color 0.3s;
}

.create-post-button:hover {
  background-color: #45a049; /* Darker Green */
}

.sort-dropdown select {
  padding: 0.8vh 1.6vh;
  font-size: 1.6vh;
}

.posts-container {
  padding: 0;
}

.post-list {
  padding: 0;
  list-style-type: none;
}

.post-box {
  border: 1px solid #ccc;
  padding: 1vh;
  margin-bottom: 1.5vh;
  border-radius: 0.5vh;
  cursor: pointer;
  text-align: left;
}

.modal {
  display: flex;
  justify-content: center;
  align-items: center;
  position: fixed;
  z-index: 9999; /* Ensure the modal is on top of other content */
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0,0,0,0.4);
}

.modal-content {
  background-color: #fefefe;
  margin: auto;
  padding: 2vh;
  border: 1px solid #888;
  width: 80vh;
  max-height: 80vh;
  overflow-y: auto;
  position: relative; /* Adjusted to allow proper closing */
}

.modal-input {
  width: calc(100% - 4.8vh); /* Adjusted width to accommodate the close button */
  padding: 1vh;
  margin-bottom: 2vh;
  border: 1px solid #ccc;
  border-radius: 5px;
  font-size: 1.6vh;
  box-sizing: border-box;
}

.modal-textarea {
  width: 100%;
  padding: 1vh;
  margin-bottom: 2vh;
  border: 1px solid #ccc;
  border-radius: 5px;
  font-size: 1.6vh;
  box-sizing: border-box;
}

.post-button {
  padding: 1vh 2vh;
  font-size: 1.6vh;
  background-color: #4CAF50; /* Green */
  border: none;
  color: white;
  border-radius: 5px;
  cursor: pointer;
  transition: background-color 0.3s;
}

.post-button:hover {
  background-color: #45a049; /* Darker Green */
}

.close {
  color: #aaa;
  position: absolute;
  top: 2vh;
  right: 2vh;
  font-size: 2vh;
  font-weight: bold;
}

.close:hover,
.close:focus {
  color: black;
  text-decoration: none;
  cursor: pointer;
}
</style>
