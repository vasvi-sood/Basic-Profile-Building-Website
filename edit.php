<?php
session_start();
$position = 0;
$education = 0;
if (isset($_POST["edit"])) {
    echo "Checks passed";
    require_once "pdo.php";

    $profile_id = $_SESSION["delete_profile_id"];
    unset($_SESSION["delete_profile_id"]);
    $first_name = $_POST["first_name"];
    $last_name = $_POST["last_name"];
    $email = $_POST["email"];
    $headline = $_POST["headline"];
    $summary = $_POST["summary"];
    $sql = "Update `Profile` set  `first_name`=:first_name , `last_name`=:last_name , `email`=:email, `headline`=:headline , `summary`=:summary where profile_id=$profile_id ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
        ':first_name' => $first_name,
        ':last_name' => $last_name,
        ':email' => $email,
        ':headline' => $headline,
        ':summary' => $summary
    ));


    $sql = "DELETE FROM `Position` where profile_id= :profile_id ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(':profile_id' => $profile_id));

    $i = 1;
    while (isset($_POST["positionyear$i"])) {
        $year = $_POST["positionyear$i"];
        $description = $_POST["description$i"];
        $sql = "INSERT INTO `Position` ( `profile_id`, `rank`, `year`, `description`) VALUES ( :profile_id, :rank, :year, :description)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(
            ':profile_id' => $profile_id,
            ':rank' => $i,
            ':year' => $year,
            ':description' => $description,
        ));
        $i++;
    }
    $sql = "DELETE FROM `Education` where profile_id= :profile_id ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(':profile_id' => $profile_id));

    $j = 1;
    while (isset($_POST["educationyear$j"])) {
        $year = $_POST["educationyear$j"];
        $school = $_POST["school$j"];
        $sql = "SELECT institution_id FROM `Institution` where name=:school";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(
            ':school' => $school
        ));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $institution_id;
        if ($row == false) {
            $sql = "INSERT INTO `Institution` (name) VALUE (:school)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(array(
                ':school' => $school
            ));
            $institution_id = $pdo->lastInsertId();
        } else {
            $institution_id = $row["institution_id"];
        }

        $sql = "INSERT INTO `Education` ( `profile_id`,`institution_id`, `rank`, `year`) VALUES ( :profile_id, :institution_id, :rank, :year)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(
            ':profile_id' => $profile_id,
            ':institution_id' => $institution_id,
            ':rank' => $j,
            ':year' => $year
        ));
        $j++;
    }

    $_SESSION["msg"] = "Editted record";

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

    <p id="message" style="color:red;font-size:2em; ">
    </p>
    <h1>Edit details: </h1>
    <form action="edit.php" method="post" style="display:inline-grid">
        <label>First Name :</label>
        <input type="text" name="first_name" id="first_name" value="<?= htmlentities($row['first_name']) ?>">
        <label>Last Name :</label>
        <input type="text" name="last_name" id="last_name" value="<?= htmlentities($row['last_name']) ?>">
        <label>Email</label>
        <input type="text" name="email" id="email" value="<?= htmlentities($row['email']) ?>">
        <label>Headline</label>
        <input type="text" name="headline" id="headline" value="<?= htmlentities($row['headline']) ?>">
        <label>Summary</label>
        <input type="text" name="summary" id="summary" value="<?= htmlentities($row['summary']) ?>">
        <label>Position <button onclick="  addPosition(); return false; ">+</button></label>

        <div id="positions">
            <?php $sql = "SELECT * FROM `Position` where profile_id= :profile_id ORDER BY rank";
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute(array(':profile_id' => $profile));


                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            $position++; ?>

            <div id="position<?= $row["rank"]; ?>" style="display: block;">
                <label>Year</label><input type="number" id="positionyear<?= $row["rank"]; ?>"
                    name="positionyear<?= $row["rank"]; ?>"
                    value='<?= htmlentities($row["year"]) ?>'><label>Description</label><textarea
                    id="description<?= $row["rank"]; ?>" name="description<?= $row["rank"]; ?>">
                    <?= htmlentities($row["description"]) ?>
        </textarea>
                <button id="minus<?= $row["rank"]; ?>">-</button>
            </div>
            <?php   } ?>
        </div>
        <label>Education <button onclick="  addEducation(); return false; ">+</button></label>
        <div id="educations">
            <?php $sql = "SELECT * FROM `Education` where profile_id= :profile_id ORDER BY rank";
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute(array(':profile_id' => $profile));
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            $education++;
                            $institution_id = $row["institution_id"];
                            $sql2 = "SELECT * FROM `Institution` WHERE institution_id = :institution_id";
                            $stmt2 = $pdo->prepare($sql2);
                            $stmt2->execute(array(':institution_id' => $institution_id));
                            $institution = $stmt2->fetch(PDO::FETCH_ASSOC);

                        ?>
            <div id="education<?= $row["rank"]; ?>" style="display: block;">
                <label>Year</label><input type="number" id="educationyear<?= $row["rank"]; ?>"
                    name="educationyear<?= $row["rank"]; ?>"
                    value='<?= htmlentities($row["year"]) ?>'><label>School</label><input type="text"
                    id="school<?= $row["rank"]; ?>" name="school<?= $row["rank"]; ?>"
                    value='<?= htmlentities($institution["name"]) ?>'>
                </input>
                <button id="eminus<?= $row["rank"]; ?>">-</button>
            </div>
            <?php   } ?>
        </div>


        <BR>
        <?php $_SESSION["delete_profile_id"] = $_GET["profile_id"]; ?>
        <input type="submit" value="Edit" name="edit" onclick="return verify();">
        <input type="submit" value="Cancel" onclick=" location.replace('index.php');return false;">

    </form>

    <?php  }
        }
    } else {
        // $_SESSION["err"] = "Profile id not set";
        // header("Location:index.php");
        // return;
    }
    ?>

    <script src="jquery.min.js"></script>
    <script type="text/javascript">
    let position = <?= $position ?>;
    let education = <?= $education ?>;
    $(document).ready(function() {
        for (let i = 1; i <= position; i++)
            document.getElementById(`minus${i}`).addEventListener("click", function(e) {
                removePosition(e.target.id);
                e.preventDefault();

            });
        for (let i = 1; i <= education; i++)
            document.getElementById(`eminus${i}`).addEventListener("click", function(e) {
                removeEducation(e.target.id);
                e.preventDefault();

            });
    });

    function verify() {

        let first_name = $("#first_name").val();
        first_name = first_name.trim();
        let last_name = $("#last_name").val();
        last_name = last_name.trim();
        let email = $("#email").val();
        email = email.trim();
        let headline = $("#headline").val();
        headline = headline.trim();
        let summary = $("#summary").val();
        summary = summary.trim();
        console.log(summary);
        if (first_name.length <= 0 || last_name.length <= 0 || email.length <= 0 || headline.length <= 0 || summary
            .length <= 0) {

            $("#message").html("All fields are required");
            return false;
        }
        let i = 1;
        while ($(`#position${i}`).length > 0) {
            // console.log("checking", $(`#position${i}`));
            console.log($(`#positionyear${i}`).val().trim().length, $(`#description${i}`).val().trim().length);
            if ($(`#positionyear${i}`).val().trim().length <= 0 || $(`#description${i}`).val().trim().length <= 0) {
                $("#message").html("All fields are required");
                return false;
            }
            i++;
        }
        let j = 1;
        while ($(`#education${j}`).length > 0) {
            console.log($(`#educationyear${j}`).val().trim().length, $(`#school${j}`).val().trim().length);
            if ($(`#educationyear${j}`).val().trim().length <= 0 || $(`#school${j}`).val().trim().length <= 0) {
                $("#message").html("All fields are required");
                return false;
            }
            j++;
        }
        return true;
    }

    function addEducation() {
        if (education >= 9)
            alert("Only 9 possitions are allowed");
        else {

            education++;
            var child = document.createElement("div");
            var minus = document.createElement("button");
            minus.id = `eminus${education}`;
            minus.innerHTML = "-";
            child.id = `education${education}`;
            minus.addEventListener("click", function(e) {
                removeEducation(e.target.id);

                e.preventDefault();

            });


            child.innerHTML = `<label>Year</label><input type="number" id="educationyear${education}" name="educationyear${education}"><label>School</label><input type="text" id="school${education}" name="school${education}">
        </input>`;
            child.append(minus);
            $(child).css("display", "block");
            console.log(child.id);

            $("#educations").append(child);


        }

    }

    function addPosition() {
        if (position >= 9)
            alert("Only 9 possitions are allowed");
        else {

            position++;
            var child = document.createElement("div");
            var minus = document.createElement("button");
            minus.id = `minus${position}`;
            minus.innerHTML = "-";
            child.id = `position${position}`;
            minus.addEventListener("click", function(e) {
                removePosition(e.target.id);
                e.preventDefault();

            });


            child.innerHTML = `<label>Year</label><input type="number" id="positionyear${position}" name="positionyear${position}"><label>Description</label><textarea id="description${position}" name="description${position}">
        </textarea>`;
            child.append(minus);
            $(child).css("display", "block");
            console.log(child.id);

            $("#positions").append(child);


        }

    }

    function removeEducation(minusid) {
        id = minusid.substring(6);
        console.log("hiding", id);
        let i = Number(id) + 1;

        //while education id exists

        $(`#education${id}`).remove();

        for (; i <= 9; i++) {

            console.log(`renaming ${i} to ${i-1}`);

            $(`#education${i}`).attr('id', `education${i-1}`);
            $(`#eminus${i}`).attr('id', `eminus${i-1}`);
            $(`#educationyear${i}`).attr('name', `educationyear${i-1}`);
            $(`#educationyear${i}`).attr('id', `educationyear${i-1}`);
            $(`#school${i}`).attr('name',
                `school${i-1}`);
            $(`#school${i}`).attr('id', `school${i-1}`);


        }
        education--;
        return false;
    }

    function removePosition(minusid) {
        id = minusid.substring(5);
        console.log("hiding", id);
        let i = Number(id) + 1;

        //while position id exists
        $(`#position${id}`).remove();
        for (; i <= 9; i++) {
            console.log(`renaming ${i} to ${i-1}`);
            $(`#position${i}`).attr('id', `position${i-1}`);
            $(`#minus${i}`).attr('id', `minus${i-1}`);
            $(`#positionyear${i}`).attr('name', `positionyear${i-1}`);
            $(`#positionyear${i}`).attr('id', `positionyear${i-1}`);
            $(`#description${i}`).attr('name',
                `description${i-1}`);
            $(`#description${i}`).attr('id', `description${i-1}`);

        }
        position--;

        return false;
    }
    </script>


</body>

</html>