<template>
  <div>
    <Header />

    <div id="post-details-content">
      <div v-if="post">
        <!-- Return button -->
        <button class="return-link" aria-label="return to forums" @click="$router.push('/forums_hub')">Return to Forums</button>

        <!-- Edit button -->
        <button v-if="isAuthorOrAdmin" aria-label="edit post" class="return-link svg-button" @click="showEditModal = true">Edit Post</button>

        <!-- Delete button -->
        <button v-if="isAuthorOrAdmin" aria-label="delete post" class="return-link svg-button" @click="deletePost">Delete Post</button>

        <h1>{{ post.nosaukums }}</h1>
        <p>{{ post.saturs }}</p>
        <p>Autors: {{ post.authorProfile.name }}</p>
        <p>Date: {{ post.created_at.slice(0, 10) }}</p>

        <div style="margin-bottom: 2vh;"></div>

        <!-- Add Comment button -->
        <button class="return-link" aria-label="add comment" @click="showModal = true">Add Comment</button>

        <div style="margin-bottom: 2vh"></div>

        <!-- Display comments -->
        <div v-for="(comment, index) in comments" :key="index" class="comment-wrapper">
  <div>
    <p><strong>Author:</strong> {{ comment.author.name }}</p>
    <p><strong>Content:</strong> {{ comment.content }}</p>
    <p><strong>Date:</strong> {{ comment.created_at.slice(0, 10) }}</p>
    <div>
      <button @click="openEditCommentModal(comment.id)" class="edit-comment-button">Edit Comment</button>
      <button @click="deleteComment(comment.id)" class="delete-comment-button" v-if="isAuthorOrAdmin">Delete Comment</button>
    </div>
  </div>
</div>
        </div>
        <div v-else>
          <p>No comments yet.</p>
        </div>
      </div>
    </div>


    <div v-if="showModal" class="modal-overlay" @click.self="showModal = false">
  <div class="modal">
    <span class="close" @click="showModal = false">&times;</span> <!-- Corrected class name -->
    <form @submit.prevent="addComment">
      <div>
        <label for="commentContent">Comment:</label>
        <textarea v-model="commentContent" placeholder="Enter your comment" class="modal-textarea" required></textarea>
      </div>
      <button type="submit" class="post-button">Add Comment</button>
    </form>
  </div>
</div>

<!-- Edit Post Modal -->
<div v-if="showEditModal" class="modal-overlay" @click.self="showEditModal = false">
  <div class="modal">
    <span class="close" @click="showEditModal = false">&times;</span>
    <form @submit.prevent="saveEditedPost">
      <div>
        <label for="editedTitle">Title:</label>
        <input type="text" v-model="editedTitle" placeholder="Enter title" class="modal-input" required>
      </div>
      <div>
        <label for="editedContent">Content:</label>
        <textarea v-model="editedContent" placeholder="Enter content" class="modal-textarea" required></textarea>
      </div>
      <button type="submit" class="post-button">Save Changes</button>
    </form>
  </div>
</div>



<div v-if="showEditCommentModal" class="modal-overlay" @click.self="showEditCommentModal = false">
    <div class="modal">
    <span class="close" @click="showEditCommentModal = false">&times;</span>
    <form @submit.prevent="updateComment">
      <div>
        <label for="editedCommentContent">Comment:</label>
        <textarea v-model="editedCommentContent" placeholder="Edit your comment" class="modal-textarea" required></textarea>
      </div>
      <button type="submit" class="post-button">Update Comment</button>
    </form>
  </div>
</div>   

  
</template>

<script>
import axios from 'axios';
import Header from '../components/Header.vue';

