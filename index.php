<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vasvi Sood</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <H1>Profile uploading</H1>
    <p id="message" style="color:red;font-size:2em; ">
        <?php if (isset($_SESSION['err'])) {
            echo $_SESSION['err'];
            unset($_SESSION['err']);
        } ?>
    </p>
    <p style="color:green;font-size:2em; ">
        <?php if (isset($_SESSION['msg'])) {
            echo $_SESSION['msg'];
            unset($_SESSION['msg']);
        } ?>
    </p>
    <?php if (isset($_SESSION['user_id'])) {
        $currentUser = $_SESSION['user_id'];
        echo $_SESSION['user_id'];
    ?>
    <a href="logout.php">Logout</a>
    <a href="add.php">Add new profile</a>
    <H1>Your Profiles:</H1>
    <table border="1">
        <th>Name</th>
        <th>Headline</th>
        <th>Action</th>
        <?php
            require_once "pdo.php";
            $sql = "Select * from `Profile` where user_id=$currentUser";
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>";
                echo "<td>";
                $profile_id = $row["profile_id"];
                echo "<a href='view.php?profile_id=$profile_id'>" . htmlentities($row["first_name"] . " " . $row["last_name"]) . "</a>";
                echo "</td>";
                echo "<td>";
                echo htmlentities($row["headline"]);
                echo "</td>";
                echo "<td>";
                echo "<a href='edit.php?profile_id=$profile_id'>Edit</a> " . "<a href='delete.php?profile_id=$profile_id'>Delete</a> ";
                echo "</td>";
                echo "</tr>";
            } ?>
    </table>
    <hr>
    <?php } else { ?>
    <a href="login.php">Login</a>
    <?php } ?>
    <br>
    <br>
    <H1>Other's Profiles:</H1>
    <table border="1">

        <th>Name</th>
        <th>Headline</th>

        <?php
        require_once "pdo.php";
        $currentUser = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : -1;
        $sql = "Select * from `Profile` where user_id!=$currentUser";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td>";
            $profile_id = $row["profile_id"];
            echo "<a href='view.php?profile_id=$profile_id'>" . htmlentities($row["first_name"] . " " . $row["last_name"]) . "</a>";
            echo "</td>";
            echo "<td>";
            echo htmlentities($row["headline"]);
            echo "</td>";
            echo "</tr>";
        }
        ?>
    </table>
</body>

</html>