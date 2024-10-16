<template>
  <div class="RegWindow">
    <div class="horizontal-line"></div>
    <h1 id="header">RVTGTR</h1>
    <form @submit.prevent="register">
      <div id="input-container">
        <input type="email" placeholder="E-mail" v-model="formData.email" required>
        <input type="text" placeholder="Username" v-model="formData.username" required>
        <div class="additional-inputs">
          <input type="text" placeholder="Name" v-model="formData.name" required>
          <!-- Enforce 7-character minimum for the password fields -->
          <input type="password" placeholder="Password" v-model="formData.password" required minlength="7">
          <input type="password" placeholder="Confirm password" v-model="formData.password_confirmation" required minlength="7">
        </div>
      </div>
      <div id="button-container">
        <button type="submit" id="reg-but">REGISTER</button>
      </div>
      <RouterLink to="/" id="go-back">Go Back</RouterLink>
      <p v-if="errorMessage" class="error-message">{{ errorMessage }}</p>
    </form>
  </div>
  <div class="bottom-text">
    SIA OOGA BOOGA produkts - nezinu kādu random bs te raksta
    According to all known laws of aviation, there is no way a bee should be able to fly. Its wings are too small to get its fat little body off the ground. The bee, of course, flies anyway because bees don't care what humans think is impossible. Yellow, black. Yellow, black. Yellow, black. Yellow, black. Ooh, black and yellow! Let's shake it up a little. Barry! Breakfast is ready! Ooming!
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      formData: {
        email: "",
        username: "",
        name: "",
        password: "",
        password_confirmation: ""
      },
      errorMessage: ""
    };
  },
  methods: {
    async register() {
      try {
        const response = await axios.post('/api/register', this.formData);
        console.log(response.data);
        alert("Registration successful!");
        this.$router.push("/");
      } catch (error) {
        if (error.response && error.response.data && error.response.data.errors) {
          this.errorMessage = Object.values(error.response.data.errors)[0][0];
        } else {
          this.errorMessage = "Registration failed! Please try again later.";
        }
        console.error(error.response.data);
        alert(this.errorMessage);
      }
    }
  }
};
</script>
  <style scoped>
  body {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
    background-color: #f0f0f0;
  }

  .RegWindow {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    width: 100%;
    max-width: 450px;
  }

  #header {
    font-family: "Inter", sans-serif;
    font-size: 80px;
    font-weight: bold;
    margin-bottom: 20px;
    color: #00FF29; /* Original green color */
    -webkit-text-stroke: 4px #ffffff; /* Chrome, Safari */
    stroke: 4px #ffffff; /* Standard */
  }

  #input-container {
    margin-bottom: 20px;
  }

  input {
    display: block;
    height: 40px;
    width: 800px; /* Increased width */
    border-radius: 15px;
    border: 0;
    font-size: 18px;
    text-indent: 20px;
    font-family: "Inter", sans-serif;
    color: #000000;
    background-color: #ffffff;
    margin: 10px auto;
    padding: 0 15px; /* Added padding for better touch experience */
  }

  input::placeholder {
    color: #000000;
    font-size: 16px;
  }

  #button-container {
    display: flex;
    justify-content: center;
    margin-bottom: 20px;
    gap: 20px;
  }

  button {
    padding-top: 5px;
    height: 45px;
    width: 150px;
    border: none;
    border-radius: 25px;
    background-color: #00570E;
    color: #ffffff;
    font-family: "Inter", sans-serif;
    font-size: 18px;
    cursor: pointer;
    text-align: center;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
  }

  button:hover {
    transform: scale(1.10);
    border: 1px white solid;
  }

  #error-message {
    color: red;
    margin-top: 10px;
    font-family: "Inter", sans-serif;
    font-size: 16px;
  }

  #go-back {
    display: inline-block;
    margin-top: 10px;
    margin-bottom: 20px;
    font-family: "Inter", sans-serif;
    font-size: 14px;
    color: #00FF29;
    text-decoration: none;
    cursor: pointer;
    text-align: center;
    width: 100%;
  }

  .horizontal-line {
    position: fixed;
    top: 5vh; /* Adjusted to match the position in loginview */
    left: 0;
    height: 3px;
    background-color: white;
    width: 100%;
  }

  #go-back:hover {
    text-decoration: underline;
  }

  .bottom-text {
    position: fixed;
    bottom: 20px;
    left: 50%;
    transform: translateX(-50%);
    width: 80%;
    font-family: "Inter", sans-serif;
    font-size: 14px;
    color: #FFFFFF;
    text-align: center;
  }
  </style>
