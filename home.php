<?php
include_once 'dbconfig.php';
$displaySignup = 0; //display signup screen again in case signup failed
$displayLogin  = 0; //display login screen again in case login failed
require_once 'signup.php';
require_once 'login.php';
if (isset($_POST['newpost-button'])) {
    $user->redirect("newpost.php");
}
if (isset($_POST['profile-header'])) {
    $user->redirect('profile.php?id=' . $_SESSION['user']);
}
if (isset($_POST['sign-out-header'])) {
    $user->logout();
}
if (!isset($_GET['page'])) {
    $user->redirect('home.php?page=1');
}
if ($user->is_loggedin()) {
    $id = $_SESSION['user'];
}
try {
    $stmt = $pdo->prepare("SELECT * FROM info WHERE id = :id"); //get current user information
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $userInfo = $stmt->fetch();
}
catch (PDOException $e) {
    echo $e->getMessage();
    die();
}
try {
    $allPosts = $pdo->prepare("SELECT * FROM posts ORDER BY likes DESC"); //get all posts ordered by likes
    $allPosts->execute();
    $postsPerPage = 4;
    if ($allPosts->rowCount() > 0) {
        $totalPages = ceil($allPosts->rowCount() / $postsPerPage);
    }
}
catch (PDOException $e) {
    echo $e->getMessage();
}
try {
    $postsPerPage = 4;
    $start        = ($_GET['page'] - 1) * $postsPerPage;
    $posts        = $pdo->prepare("SELECT * FROM posts ORDER BY likes DESC,date DESC LIMIT :start,:postsPerPage"); //get selected number of posts
    $posts->bindParam(':start', $start, PDO::PARAM_INT);
    $posts->bindParam(':postsPerPage', $postsPerPage, PDO::PARAM_INT);
    $posts->execute();
}
catch (PDOException $e) {
    echo $e->getMessage();
    die();
}
if (isset($_POST['increment-likes'])) {
    try {
        $checkLike = $pdo->prepare("SELECT * FROM likes WHERE user_id=:user_id AND post_id = :post_id"); //check possible like duplicate
        $checkLike->bindParam(':user_id', $id);
        $checkLike->bindParam(':post_id', $_POST['post-id']);
        $checkLike->execute();
        if ($checkLike->rowCount() <= 0) {
            try {
                $saveLike = $pdo->prepare("INSERT INTO likes VALUES(:user_id,:post_id)"); //save like into database
                $saveLike->bindParam(':user_id', $id);
                $saveLike->bindParam(':post_id', $_POST['post-id']);
                $saveLike->execute();
            }
            catch (PDOException $e) {
                echo $e->getMessage();
            }
        } else {
            try {
                $deleteLike = $pdo->prepare("DELETE FROM likes WHERE user_id = :user_id AND post_id = :post_id"); //delete like from database if pressed again
                $deleteLike->bindParam(':user_id', $id);
                $deleteLike->bindParam(':post_id', $_POST['post-id']);
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
    <title>Home</title>
    <link rel="stylesheet" href="assets/css/main.css" charset="utf-8">
    <link rel="stylesheet" href="assets/css/basic-form-style.css" charset="utf-8">
    <link rel="stylesheet" href="assets/css/header-style.css" charset="utf-8">
    <link rel="stylesheet" href="assets/css/home-style.css" charset="utf-8">
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
          <form class="header-buttons" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'].'?page='.$_GET['page']);?>" method="post">
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
                          <form class="sign-form" id="login-form" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'].'?page='.$_GET['page']);?>" method="post">
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
                          <form class="sign-form" id="signup-form" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'].'?page='.$_GET['page']);?>" onSubmit="return validateSignUpForm()" method="post">
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

      <?php if($user->is_loggedin()) { ?>
      <div class="profile-sidebar hide" id="profile-sidebar">
        <img class="profile-picture" src="./users_image/<?php if($userInfo['profile_picture']!=$userInfo['id'].".") {echo $userInfo['profile_picture'];} else {echo "default.jpg";} ?>" alt="Profile Picture" />
        <h3 class="user-name" id="user-name"><?php echo $userInfo['name']; ?></h3>
        <div class="sidebar-posts-container">
          <?php while (($allPostsInfo = $allPosts->fetch()) !== false) {
            if($allPostsInfo['user_id']==$_SESSION['user']){ ?>
              <div class="sidebar-post" id="sidebar-post">
                <div class="sidebar-post-header" id="sidebar-post-header">
                  <a class="sidebar-post-title" href="post.php?post-id=<?php echo $allPostsInfo['id'];?>"><?php echo $allPostsInfo['title'];?></a>
                </div>
                <div class="sidebar-post-body" id="sidebar-post-body">
                  <div class="sidebar-comments-section" id="sidebar-comments-section">
                    <button type="button" class="sidebar-comment-button"><img src="./assets/img/comment-icon.png" alt="Comments" /></button>
                    <span class="sidebar-comments-counter">
                      <?php try {
                      $countSidebarPostComments = $pdo->prepare("SELECT * FROM comments WHERE post_id = :user_post_id");
                      $countSidebarPostComments->bindParam(':user_post_id',$allPostsInfo['id']);
                      $countSidebarPostComments->execute();
                      echo $countSidebarPostComments->rowCount();
                    } catch (PDOException $e) {
                      echo $e->getMessage();
                    }
                     ?></span>
                  </div>
                  <div class="sidebar-likes-section" id="sidebar-likes-section">
                    <button type="button" class="sidebar-like-button"><img src="./assets/img/thumbs-up.png" alt="Likes" /></button>
                    <span class="sidebar-likes-counter">
                      <?php try {
                      $countSidebarPostLikes = $pdo->prepare("SELECT * FROM likes WHERE post_id = :user_post_id");
                      $countSidebarPostLikes->bindParam(':user_post_id',$allPostsInfo['id']);
                      $countSidebarPostLikes->execute();
                      echo $countSidebarPostLikes->rowCount();
                    } catch (PDOException $e) {
                      echo $e->getMessage();
                    }

                     ?></span>
                  </div>


                </div>
                <hr class=".sidebar-posts-separator">

              </div>
            <?php }
          } ?>
        </div>
      </div>
      <?php } ?>


      <div class="main-section">
        <?php if($user->is_loggedin()) { ?>
        <form class="newpost-button-form" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'].'?page='.$_GET['page']);?>" method="post">
          <button type="submit" name="newpost-button" class="newpost-button">Create a new post!</button>
        </form>
        <?php } ?>

        <h3 class="topic-section-title">Most Popular</h3>
        <div class="pages-navigation">
          <?php
          if($_GET['page']!=1)
          {
             $previous =$_GET['page']-1;
             echo "<a class='other-pages' href='".$_SERVER['PHP_SELF']."?page=1'>First</a>";
             echo "<a class='other-pages' href='".$_SERVER['PHP_SELF']."?page=".$previous."'>Previous</a>";
          }

          for($i=1;$i<=$totalPages;$i++) {

            if ($i==$_GET['page']) {
              echo "<a class='current-page' href='".$_SERVER['PHP_SELF']."?page=".$i."'>".$i."</a>";
            }
            else {
              echo "<a class='other-pages' href='".$_SERVER['PHP_SELF']."?page=".$i."'>".$i."</a>";
            }
          }

          if($_GET['page']!=$totalPages)
          {
               $next=$_GET['page']+1;
               echo "<a class='other-pages' href='".$_SERVER['PHP_SELF']."?page=".$next."'>Next</a>";
               echo "<a class='other-pages' href='".$_SERVER['PHP_SELF']."?page=".$totalPages."'>Last</a>";
          }
           ?>
        </div>
        <?php while (($postInfo = $posts->fetch()) !== false) { ?>
        <div class="topic-section">
          <div class="topic-header">
            <h2 class="topic-link" ><a href="<?php echo "post.php?post-id=".$postInfo['id']; ?>"><?php
            echo $postInfo['title'];
             ?></a></h2>
            <h3 class="topic-author" ><a href="<?php echo "profile.php?id=".$postInfo['user_id']; ?>"><?php
            $tableJoin = $pdo->prepare("SELECT info.name FROM info,posts WHERE info.id = posts.user_id AND info.id = :id");
            $tableJoin->bindParam(':id',$postInfo['user_id']);
            $tableJoin->execute();
            $author = $tableJoin->fetch();
            echo 'by '.$author['name'];
             ?></a></h3>

             <h4 class="topic-date"><?php echo $postInfo['date']; ?></h4>

          </div>
          <hr>
          <div class="topic-details">
            <p class="topic-summary">
              <?php echo $postInfo['summary']; ?>
            </p>
          </div>
          <hr>
          <div class="topic-footer">
            <div class="topic-footer-comments">
              <button type="button" name="comment-button" class="comment-button"><img src="./assets/img/comment-icon.png" alt="Comments" /></button>
              <span class="topic-comments">
                <?php try {
                  $countComment = $pdo->prepare("SELECT * FROM comments WHERE post_id=:post_id");
                  $countComment->bindParam(':post_id',$postInfo['id']);
                  $countComment->execute();
                  if ($countComment->rowCount() > 0){
                    echo $countComment->rowCount();
                  }
                  else {
                    echo "0";
                  }
                } catch (PDOException $e) {
                  echo $e->getMessage;
                  die();
                }
                 ?>
              </span>
            </div>

            <div class="topic-footer-likes">
              <?php

                try {
                  $checkLiked = $pdo->prepare("SELECT * FROM likes WHERE user_id=:user_id AND post_id = :post_id");
                  $checkLiked->bindParam(':user_id',$id);
                  $checkLiked->bindParam(':post_id',$postInfo['id']);
                  $checkLiked->execute();
                  if ($checkLiked->rowCount() > 0) { ?>
                    <form class="form-likes" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'].'?page='.$_GET['page']);?>" method="post">
                      <input type="hidden" name="post-id" value="<?php echo $postInfo['id'];?>">
                      <button type="submit" name="increment-likes" class="like-button"><img src="./assets/img/thumbs-up-green.png" alt="Thumbs Up" /></button>
                    </form>
                  <?php }
                  else { ?>
                    <form class="form-likes" action="<?php if($user->is_loggedin()) {echo htmlspecialchars($_SERVER['PHP_SELF'].'?page='.$_GET['page']);}?>" method="post">
                      <input type="hidden" name="post-id" value="<?php echo $postInfo['id'];?>">
                      <button type="<?php if($user->is_loggedin()) {echo "submit";} else {echo "button";}?>" name="increment-likes" class="like-button"><img src="./assets/img/thumbs-up.png" alt="Thumbs Up" /></button>
                    </form>
                  <?php }
                } catch (PDOException $e) {
                  echo $e->getMessage();
                }
               ?>
             <span class="topic-likes">
                <?php
                  try {
                    $countLikes = $pdo->prepare("SELECT * FROM likes where post_id = :post_id");
                    $countLikes->bindParam(':post_id',$postInfo['id']);
                    $countLikes->execute();
                    $countLikesResult = $countLikes->rowCount();
                    echo $countLikesResult;

                    try {
                      $updateLikes = $pdo->prepare("UPDATE posts SET likes=:likes WHERE id=:post_id");
                      $updateLikes->bindParam(':likes',$countLikesResult);
                      $updateLikes->bindParam(':post_id',$postInfo['id']);
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
        </div>
        <?php } ?>
        <div class="pages-navigation">
          <?php
          if($_GET['page']!=1)
          {
             $previous =$_GET['page']-1;
             echo "<a class='other-pages' href='".$_SERVER['PHP_SELF']."?page=1'>First</a>";
             echo "<a class='other-pages' href='".$_SERVER['PHP_SELF']."?page=".$previous."'>Previous</a>";
          }

          for($i=1;$i<=$totalPages;$i++) {

            if ($i==$_GET['page']) {
              echo "<a class='current-page' href='".$_SERVER['PHP_SELF']."?page=".$i."'>".$i."</a>";
            }
            else {
              echo "<a class='other-pages' href='".$_SERVER['PHP_SELF']."?page=".$i."'>".$i."</a>";
            }
          }

          if($_GET['page']!=$totalPages)
          {
               $next=$_GET['page']+1;
               echo "<a class='other-pages' href='".$_SERVER['PHP_SELF']."?page=".$next."'>Next</a>";
               echo "<a class='other-pages' href='".$_SERVER['PHP_SELF']."?page=".$totalPages."'>Last</a>";
          }
           ?>
        </div>
        <br><br><br>

      </div>

    </div>

  </body>
</html>
