<?php
include_once 'dbconfig.php';
if (!isset($_GET['id']) || $_GET['id'] == "") {
    $user->redirect('home.php');
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
if (isset($_POST['sign-out-header'])) {
    $user->logout();
    $user->redirect('home.php');
}
if (isset($_POST['ban-user'])) {
    try {
        $banUser = $pdo->prepare("UPDATE info SET ban=1 WHERE id=:id");
        $banUser->bindParam(':id', $_GET['id']);
        $banUser->execute();
    }
    catch (PDOException $e) {
        echo $e->getMessage();
    }
}
if (isset($_POST['unban-user'])) {
    try {
        $unbanUser = $pdo->prepare("UPDATE info SET ban=0 WHERE id=:id");
        $unbanUser->bindParam(':id', $_GET['id']);
        $unbanUser->execute();
    }
    catch (PDOException $e) {
        echo $e->getMessage();
    }
}
if (isset($_POST['set-admin'])) {
    try {
        $setAdmin = $pdo->prepare("UPDATE info SET admin=1 WHERE id=:id");
        $setAdmin->bindParam(':id', $_GET['id']);
        $setAdmin->execute();
    }
    catch (PDOException $e) {
        echo $e->getMessage();
    }
}
if (isset($_POST['unset-admin'])) {
    try {
        $unsetAdmin = $pdo->prepare("UPDATE info SET admin=0 WHERE id=:id");
        $unsetAdmin->bindParam(':id', $_GET['id']);
        $unsetAdmin->execute();
    }
    catch (PDOException $e) {
        echo $e->getMessage();
    }
}
try {
    $id   = $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM info where id = :id ");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
        $userInfo = $stmt->fetch();
        if (($userInfo['ban'] == 1) && ($checkVisitor['id'] != $userInfo['id']) && ($checkVisitor['admin'] != 1)) {
            die();
            $user->redirect('home.php');
        }
    } else {
        $user->redirect('home.php');
    }
}
catch (PDOException $e) {
    echo $e->getMessage();
}
try {
    $allPosts = $pdo->prepare("SELECT * FROM posts ORDER BY likes DESC");
    $allPosts->execute();
}
catch (PDOException $e) {
    echo $e->getMessage();
}
if (isset($_POST['submit_image'])) {
    $fileName    = explode(".", $_FILES["profile_picture"]["name"]);
    $fileNewName = $userInfo['id'] . '.' . end($fileName);
    $folder      = "D:/xampp/htdocs/entre/users_image/";
    $path        = $folder . $fileNewName;
    move_uploaded_file($_FILES["profile_picture"]["tmp_name"], "$path");
    try {
        $imageUpload = $pdo->prepare("UPDATE info SET profile_picture = :fileNewName WHERE id = :userId");
        $imageUpload->bindParam(':fileNewName', $fileNewName);
        $imageUpload->bindParam(':userId', $userInfo['id']);
        $imageUpload->execute();
        $stmt->execute();
        $userInfo = $stmt->fetch();
    }
    catch (PDOException $e) {
        echo $e->getMessage();
    }
}
if (isset($_POST['save-edit-about'])) {
    try {
        $about       = trim($_POST['user-about']);
        $aboutUpdate = $pdo->prepare("UPDATE info SET about = :about WHERE id = :userId");
        $aboutUpdate->bindParam(':about', $about);
        $aboutUpdate->bindParam(':userId', $userInfo['id']);
        $aboutUpdate->execute();
        $stmt->execute();
        $userInfo = $stmt->fetch();
    }
    catch (PDOException $e) {
        echo $e->getMessage();
    }
}
if (isset($_POST['save-edit-interests'])) {
    try {
        $interests       = trim($_POST['user-interests']);
        $interestsUpdate = $pdo->prepare("UPDATE info SET interests = :interests WHERE id = :userId");
        $interestsUpdate->bindParam(':interests', $interests);
        $interestsUpdate->bindParam(':userId', $userInfo['id']);
        $interestsUpdate->execute();
        $stmt->execute();
        $userInfo = $stmt->fetch();
    }
    catch (PDOException $e) {
        echo $e->getMessage();
    }
}
?>
 <!DOCTYPE html>
 <html>
   <head>
     <meta charset="utf-8">
     <title>Profile</title>
     <link rel="stylesheet" href="assets/css/main.css" charset="utf-8">
     <link rel="stylesheet" href="assets/css/header-style.css" charset="utf-8">
     <link rel="stylesheet" href="assets/css/profile-style.css" charset="utf-8">
     <link href='https://fonts.googleapis.com/css?family=Fira+Sans:400,700,400italic' rel='stylesheet' type='text/css'>
     <link href='https://fonts.googleapis.com/css?family=Courgette' rel='stylesheet' type='text/css'>
     <script type="text/javascript" src="assets/js/main.js" async></script>
   </head>
   <body>


     <div class="header">
         <div class="header-logo">
             <a href="welcome.php"><img src="assets/img/bulb-logo.png" alt="Path Logo" /></a>

             <h3><a href="welcome.php">Entrepreneurs Club</a></h3>
         </div>
         <?php if($user->is_loggedin()) { ?>
         <div class="header-sign-buttons">
           <form class="header-buttons" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'].'?id='.$userInfo['id']);?>" method="post">

             <button type="submit" name="sign-out-header" id="sign-out-header">
                 Sign Out
             </button>
           </form>
         </div>
         <?php }  ?>

     </div>


     <div class="home-container">
          <div class="flex-row">
            <div class="user-overview">
              <h1 class="user-name">
                <?php echo $userInfo['name']; ?>
              </h1>
              <?php if($userInfo['profession'] == 0){ ?>
                <h3 class="profession">Entrepreneur</h3>
              <?php } else { ?>
                <h3 class="profession">Investor</h3>
                <?php } ?>
              <div class="user-image">
                <img class="user-image-box" src="./users_image/<?php if($userInfo['profile_picture']!=$userInfo['id'].".") {echo $userInfo['profile_picture'];} else {echo "default.jpg";} ?>" alt="Profile Picture" />
                <?php if($user->is_loggedin() && (($_GET['id'] == $_SESSION['user'])||($checkVisitor['admin']))) { ?>
                <form class="form-upload" method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'].'?id='.$userInfo['id']);?>" enctype="multipart/form-data">
                  <input type="file" name="profile_picture">
                  <input type="submit" name="submit_image" value="Update profile picture">
               </form>
               <?php } ?>
              </div>
              <div class="user-info">

                  <form class="form-about" method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'].'?id='.$userInfo['id']);?>">
                    <label for="user-about">About</label>
                    <?php if($user->is_loggedin() && ($_GET['id'] == $_SESSION['user'] || ($checkVisitor['admin']==1))) {
                    if (!isset($_POST['edit-about'])) { ?>
                    <button class="about-button" type="submit" name="edit-about"><img src="./assets/img/edit-icon.png" alt="Edit About" /></button>
                    <?php } else { ?>
                   <button class="about-button" type="submit" name="save-edit-about"><img src="./assets/img/save-icon.png" alt="Save About" /></button>
                   <?php }} ?>
                    <textarea class="user-about" name="user-about" placeholder="About <?php echo $userInfo['name'];?>" <?php if(!isset($_POST['edit-about'])) { echo "readonly";} ?>><?php echo $userInfo['about']; ?></textarea>

                  </form>
                  <form class="form-interests" method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'].'?id='.$userInfo['id']);?>">
                    <label for="user-interests">Interests</label>
                    <?php if($user->is_loggedin() && ($_GET['id'] == $_SESSION['user'] || ($checkVisitor['admin']==1))) {
                    if (!isset($_POST['edit-interests'])) { ?>
                    <button class="interests-button" type="submit" name="edit-interests"><img src="./assets/img/edit-icon.png" alt="Edit Interests" /></button>
                    <?php } else { ?>
                   <button class="interests-button" type="submit" name="save-edit-interests"><img src="./assets/img/save-icon.png" alt="Save Interests" /></button>
                   <?php }} ?>
                    <textarea class="user-interests" name="user-interests" placeholder="Interests" <?php if(!isset($_POST['edit-interests'])) { echo "readonly";} ?>><?php echo $userInfo['interests']; ?></textarea>
                  </form>
                  <div class="form-contact">
                    <h3>Contact <?php echo $userInfo['name']; ?> :</h3>
                    <a href="mailto:<?php echo $userInfo['email']; ?>" target="_blank"><?php echo $userInfo['email']; ?></a>
                  </div>
                  <div class="profile-posts-container">
                    <?php while (($allPostsInfo = $allPosts->fetch()) !== false) {
                      if($allPostsInfo['user_id']==$_GET['id']){ ?>
                        <div class="profile-post">
                          <div class="profile-post-header">
                            <a class="profile-post-title" href="post.php?post-id=<?php echo $allPostsInfo['id'];?>"><?php echo $allPostsInfo['title'];?></a>
                          </div>
                          <div class="profile-post-body">
                            <div class="profile-comments-section">
                              <button type="button" class="profile-comment-button"><img src="./assets/img/comment-icon.png" alt="Comments" /></button>
                              <span class="profile-comments-counter">
                                <?php try {
                                $countprofilePostComments = $pdo->prepare("SELECT * FROM comments WHERE post_id = :user_post_id");
                                $countprofilePostComments->bindParam(':user_post_id',$allPostsInfo['id']);
                                $countprofilePostComments->execute();
                                echo $countprofilePostComments->rowCount();
                              } catch (PDOException $e) {
                                echo $e->getMessage();
                              }
                               ?></span>
                            </div>
                            <div class="profile-likes-section">
                              <button type="button" class="profile-like-button"><img src="./assets/img/thumbs-up.png" alt="Likes" /></button>
                              <span class="profile-likes-counter">
                                <?php try {
                                $countprofilePostLikes = $pdo->prepare("SELECT * FROM likes WHERE post_id = :user_post_id");
                                $countprofilePostLikes->bindParam(':user_post_id',$allPostsInfo['id']);
                                $countprofilePostLikes->execute();
                                echo $countprofilePostLikes->rowCount();
                              } catch (PDOException $e) {
                                echo $e->getMessage();
                              }

                               ?></span>
                            </div>


                          </div>
                          <hr class=".profile-posts-separator">

                        </div>
                      <?php }
                    } ?>
                  </div>
              </div>
            </div>
            <?php if($user->is_loggedin() && $checkVisitor['admin'] == 1) { ?>
            <div class="admin-control">

              <form class="admin-control-form" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'].'?id='.$userInfo['id']);?>" method="post">
                <?php if($userInfo['admin']==0) { ?>
                <button type="submit" name="set-admin">Set as admin</button><hr>
                <?php } else { ?>
                <button type="submit" name="set-admin">Remove admin</button><hr>
                <?php } ?>

               <?php if($userInfo['ban']==0){ ?>
                <button type="submit" name="ban-user">Ban user</button><hr>
                <?php } else { ?>
                  <button type="submit" name="unban-user">Unban user</button><hr>
                  <?php } ?>
              </form>
            </div>
            <?php } ?>
          </div>


     </div>





   </body>
 </html>
