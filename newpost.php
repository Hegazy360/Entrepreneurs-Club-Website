<?php
include_once 'dbconfig.php';
if (!$user->is_loggedin()) {
    $user->redirect('home.php');
}
if (isset($_POST['post-submit'])) {
  if(isset($_POST['video'])){
    $rx = '~
    ^(?:https?://)?
     (?:www\.)?
     (?:youtube\.com|youtu\.be)
     /watch\?v=([^&]+)
     ~x';
     $has_match = preg_match($rx, $_POST['video'], $matches);
     if($has_match){
       $video=$matches[1];
          }
     else {
       $video = 0;
     }

  }
    try {
        $newpost = $pdo->prepare("INSERT INTO posts(title,summary,description,user_id,date,video) VALUES(:title,:summary,:description,:user_id,:date,:video)");
        $newpost->bindParam(':title', $_POST['post-title']);
        $newpost->bindParam(':summary', $_POST['post-summary']);
        $newpost->bindParam(':description', $_POST['post-description']);
        $newpost->bindParam(':user_id', $_SESSION['user']);
        $newpost->bindParam(':date', date("Y-m-d"));
        $newpost->bindParam(':video', $video);
        $newpost->execute();
        $user->redirect('home.php');
    }
    catch (PDOException $e) {
        echo $e->getMessage();
        die();
    }
}
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>New Post</title>
    <link rel="stylesheet" href="assets/css/main.css" charset="utf-8">
    <link rel="stylesheet" href="assets/css/header-style.css" charset="utf-8">
    <link rel="stylesheet" href="assets/css/basic-form-style.css" charset="utf-8">
    <link rel="stylesheet" href="assets/css/newpost-style.css" charset="utf-8">
    <link href='https://fonts.googleapis.com/css?family=Fira+Sans:400,700,400italic' rel='stylesheet' type='text/css'>
    <link href='https://fonts.googleapis.com/css?family=Courgette' rel='stylesheet' type='text/css'>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.2/jquery.min.js"></script>
    <script type="text/javascript" src="assets/js/jquery.flexibleArea.js"></script>
    <script type="text/javascript" src="assets/js/main.js" async></script>
  </head>
  <body>

    <div class="header">
        <div class="header-logo">
            <a href="welcome.php"><img src="assets/img/bulb-logo.png" alt="Path Logo" /></a>

            <h3><a href="welcome.php">Entrepreneurs Club</a></h3>
        </div>
        <div class="header-sign-buttons">
            <button type="button" name="sign-in-header" id="sign-in-header" onclick="checkPressed(1)">
                Log in
            </button>
            <button type="button" name="sign-up-header" id="sign-up-header" onclick="checkPressed(2)">
                Sign up
            </button>
        </div>
    </div>

    <div class="home-container">

      <div class="main-section">
        <form class="newpost-form" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>" method="post">
          <label for="post-title">Title</label>
          <input type="text" name="post-title" class="post-title" maxlength="30" required autocomplete="off">
          <label for="post-summary">Summary</label>
          <textarea class="post-summary" name="post-summary" maxlength="200" required></textarea>
          <label for="post-description">Description</label>
          <textarea class="post-description" name="post-description" maxlength="2500" required></textarea>
          <label for="video">Youtube Link (If available)</label>
          <input type="text" name="video" placeholder="Please make sure it's a youtube link for it to be displayed">
          <button type="submit" name="post-submit">Post it!</button>
        </form>

      </div>

    </div>

  </body>
</html>
