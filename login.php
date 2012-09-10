<?php
include 'config.inc.php';
include 'users.class.php';

// If form is submitted, process 'login'
if (isset($_POST['user_id'])):
    setcookie("user_id", $_POST['user_id'], time() + 3600);
    header("Location: dashboard.php");
    exit();
else:
    // Connect to database
    $con = mysql_connect(DB_HOST, DB_USER, DB_PW);
    if (!$con) {
        die('Could not connect: ' . mysql_error());
    }

    mysql_select_db(DB_NAME, $con);

    $userObj = new User;

    // Get list of users for form
    $user_arr = User::getAll();

    // Build login form

    $form_str = "<div id=\"login\">";

    foreach ($user_arr as $user_id) {
        $userObj->setId($user_id);
        $userObj->load();

        $image = file_exists($userObj->getImage()) ? $userObj->getImage() : 'user.png';

        $form_str .= "<form action=" . $_SERVER['PHP_SELF'] . " method='post' class='form-inline'>";
        $form_str .= "<input type='hidden' name='user_id' value='$user_id'/>";
        $form_str .= "<input type='submit' value='' class='btn' style='background: url(images/$image) no-repeat center center' />";
        $form_str .= "<p>" . $userObj->getName() . "</p>";
        $form_str .= "</form>";
    }

    $form_str .= "</div>";

    include 'header.php';
    ?>
    <div id="" class="header container">
        <h1>Weather Debt Manager</h1>
        <div id="user">
            <?php echo $user_str; ?>
        </div>
    </div>
    <div class="container">
        <h3>Log In</h3>


        <?php echo $form_str; ?>
    </div>
    <?php
    include 'footer.php';
    mysql_close($con);
endif;
?>