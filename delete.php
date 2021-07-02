<?php
session_start();
if (isset($_POST["delete"])) {
    require_once "pdo.php";
    $profile_id = $_SESSION["delete_profile_id"];
    unset($_SESSION["delete_profile_id"]);
    $sql = "DELETE FROM `Profile` where profile_id= :profile_id ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(':profile_id' => $profile_id));
    $_SESSION['msg'] = "Delete done";
    header("Location:index.php");
    return;
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


    <?php

    if (isset($_GET["profile_id"])) {
        require_once "pdo.php";
        $profile = $_GET["profile_id"];
        if (!isset($_SESSION['user_id'])) {
            $_SESSION["err"] = "Access denied because you are not logged in";
            header("Location:index.php");
            return;
        } else {
            $user_id = $_SESSION['user_id'];
            $sql = "Select * from `Profile` where profile_id= :profile_id ";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(array(':profile_id' => $profile));
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row == false) {

                $_SESSION["err"] = "Access denied because no such profile exists";
                header("Location:index.php");
                return;
            } else if ($row["user_id"] != $user_id) {
                $_SESSION["err"] = "Access denied because this profile does not belong to you";
                header("Location:index.php");
                return;
            } else { ?>
    <h1>Deleting profile: </h1>
    <form action="delete.php" method="post" style="display:inline-grid">
        <label>First Name :</label>
        <?= htmlentities($row['first_name']) ?>
        <label>Last Name :</label>
        <?= htmlentities($row['last_name']) ?>
        <?php $_SESSION["delete_profile_id"] = $_GET["profile_id"]; ?>
        <input type="submit" value="Delete" name="delete">
        <input type="submit" value="Cancel" onclick=" location.replace('index.php');return false;">

    </form>

    <?php  }
        }
    } else {
        $_SESSION["err"] = "Profile id not set";
        header("Location:index.php");
        return;
    }
    ?>

</body>

</html>