export default {
  data() {
    return {
      post: null,
      showModal: false,
      commentContent: '',
      comments: [],
      showEditModal: false,
      editedTitle: '',
      editedContent: '',
      currentUser: null, // To hold the current user information
      showEditCommentModal: false,
      editingCommentId: null,
      editedCommentContent: '',
    };
  },
  computed: {
    isAuthorOrAdmin() {
      return this.currentUser && (this.currentUser.id === this.post.authorProfile.id || this.currentUser.role_id === 1);
    },
  },
  methods: {
    async deleteComment(commentId) {
      if (!confirm('Are you sure you want to delete this comment?')) return;
      const token = localStorage.getItem('authToken');
      try {
        const response = await axios.delete(`/api/deleteComment/${commentId}`, {
          headers: {
            Authorization: `Bearer ${token}`
          }
        });
        if (response.data && response.data.status) {
          alert('Comment deleted successfully');
          await this.fetchComments(this.post.id); // Refresh the comments list
        } else {
          alert('Error deleting comment');
        }
      } catch (error) {
        console.error('Error deleting comment:', error);
        alert('Error deleting comment');
      }
    },
    async getPost(id) {
      const token = localStorage.getItem('authToken');
      try {
        const response = await axios.get(`/api/getForumPost?id=${id}`, {
          headers: {
            Authorization: `Bearer ${token}`
          }
        });
        const postData = response.data.data;
        const authorProfile = await this.fetchProfile(postData.autors);
        postData.authorProfile = authorProfile;
        this.post = postData;

        // Fetch comments after getting the post
        await this.fetchComments(id);

        // Fetch the current user
        this.currentUser = await this.fetchCurrentUser();
      } catch (error) {
        console.error("There was an error fetching the post:", error);
      }
    },
    async addComment() {
      const token = localStorage.getItem('authToken');
      const postId = this.$route.params.postId;
      try {
        const response = await axios.post(`/api/createComment`, {
          post_id: postId,
          author_id: this.currentUser.id,
          content: this.commentContent
        }, {
          headers: {
            Authorization: `Bearer ${token}`
          }
        });
        if (response.data && response.data.status) {
          alert('Comment added successfully');
          this.commentContent = '';
          this.showModal = false;
          await this.fetchComments(postId);
        } else {
          alert('Error adding comment');
        }
      } catch (error) {
        console.error('Error adding comment:', error);
        alert('Error adding comment');
      }
    },
    async fetchProfile(authorId) {
      const token = localStorage.getItem('authToken');
      try {
        const response = await axios.get(`/api/getUserProfile?id=${authorId}`, {
          headers: {
            Authorization: `Bearer ${token}`
          }
        });
        return response.data.userData;
      } catch (error) {
        console.error('Error fetching profile:', error);
        return null;
      }
    },
    async fetchComments(postId) {
      const token = localStorage.getItem('authToken');
      try {
        const response = await axios.get('/api/getComments', {
          headers: {
            Authorization: `Bearer ${token}`,
            'Content-Type': 'application/json'
          },
          params: { post_id: postId }
        });
        if (response.data && response.data.comments) {
          this.comments = response.data.comments;
        } else {
          console.error('Comments data is missing or malformed', response.data);
          this.comments = [];
        }
      } catch (error) {
        console.error('Error fetching comments:', error);
        this.comments = [];
      }
    },
    async fetchCurrentUser() {
      const token = localStorage.getItem('authToken');
      try {
        const response = await axios.get('/api/profile', {
          headers: {
            Authorization: `Bearer ${token}`
          }
        });
        return response.data.data;
      } catch (error) {
        console.error('Error fetching current user:', error);
        return null;
      }
    },
    async deletePost() {
      if (!confirm('Are you sure you want to delete this post?')) return;

      const token = localStorage.getItem('authToken');
      const postId = this.post.id;
      try {
        const response = await axios.delete(`/api/deletePost/${postId}`, {
          headers: {
            Authorization: `Bearer ${token}`
          }
        });
        if (response.data && response.data.status) {
          alert('Post deleted successfully');
          this.$router.push('/forums_hub');
        } else {
          alert('Error deleting post');
        }
      } catch (error) {
        console.error('Error deleting post:', error);
        alert('Error deleting post');
      }
    },
    editPost() {
      this.editedTitle = this.post.nosaukums;
      this.editedContent = this.post.saturs;
      this.showEditModal = true;
    },
    async saveEditedPost() {
      const token = localStorage.getItem('authToken');
      const postId = this.post.id;
      try {
        const response = await axios.post(`/api/editForumPost/${postId}`, {
          nosaukums: this.editedTitle,
          saturs: this.editedContent
        }, {
          headers: {
            Authorization: `Bearer ${token}`
          }
        });
        if (response.data && response.data.status) {
          alert('Post edited successfully');
          this.showEditModal = false;
          await this.getPost(postId); // Refresh the post data
        } else {
          alert('Error editing post');
        }
      } catch (error) {
        console.error('Error editing post:', error);
        alert('Error editing post');
      }
    },
    async openEditCommentModal(commentId) {
      this.editingCommentId = commentId;
      this.showEditCommentModal = true;
      // Assuming comment data is already available in this.comments
      const comment = this.comments.find(c => c.id === commentId);
      if (comment) {
        this.editedCommentContent = comment.content;
      }
    },
    async updateComment() {
      const token = localStorage.getItem('authToken');
      const commentId = this.editingCommentId;
      try {
        const response = await axios.put(`/api/updateComment/${commentId}`, {
          content: this.editedCommentContent
        }, {
          headers: {
            Authorization: `Bearer ${token}`
          }
        });
        if (response.data && response.data.status) {
          alert('Comment updated successfully');
          this.showEditCommentModal = false;
          await this.fetchComments(this.post.id);
        } else {
          alert('Error updating comment');
        }
      } catch (error) {
        console.error('Error updating comment:', error);
        alert('Error updating comment');
      }
    },
  },
  
  mounted() {
    const postId = this.$route.params.postId;
    this.getPost(postId);
  },
  components: {
    Header
  }
};
</script>

