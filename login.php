<?php
if (isset($_POST["submitLogin"])) {
    $email    = $_POST['emailLogin'];
    $password = $_POST['passwordLogin'];
    if (!empty($email) && !empty($password)) {
        if ($user->login($email, $password)) {
            $displayLogin = 0;
            $user->redirect($_SERVER['PHP_SELF'] . "?" . $_SERVER['QUERY_STRING']);
        } else {
            $displayLogin = 1;
        }
    }
}
?>
