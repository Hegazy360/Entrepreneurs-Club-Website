<?php
class USER //USER class contains all user related functions
{
    private $db;
    function __construct($pdo)
    {
        $this->db = $pdo; //set this instance's db to the passed pdo
    }
    public function signup($name, $email, $password, $profession) 
    {
        try {
            $password_hash = password_hash($password . 'super7', PASSWORD_DEFAULT);
            $stmt          = $this->db->prepare("Insert into info (email,name,password,profession) VALUES (:email,:name,:password,:profession)");
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $password_hash);
            $stmt->bindParam(':profession', $profession);
            $stmt->execute();
            return $stmt;
        }
        catch (PDOException $e) {
            echo $e->getMessage();
            die();
        }
    }
    public function login($email, $password)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM info WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $result = $stmt->fetch(); //put results in $result array;
            if ($stmt->rowCount() > 0) {
                if (password_verify($password . 'super7', $result['password'])) {
                    $_SESSION['user'] = $result['id'];
                    return true;
                } else {
                    return false;
                }
            }
        }
        catch (PDOException $e) {
            echo $e->getMessage();
            kill();
        }
    }
    public function is_loggedin()
    {
        if (isset($_SESSION['user'])) {
            return true;
        } else {
            return false;
        }
    }
    public function redirect($url)
    {
        header("Location: $url");
    }
    public function logout()
    {
        session_destroy();
        unset($_SESSION['user']);
        return true;
    }
}
?>