<style scoped>
#post-details-content {
  width: 100%;
  max-width: 1200px;
  margin: 0 auto;
  padding: 2vh;
  box-sizing: border-box;
}

.return-link {
  padding: 1vh 2vh;
  font-size: 1.6vh;
  background-color: #4CAF50; /* Green */
  border: none;
  color: white;
  border-radius: 5px;
  cursor: pointer;
  transition: background-color 0.3s;
}

.return-link:hover {
  background-color: #45a049; /* Darker Green */
}

.comment-wrapper {
  border: 1px solid #ccc;
  padding: 1vh;
  margin-bottom: 1.5vh;
  border-radius: 0.5vh;
}

.modal-content {
  background-color: #fefefe;
  margin: auto;
  padding: 2vh;
  border: 1px solid #888;
  width: 80%;
  max-width: 500px;
  border-radius: 5px;
  box-sizing: border-box;
  position: relative;
}

.svg-button {
  background: transparent;
  border: none;
  cursor: pointer;
}

.modal-input,
.modal-textarea {
  width: 100%;
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

.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5); /* Semi-transparent black */
  z-index: 999; /* Ensure it's on top of other content */
  display: flex;
  justify-content: center;
  align-items: center;
}

.modal {
  background-color: white;
  padding: 20px;
  border-radius: 5px;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.3); /* Add some shadow for depth */
  position: relative; /* Ensure the close button is positioned relative to the modal */
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
  position: absolute;
  top: 10px;
  right: 10px;
  font-size: 20px;
  cursor: pointer;
  color: #888; /* Gray color */
}

.close:hover {
  color: #555; /* Darker gray on hover */
}

.close:focus {
  color: black;
  text-decoration: none;
  cursor: pointer;
}
.edit-comment-button,
.delete-comment-button  {
  padding-top: 1vh; /* Adjust the padding as needed */
  padding-bottom: 1vh; /* Adjust the padding as needed */
  background-color: #4CAF50; /* Green */
  border: none;
  color: white;
  border-radius: 5px;
  cursor: pointer;
  transition: background-color 0.3s;
}

.edit-comment-button:hover,
  .delete-comment-button:hover {
    background-color: #45a049; /* Darker Green */
  }

  .delete-comment-button {
    margin-left: 1vh; /* Add margin to separate buttons */
  }
</style>
