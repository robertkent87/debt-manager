<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

$user_str = "";

if (isset($_COOKIE['user_id'])) {
    $user_str = "<p>Logged in as: <strong>" . $userObj->getName() . "</strong> <a  href='logout.php'>Log out</a></p>";
}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>Weather Debt Manager</title>
        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
        <script type="text/javascript" src="bootstrap/js/bootstrap.js"></script>
        <script type="text/javascript" src="bootstrap/js/bootstrap-datepicker.js"></script>
        <script type="text/javascript" src="js/jquery.tablesorter.js"></script>
        <link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.min.css" media="all" />
        <link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap-responsive.min.css" media="all" />
        <link rel="stylesheet" type="text/css" href="bootstrap/css/datepicker.css" media="all" />
        <link href='http://fonts.googleapis.com/css?family=Kreon:400,700' rel='stylesheet' type='text/css'>
        <link href='http://fonts.googleapis.com/css?family=Droid+Sans' rel='stylesheet' type='text/css'>
        <link rel="stylesheet" type="text/css" href="css/style.css" media="all" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>