  <template>
    <div class="LoginWindow">
      <h1 id="header" style="font-size: 5vh; padding-left: 4vh;">Forgot your password?</h1>  
      <form>
        <p id="ForgotPassMessage">Enter your email address and we'll send you a link to reset your password</p>
        <div id="input-container">
          <input type="text" placeholder="E-MAIL" id="email">
        </div>
        <RouterLink to="/ResetPass" id="send">
          <button id="send-but">Send</button>
        </RouterLink>
        <p id="or" style="padding-bottom: 2vh;">Or</p>
        <RouterLink to="/register" id="register">Create new account</RouterLink>
        <div class="horizontal-line"></div> <!-- Added horizontal line -->
      </form>
    </div>
  </template>
<style scoped>
body {
  place-items: center;
  overflow: auto;
}

.LoginWindow {
  width: 450px;
  height: 600px;
  background-color: var(--color-element);
  border-radius: 25px;
  box-shadow: 0px 0px 300px 50px var(--color-element);
  display: block;
}

#header {
  font: "Inter";
  font-size: 80px;
  font-weight: bold;
  display: flex;
  position: relative;
  left: 69px;
  top: 40px;
  width: 300px;
  margin: 0;
  padding: 0;
}

p {
  width: 16vw;
  font-size: 16px;
  display: flex;
  position: relative;
  text-align: center;
}

#ForgotPassMessage {
  top: 7vh;
  left: 4vw;
}

#input-container {
  width: 300px;
  height: 180px;
  display: block;
  position: relative;
  top: 45px;
  left: 77px;
}

input {
  height: 70px;
  width: 300px;
  border-radius: 25px;
  border: 1px solid white; /* Add white outline */
  font-size: 20px;
  text-indent: 20px;
  font: "Inter";
  color: var(--color-text);
  background-color: var(--color-light-dark-red);
}

input::placeholder {
  color: var(--color-text);
  font-size: 17px;
}

#email {
  margin-top: 45px;
}

#send-but, #register { /* Style the buttons like the green buttons in ForumHub */
  height: 45px;
  width: 150px;
  border: none;
  border-radius: 5px;
  display: flex;
  position: relative;
  left: 150px;
  display: table-cell;
  vertical-align: middle;
  background-color: #4CAF50; /* Green */
  color: white;
  font: "Inter";
  font-size: 18px;
  cursor: pointer;
  transition: background-color 0.3s;
}

#send-but:hover, #register:hover {
  background-color: #45a049; /* Darker Green on hover */
}

#send-but:focus, #register:focus {
  outline: none; /* Remove outline on focus */
}

#send {
  text-decoration: none;
}

#or {
  margin-top: 2vh;
  left: 11.2vw;
}
.horizontal-line {
  position: fixed;
  top: 5vh; /* Adjust as needed */
  left: 0;
  height: 3px;
  background-color: white;
  width: 100%; /* Span from left to right */
}

#register {
  text-align: center; /* Center text horizontally */
}
</style>






