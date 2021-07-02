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
    session_start();
    if (isset($_GET["profile_id"])) {
        require_once "pdo.php";
        $profile = $_GET["profile_id"];
        $sql = "Select * from `Profile` where profile_id= :profile_id ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(':profile_id' => $profile));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row == false) {
            $_SESSION["err"] = "Profile does not exists";
            header("Location:index.php");
            return;
        } else {
            echo "<table>";
            echo "<tr><th>Profile Information</th>" . "</tr>";
            echo "<tr><td>First name : " . htmlentities($row['first_name']) . "</td></tr>";
            echo "<tr><td>Last name : " . htmlentities($row['last_name']) . "</td></tr>";
            echo "<tr><td>Email : " . htmlentities($row['email']) . "</td></tr>";
            echo "<tr><td>Headline : " . htmlentities($row['headline']) . "</td></tr>";
            echo "<tr><td>Summary: " . htmlentities($row['summary']) . "</td></tr>";

            $sql = "SELECT * FROM `Position` where profile_id= :profile_id ORDER BY rank";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(array(':profile_id' => $profile));

            echo "<tr><th>Position </th>" . "</tr>";
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr><td>Year : " . htmlentities($row["year"]) . "</td></tr>";

                echo "<tr><td>Description: " . htmlentities($row['description']) . "</td></tr>";
            }

            $sql = "SELECT * FROM `Education` where profile_id= :profile_id ORDER BY rank";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(array(':profile_id' => $profile));

            echo "<tr><th>Education </th>" . "</tr>";
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

                echo "<tr><td>Year : " . htmlentities($row["year"]) . "</td></tr>";
                $institution_id = $row["institution_id"];
                $sql2 = "SELECT * FROM `Institution` WHERE institution_id = :institution_id";
                $stmt2 = $pdo->prepare($sql2);
                $stmt2->execute(array(':institution_id' => $institution_id));
                $institution = $stmt2->fetch(PDO::FETCH_ASSOC);
                echo "<tr><td>School: " . htmlentities($institution["name"]) . "</td></tr>";
            }

            echo "</table>";
        }
    } else {
        $_SESSION["err"] = "Profile id not set";
        header("Location:index.php");
        return;
    } ?>

</body>

</html>