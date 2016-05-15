<?php
if (isset($_POST["submitSignup"])) {
    $name            = trim($_POST['nameSignup']); //trim white space
    $email           = trim($_POST['emailSignup']);
    $emailConfirm    = trim($_POST['emailConfirm']);
    $password        = trim($_POST['passwordSignup']);
    $passwordConfirm = trim($_POST['passwordConfirm']);
    $profession      = $_POST['profession'];
    if (!empty($name) && !empty($email) && !empty($emailConfirm) && !empty($password) && !empty($passwordConfirm)) {
        try {
            $stmt = $pdo->prepare("Select email from info where email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $displaySignup = 1;
                echo "
        <script type=\"text/javascript\">
        alert(\"Email already taken\");
        </script>
        ";
            } else {
                $displaySignup = 0;
                if ($user->signup($name, $email, $password, $profession)) {
                    $user->login($email, $password);
                    $user->redirect($_SERVER['PHP_SELF'] . "?" . $_SERVER['QUERY_STRING']);
                }
            }
        }
        catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
}
?>
