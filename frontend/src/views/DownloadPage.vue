  <template>
    <div>
      <Header />
      <a href="/RVTGTR.exe" download style="color: aqua;">Click here to download the latest version of RVTGTR.EXE!<br/></a>
      <a href="/RVTGTR.zip" download style="color: aqua;">Click here to download the latest version of RVTGTR.ZIP!</a>
      
      <!-- 
      ieliec majas ka lade zipu, nevis exe, lai var ari uz telefona noladet -->




      <div v-if="isAdmin">
        <h1>Admin File Upload</h1>
        <input type="file" @change="handleFileUpload" />
        <div>
          <button @click="uploadFile">Upload</button>
          <div v-if="uploadMessage">{{ uploadMessage }}</div>
          <div v-if="uploadedFiles.length > 0">
            <h2>Uploaded Files:</h2>
            <div v-for="(file, index) in uploadedFiles" :key="index">
              <a v-if="isImage(file.url)" :href="file.url" target="_blank">{{ file.name }}</a>
              <img v-else :src="file.url" :alt="file.name" />
              <button @click="deleteFile(file, index)">Delete</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </template>

  <script>
  import Header from '../components/Header.vue';
  import axios from 'axios';

  export default {
    name: "FileUpload",
    components: {
      Header
    },
    data() {
      return {
        selectedFile: null,
        uploadMessage: '',
        uploadedFiles: [] // Array to store uploaded files
      };
    },
    methods: {
      handleFileUpload(event) {
        try {
          this.selectedFile = event.target.files[0];
        } catch (error) {
          console.error("Error in handleFileUpload:", error);
          this.uploadMessage = 'Error selecting file. Please try again.';
        }
      },
      async uploadFile() {
        const token = localStorage.getItem('authToken');

        if (!this.selectedFile) {
          this.uploadMessage = 'Please select a file first.';
          return;
        }

        const formData = new FormData();
        formData.append('file', this.selectedFile);

        try {
          const response = await axios.post('/api/uploadFile', formData, {
            headers: {
              'Content-Type': 'multipart/form-data',
              'Authorization': `Bearer ${token}`
            }
          });
          this.uploadMessage = response.data.message;
          this.uploadedFiles.push({
            url: response.data.url, // Store the URL of the file
            name: this.selectedFile.name // Store the original file name
          });
        } catch (error) {
          console.error("Error in uploadFile:", error);
          this.uploadMessage = error.response ? error.response.data.message : 'File upload failed. Please try again.';
        }
      },
      async deleteFile(file, index) {
        const token = localStorage.getItem('authToken');

        try {
          const response = await axios.post('/api/deleteFile', { url: file.url }, {
            headers: {
              'Authorization': `Bearer ${token}`
            }
          });
          this.uploadMessage = response.data.message;
          if (response.data.status) {
            this.uploadedFiles.splice(index, 1); // Remove the file from the list
          }
        } catch (error) {
          console.error("Error in deleteFile:", error);
          this.uploadMessage = error.response ? error.response.data.message : 'File deletion failed. Please try again.';
        }
      },
      isImage(url) {
        return /\.(jpg|jpeg|png|gif)$/.test(url.toLowerCase());
      }
    },
    created() {
      // Fetch user data or get it from props
      // Assuming userRole is passed as a prop or fetched from the API
      if (this.userRole === 'admin') {
        this.isAdmin = true;
      }
    },
    props: {
      userRole: {
        type: String,
        required: true
      }
    }
  }
  </script>

  <style scoped>
  /* Add your styles here */
  </style>
    
