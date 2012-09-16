<?php

include 'config.inc.php';
include 'users.class.php';
include 'debts.class.php';
include 'payments.class.php';

$error = "";

// Check values are not empty

if (array_filter($_POST['user_id'])) {
    $debtees = $_POST['user_id'];
} else {
    $error .= "<p>Please select at least one user.</p>";
}

if (array_filter($_POST['user_id'])) {
    $drinks_owed = $_POST['drinks_owed'];
} else {
    $error .= "<p>Please make sure all <strong>Drinks</strong> fields are completed.</p>";
}

if (array_filter($_POST['user_id'])) {
    $food_owed = $_POST['food_owed'];
} else {
    $error .= "<p>Please make sure all <strong>Food</strong> fields are completed.</p>";
}

// Check values are numeric

if (isset($drinks_owed)) {
    foreach ($drinks_owed as $drink_cost) {
        if (!is_numeric($drink_cost))
            $error .= "<p>Please make sure all drinks fields are numbers.</p>";
    }
}

if (isset($food_owed)) {
    foreach ($food_owed as $food_cost) {
        if (!is_numeric($food_cost))
            $error .= "<p>Please make sure all food fields are numbers.</p>";
    }
}


if ($error) {
    echo "<div class='alert alert-error'><button type='button' class='close' data-dismiss='alert'>×</button>" . $error . "</div>";
} else {
    // Connect to database
    $con = mysql_connect(DB_HOST, DB_USER, DB_PW);
    if (!$con) {
        die('Could not connect: ' . mysql_error());
    }

    mysql_select_db(DB_NAME, $con);

    $user_id = $_COOKIE['user_id'];

    $debtObj = new Debt();
    $userObj = new User();
    $paymentObj = new Payment();

    $userObj->setId($user_id);
    $userObj->load();
    
    $total = 0;

    $paymentObj = new Payment();
    $paymentObj->setUser_id($user_id);

    $paymentObj->create();

    $payment_id = mysql_insert_id();

    $debtObj = new Debt();
    $debtObj->setOwed_to($user_id);
    $debtObj->setPayment_id($payment_id);
    $total = 0;

    for ($i = 0; $i < count($_POST["user_id"]); $i++) {
        $debtObj->setOwed_by($_POST['user_id'][$i]);
        $debt_total = $_POST['drinks_owed'][$i] + $_POST['food_owed'][$i];
        $total += $debt_total;
        $debtObj->setAmount($debt_total);
        $debtObj->create();
        //echo "<p>" . $_POST['user_id'][$i] . " owes " . $_COOKIE['user_id'] . " &pound;" . $_POST['drinks_owed'][$i] . " for drinks and &pound;" . $_POST['food_owed'][$i] . " for food for a total of &pound;" . $total . "</p>";
    }

    $paymentObj->setId($payment_id);
    $paymentObj->setTotal($total);
    $paymentObj->setDate($_POST['date']);
    $paymentObj->update();
    
    mysql_close($con);

    echo "<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>×</button>Payment added successfully</div>";
}
?>