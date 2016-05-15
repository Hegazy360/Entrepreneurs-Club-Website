<?php
include_once 'dbconfig.php';
$displaySignup = 0; //display signup screen again in case signup failed
$displayLogin  = 0; //display login screen again in case login failed
require_once 'signup.php';
require_once 'login.php';
if (!isset($_GET['post-id']) || $_GET['post-id'] == '') {
    $user->redirect('home.php');
}
if (isset($_POST['profile-header'])) {
    $user->redirect('profile.php?id=' . $_SESSION['user']);
}
if (isset($_POST['sign-out-header'])) {
    $user->logout();
}
if (isset($_POST['delete-post'])) {
    try {
        $deletePost = $pdo->prepare("DELETE FROM posts WHERE id=:id");
        $deletePost->bindParam(':id', $_GET['post-id']);
        $deletePost->execute();
        $user->redirect('home.php');
    }
    catch (PDOException $e) {
        echo $e->getMessage();
        die();
    }
}
try {
    $get_post = $pdo->prepare("SELECT * FROM posts,info WHERE posts.user_id = info.id and posts.id = :post_id");
    $get_post->bindParam(':post_id', $_GET['post-id']);
    $get_post->execute();
    if ($get_post->rowCount() > 0) {
        $post = $get_post->fetch();
    } else {
        $user->redirect('home.php');
    }
}
catch (PDOException $e) {
    echo $e->getMessage();
    die();
}
if (isset($_POST['save-post'])) {
    try {
        $editPost = $pdo->prepare("UPDATE posts SET title = :title,summary = :summary,description = :description WHERE id=:id");
        $editPost->bindParam(':title', $_POST['post-title']);
        $editPost->bindParam(':summary', $_POST['post-summary']);
        $editPost->bindParam(':description', $_POST['post-description']);
        $editPost->bindParam(':id', $_GET['post-id']);
        $editPost->execute();
        $get_post->execute();
        $post = $get_post->fetch();
    }
    catch (PDOException $e) {
        echo $e->getMessage();
        die();
    }
}
if ($user->is_loggedin()) {
    try {
        $visitor = $pdo->prepare("SELECT * FROM info WHERE id=:id");
        $visitor->bindParam(':id', $_SESSION['user']);
        $visitor->execute();
        $checkVisitor = $visitor->fetch();
    }
    catch (PDOException $e) {
        echo $e->getMessage();
    }
}
if (isset($_POST['comment-submit'])) {
    try {
        $checkDuplicateComment = $pdo->prepare("SELECT * FROM comments WHERE user_id=:user_id AND post_id=:post_id AND comment=:comment");
        $checkDuplicateComment->bindParam(':user_id', $_SESSION['user']);
        $checkDuplicateComment->bindParam(':post_id', $_GET['post-id']);
        $checkDuplicateComment->bindParam(':comment', $_POST['comment']);
        $checkDuplicateComment->execute();
        if ($checkDuplicateComment->rowCount() == 0) {
            try {
                $addComment = $pdo->prepare("INSERT INTO comments(user_id,post_id,comment) VALUES(:user_id,:post_id,:comment)");
                $addComment->bindParam(':user_id', $_SESSION['user']);
                $addComment->bindParam(':post_id', $_GET['post-id']);
                $addComment->bindParam(':comment', $_POST['comment']);
                $addComment->execute();
                unset($_POST['comment-submit']);
            }
            catch (PDOException $e) {
                echo $e->getMessage();
                die();
            }
        }
    }
    catch (PDOException $e) {
        echo $e->getMessage();
    }
}
try {
    $getComments = $pdo->prepare("SELECT * FROM comments WHERE post_id = :post_id");
    $getComments->bindParam(':post_id', $_GET['post-id']);
    $getComments->execute();
}
catch (Exception $e) {
    echo $e->getMessage();
    die();
}
if (isset($_POST['save-comment'])) {
    try {
        $editComment = $pdo->prepare("UPDATE comments SET comment = :comment WHERE user_id=:user_id AND post_id=:post_id");
        $editComment->bindParam(':comment', $_POST['comment-content']);
        $editComment->bindParam(':user_id', $_POST['comment-user-id']);
        $editComment->bindParam(':post_id', $_GET['post-id']);
        $editComment->execute();
        $getComments->execute();
    }
    catch (PDOException $e) {
        echo $e->getMessage();
        die();
    }
}
if (isset($_POST['delete-comment'])) {
    try {
        $deleteComment = $pdo->prepare("DELETE FROM comments WHERE user_id=:user_id AND post_id=:post_id AND comment=:comment");
        $deleteComment->bindParam(':comment', $_POST['comment-content']);
        $deleteComment->bindParam(':user_id', $_POST['comment-user-id']);
        $deleteComment->bindParam(':post_id', $_GET['post-id']);
        $deleteComment->execute();
        $getComments->execute();
    }
    catch (PDOException $e) {
        echo $e->getMessage();
        die();
    }
}
if (isset($_POST['increment-likes'])) {
    try {
        $checkLike = $pdo->prepare("SELECT * FROM likes WHERE user_id=:user_id AND post_id = :post_id");
        $checkLike->bindParam(':user_id', $_SESSION['user']);
        $checkLike->bindParam(':post_id', $_GET['post-id']);
        $checkLike->execute();
        if ($checkLike->rowCount() <= 0) {
            try {
                $saveLike = $pdo->prepare("INSERT INTO likes VALUES(:user_id,:post_id)");
                $saveLike->bindParam(':user_id', $_SESSION['user']);
                $saveLike->bindParam(':post_id', $_GET['post-id']);
                $saveLike->execute();
            }
            catch (PDOException $e) {
                echo $e->getMessage();
            }
        } else {
            try {
                $deleteLike = $pdo->prepare("DELETE FROM likes WHERE user_id = :user_id AND post_id = :post_id");
                $deleteLike->bindParam(':user_id', $_SESSION['user']);
                $deleteLike->bindParam(':post_id', $_GET['post-id']);
                $deleteLike->execute();
            }
            catch (PDOException $e) {
                echo $e->getMessage();
            }
        }
    }
    catch (Exception $e) {
        echo $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Post</title>
    <link rel="stylesheet" href="assets/css/main.css" charset="utf-8">
    <link rel="stylesheet" href="assets/css/header-style.css" charset="utf-8">
    <link rel="stylesheet" href="assets/css/basic-form-style.css" charset="utf-8">
    <link rel="stylesheet" href="assets/css/post-style.css" charset="utf-8">
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

            <h3><a href="home.php">Entrepreneurs Club</a></h3>
        </div>
        <?php if($user->is_loggedin()) {?>
        <div class="header-sign-buttons">
          <form class="header-buttons" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'].'?post-id='.$_GET['post-id']);?>" method="post">
            <button type="submit" name="profile-header" id="profile-header">
                Profile
            </button>
            <button type="submit" name="sign-out-header" id="sign-out-header">
                Sign Out
            </button>
          </form>
        </div>
        <?php } else { ?>
          <div class="header-sign-buttons">
            <button type="button" name="sign-in-header" id="sign-in-header" onclick="checkPressed(1)">
                Log in
            </button>
            <button type="button" name="sign-up-header" id="sign-up-header" onclick="checkPressed(2)">
                Sign up
            </button>
          </div>
          <?php }?>
    </div>
    <div class="home-container" onclick='checkPressedDiv(event)'>


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
                          <form class="sign-form" id="login-form" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'].'?post-id='.$_GET['post-id']);?>" method="post">
                              <label for="email">Email:</label><br>
                              <input type="email" name="emailLogin" id="email" required><br>
                              <label for="password">Password:</label><br>
                              <input type="password" name="passwordLogin" required><br>
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
                          <form class="sign-form" id="signup-form" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'].'?post-id='.$_GET['post-id']);?>" onSubmit="return validateSignUpForm()" method="post">
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

      <div class="post-section">
        <form class="post-content-form" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'].'?post-id='.$_GET['post-id']);?>" method="post">

          <div class="post-header">
            <h3 class="topic-author" ><a href="<?php echo "profile.php?id=".$post['id']; ?>"><?php
            $tableJoin = $pdo->prepare("SELECT info.name FROM info,posts WHERE info.id = posts.user_id AND info.id = :id");
            $tableJoin->bindParam(':id',$post['user_id']);
            $tableJoin->execute();
            $author = $tableJoin->fetch();
            echo $author['name'];
             ?></a></h3>
             <h4 class="topic-date"><?php echo $post['date']; ?></h4>

          </div>

          <?php if($user->is_loggedin()) {
            if($checkVisitor['admin'] == 1 || $_SESSION['user'] == $post['user_id']) { ?>
          <div class="control-panel">
          <?php if(!isset($_POST['edit-post'])) {?>
            <button type="submit" name="edit-post"><img src="./assets/img/edit-icon.png" alt="Edit" /></button>
          <?php } else {?>
            <button type="submit" name="save-post"><img src="./assets/img/save-icon.png" alt="Save" /></button>
          <?php } ?>
          <button type="submit" name="delete-post"><img src="./assets/img/delete-icon.png" alt="Delete" /></button>
        </div>
        <?php }} ?>

          <input type="text" class="post-title" name="post-title" value="<?php echo $post['title']; ?>" <?php if(!isset($_POST['edit-post'])) { echo "readonly";} ?> maxlength="30" required autocomplete="off">
          <textarea class="post-summary" name="post-summary"<?php if(!isset($_POST['edit-post'])) { echo "readonly";} ?> maxlength="200" required><?php echo $post['summary']; ?></textarea>
          <hr>
          <textarea class="post-description" name="post-description"  <?php if(!isset($_POST['edit-post'])) { echo "readonly";} ?> maxlength="2500" required><?php echo $post['description']."\r\n"; ?></textarea>
          <?php if($post['video']!='0') { ?>
          <iframe class="youtube-video" src="<?php echo "https://www.youtube.com/embed/".$post['video']; ?>" frameborder="0" frameborder="0" allowfullscreen></iframe>
        <?php } ?>
        </form>
        <hr>
        <div class="post-footer">

          <?php

            try {
              $checkLiked = $pdo->prepare("SELECT * FROM likes WHERE user_id=:user_id AND post_id = :post_id");
              $checkLiked->bindParam(':user_id',$_SESSION['user']);
              $checkLiked->bindParam(':post_id',$_GET['post-id']);
              $checkLiked->execute();
              if ($checkLiked->rowCount() > 0) { ?>
                <form class="form-likes" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'].'?post-id='.$_GET['post-id']);?>" method="post">
                  <input type="hidden" name="post-id" value="<?php echo $_GET['post-id'];?>">
                  <button type="submit" name="increment-likes" class="like-button"><img src="./assets/img/thumbs-up-green.png" alt="Thumbs Up" /></button>
                </form>
              <?php }
              else { ?>
                <form class="form-likes" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'].'?post-id='.$_GET['post-id']);?>" method="post">
                  <input type="hidden" name="post-id" value="<?php echo $_GET['post-id'];?>">
                  <button type="<?php if($user->is_loggedin()) {echo "submit";} else {echo "button";}?>" name="increment-likes" class="like-button"><img src="./assets/img/thumbs-up.png" alt="Thumbs Up" /></button>
                </form>
              <?php }
            } catch (PDOException $e) {
              echo $e->getMessage();
            }
           ?>
          <span class="post-likes">
             <?php
               try {
                 $countLikes = $pdo->prepare("SELECT * FROM likes where post_id = :post_id");
                 $countLikes->bindParam(':post_id',$_GET['post-id']);
                 $countLikes->execute();
                 $countLikesResult = $countLikes->rowCount();
                 echo $countLikesResult;

                 try {
                   $updateLikes = $pdo->prepare("UPDATE posts SET likes=:likes WHERE id=:post_id");
                   $updateLikes->bindParam(':likes',$countLikesResult);
                   $updateLikes->bindParam(':post_id',$_GET['post-id']);
                   $updateLikes->execute();
                 } catch (PDOException $e) {
                   echo $e->getMessage();
                 }


               } catch (PDOException $e) {
                 echo $e->getMessage();
               }
              ?>
              </span>
        </div>
      </div>
      <hr class="post-comment-separator">
      <div class="comments-container">
      <?php while(($comment = $getComments->fetch()) !== false) {?>

        <form class="comment-section" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'].'?post-id='.$_GET['post-id']);?>" method="post">
          <input type="hidden" name="comment-user-id" value="<?php echo $comment['user_id'];?>">
          <?php try {
            $getName = $pdo->prepare("SELECT * FROM info WHERE id = :id");
            $getName->bindParam(':id',$comment['user_id']);
            $getName->execute();
            $name = $getName->fetch();
          } catch (PDOException $e) {
            echo $e->getMessage();
          }
           ?>
           <a href="<?php echo "profile.php?id=".$name['id'];?>" class="<?php if ($name['profession']==0){echo "comment-name";} else{echo "comment-name investor-name";}?>"><?php echo $name['name'];?></a>
           <textarea class="<?php if ($name['profession']==0){echo "comment-content";} else{echo "comment-content investor-comment";}?>" name="comment-content" <?php if(!isset($_POST['edit-comment']) || $_POST['comment-id']!=$comment['id']) { echo "readonly"; } ?>><?php echo $comment['comment']; ?></textarea>
           <?php if($user->is_loggedin()) {
             if($checkVisitor['admin'] == 1 || $_SESSION['user'] == $comment['user_id']) { ?>
           <div class="control-panel">
               <?php if(!isset($_POST['edit-comment']) || $_POST['comment-id']!=$comment['id']) {?>
                 <input type="hidden" name="comment-id" value="<?php echo $comment['id'];?>">
                 <button type="submit" name="edit-comment"><img src="./assets/img/edit-icon.png" alt="Edit" /></button>
               <?php } else {?>
                 <button type="submit" name="save-comment"><img src="./assets/img/save-icon.png" alt="Save" /></button>
               <?php } ?>
               <button type="submit" name="delete-comment"><img src="./assets/img/delete-icon.png" alt="Delete" /></button>
           </div>
           <?php }} ?>
        </form>



      <hr class="comment-separator">
      <?php } ?>
      <?php if($user->is_loggedin()) { ?>
      <div class="newcomment-section">
        <form class="newcomment-form" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'].'?post-id='.$_GET['post-id']);?>" method="post">
          <textarea name="comment" class="new-comment" required maxlength="500"></textarea>
          <button type="submit" name="comment-submit">Comment</button>
        </form>
      </div>
      <?php } ?>
    </div>
  </div>


  </body>
</html>
