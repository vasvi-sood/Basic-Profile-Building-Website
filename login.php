<?php
session_start();
if (isset($_POST["submit"])) {
    require_once "pdo.php";
    $password = $_POST["password"];
    $email = trim($_POST["email"]);
    $_SESSION["typed_in_email"] = $_POST["email"];

    $sql = "Select * from `users` where email= :email";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
        ':email' => $email,
    ));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row == false) {
        $_SESSION["err"] = "Email does not exists";
        header("Location:login.php");
        return;
    } else {
        if (password_verify($password, $row['password']) == false) {
            $_SESSION["err"] = "Passwords do not match";
            header("Location:login.php");
            return;
        }
        $_SESSION['email'] = $email;
        $_SESSION['user_id'] = $row["user_id"];
        header("Location:index.php");
        return;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <p id="message" style="color:red;font-size:2em; ">
        <?php if (isset($_SESSION['err'])) {
            echo $_SESSION['err'];
            unset($_SESSION['err']);
        } ?>
    </p>
    <form action="login.php" method="post" style="display:inline-grid">
        <label>Email</label>
        <input type="text" name="email" id="email" value="<?php if (isset($_SESSION['typed_in_email'])) {
                                                                echo htmlentities($_SESSION['typed_in_email']);
                                                                unset($_SESSION['typed_in_email']);
                                                            }  ?>">
        <label>Password</label>
        <input type="password" name="password" id="pass">
        <input type="submit" name="submit" value="Submit" onclick="return verifyEmail();">

    </form>
    <Script src="jquery.min.js"></Script>
    <script type="text/javascript">
    $(document).ready(function() {

    })

    function verifyEmail() {
        let email = $("#email").val();
        email = email.trim();
        let pass = $('#pass').val();
        let ans = email.indexOf("@");
        if (ans < 0 || email.length < 1) {
            $("#message").html("Email address is not valid");
            return false;
        }
        if (pass.length < 1) {
            $("#message").html("Password field cannot be empty");
            return false;
        }

        return true;
    }
    </script>
</body>

</html>