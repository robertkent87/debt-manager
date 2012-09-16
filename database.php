<?php

// Connect to database
$con = mysql_connect(DB_HOST, DB_USER, DB_PW);
if (!$con) {
    die('Could not connect: ' . mysql_error());
}

mysql_select_db(DB_NAME, $con);

?>
