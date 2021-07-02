<?php
session_start();
if (isset($_POST["add"])) {
    echo "Checks passed";
    require_once "pdo.php";
    $first_name = $_POST["first_name"];
    $last_name = $_POST["last_name"];
    $user_id = $_SESSION["user_id"];
    $email = $_POST["email"];
    $headline = $_POST["headline"];
    $summary = $_POST["summary"];
    $sql = "INSERT INTO `Profile` ( `user_id`, `first_name`, `last_name`, `email`, `headline`, `summary`) VALUES ( :user_id, :first_name, :last_name, :email, :headline, :summary)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
        'user_id' => $user_id,
        ':first_name' => $first_name,
        ':last_name' => $last_name,
        ':email' => $email,
        ':headline' => $headline,
        ':summary' => $summary
    ));
    $sql = "SELECT profile_id from `Profile`";
    $profile_id = $pdo->lastInsertId();
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

    header("Location:index.php");
    $_SESSION["msg"] = "Added record";
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
    <?php if (isset($_SESSION["user_id"])) { ?>
    <p id="message" style="color:red;font-size:2em; ">
    </p>
    <form method="post" action="add.php" style="display:inline-grid">
        <label>First Name :</label>
        <input type="text" name="first_name" id="first_name">
        <label>Last Name :</label>
        <input type="text" name="last_name" id="last_name">
        <label>Email:</label>
        <input type="email" name="email" id="email">
        <label>Headline:</label>
        <input type="text" name="headline" id="headline">
        <label>Summary:</label>
        <textarea name="summary" id="summary">
        </textarea>
        <label>Position <button onclick="  addPosition(); return false; ">+</button></label>
        <div id="positions"></div>
        <BR>
        <input type="submit" value="Add" name="add" onclick="return verify(); ">
        <input type="submit" value="Cancel" onclick=" location.replace('index.php');return false;">

    </form>
    <?php } else {
        $_SESSION["err"] = "Access denied because you are not logged in";
        // echo $_SESSION["err"];
        header("Location:./");
        return;
    } ?>

    <script src="jquery.min.js"></script>
    <script type="text/javascript">
    let position = 0;

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
        return true;
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
                alert("HI");
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