<?php
require_once 'dbconfig.php';
$displaySignup = 0; //display signup screen again in case signup failed
$displayLogin  = 0; //display login screen again in case login failed
require_once 'signup.php';
require_once 'login.php';
if ($user->is_loggedin()) {
    $user->redirect('home.php'); //change header
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Welcome</title>
        <link rel="stylesheet" href="assets/css/main.css" charset="utf-8">
        <link rel="stylesheet" href="assets/css/header-style.css" charset="utf-8">
        <link rel="stylesheet" href="assets/css/basic-form-style.css" charset="utf-8">
        <link rel="stylesheet" href="assets/css/welcome-style.css" charset="utf-8">
        <link href='https://fonts.googleapis.com/css?family=Fira+Sans:400,700,400italic' rel='stylesheet' type='text/css'>
        <link href='https://fonts.googleapis.com/css?family=Courgette' rel='stylesheet' type='text/css'>
        <script type="text/javascript" src="assets/js/main.js" async></script>
        <script src="//content.jwplatform.com/libraries/uiI4I6Mh.js"></script>

    </head>
    <body>
        <div class="header">
            <div class="header-logo">
                <a href="welcome.php"><img src="assets/img/bulb-logo.png" alt="Path Logo" /></a>

                <h3><a href="welcome.php">Entrepreneurs Club</a></h3>
            </div>

        </div>

        <div class="home-container">
          <div id="bgvid"></div>
          <script>
          jwplayer("bgvid").setup({
              image: "http://content.jwplatform.com/thumbs/svhbHZEe-1280.jpg",
              sources: [{
                file: "http://content.jwplatform.com/videos/svhbHZEe-HPCEXtWU.mp4",
                label: "720p SD",
                "default": "true"
              }]
            });
</script>
          <div class="main-section">
            <?php if(isset($_GET['signupsuccess'])) { ?>
                <h2>SIGNUP SUCCESSFUL!</h2>
              <?php } else if(isset($_GET['loginsuccess'])) { ?>
                <h2>LOGIN SUCCESSFUL!</h2>
              <?php } ?>

              <h2 id="intro" name="intro">It's time to make a difference...</h2>
              <h4 id="description" name="description">Create and share your idea</h4>
              <br>

              <div class="sign-buttons-container">
                  <button type="button" name="sign-in-button" id="sign-in-button" onclick="checkPressed(1)">
                    Log in
                  </button>
                  <button type="button" name="sign-up-button" id="sign-up-button" onclick="checkPressed(2)">
                    Sign up
                  </button>
              </div>
              <a href="#">
              <a name="more" id="more" href="home.php">Discover</a>
          </div>

        </div>
            <?php if ($displaySignup == 1 || $displayLogin ==1) { ?>
              <div class="overlay show" id="overlay" onclick='checkPressedDiv(event)'>
            <?php } else { ?>
              <div class="overlay" id="overlay" onclick='checkPressedDiv(event)'>
            <?php } ?>




              <?php if ($displayLogin == 1) { ?>
              <div class="login-form-container show" id="login-form-container">
              <?php } else { ?>
              <div class="login-form-container" id="login-form-container">
              <?php } ?>
                    <h1>Log in</h1>
                    <form class="sign-form" id="login-form" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>" method="post">
                        <label for="email">Email:</label>
                        <input type="email" name="emailLogin" id="email" required>
                        <label for="password">Password:</label>
                        <input type="password" name="passwordLogin" required>
                        <button type="submit" name="submitLogin" id="submit">Submit</button>
                    </form>
                </div>

                <?php if ($displaySignup == 1) { ?>
                <div class="signup-form-container show" id="signup-form-container">
                <?php } else { ?>
                <div class="signup-form-container" id="signup-form-container">
                <?php } ?>
                    <h1>Sign up</h1>
                    <div class="error" id="phpErrors"></div><br>
                    <form class="sign-form" id="signup-form" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>" onSubmit="return validateSignUpForm()" method="post">
                        <label for="name">Name:</label><br>
                        <input type="text" name="nameSignup" id="nameSignup" onblur="validate(this,'errorName')" required>
                        <span class="error" id="errorName"></span><br>
                        <label for="emailSignup">Email:</label><br>
                        <input type="email" name="emailSignup" id="emailSignup" onblur="validate(this,'errorEmail')" required>
                        <span class="error" id="errorEmail"></span><br>
                        <label for="emailConfirm">Confirm Email:</label><br>
                        <input type="email" name="emailConfirm" id="emailConfirm" onblur="validate(this,'errorEmailConfirm')" required>
                        <span class="error" id="errorEmailConfirm"></span><br>
                        <label for="password">Password:</label><br>
                        <input type="password" name="passwordSignup" id="passwordSignup" onblur="validate(this,'errorPassword')" required>
                        <span class="error" id="errorPassword"></span><br>
                        <label for="passworConfirm">Confirm Password:</label><br>
                        <input type="password" name="passwordConfirm" id="passwordConfirm" onblur="validate(this,'errorPasswordConfirm')" required>
                        <span class="error" id="errorPasswordConfirm"></span><br>
                        <div class="radio-buttons">
                          <label for="entrepreneur">Entrepreneur</label>
                          <input type="radio" name="profession" value="0" checked>
                        </div>
                        <div class="radio-buttons">
                          <label for="investor">Investor</label>
                          <input type="radio" name="profession" value="1">
                        </div>

                        <button type="submit" name="submitSignup" id="submit">Submit</button>
                    </form>
                </div>

            </div>

        </div>


    </body>
</html>
