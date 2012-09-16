<?php
include 'header.php';
include 'users.class.php';
include 'debts.class.php';
include 'payments.class.php';

$userObj = new User();

$userObj->setId($user_id);
$userObj->load();
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
        <div id="" class="header container">
            <div class="row">
                <div class="span6">
                    <h1>Weather Debt Manager</h1>
                    <div id="user">
                        <?php
                        if (isset($_COOKIE['user_id']))
                            echo "<p>Logged in as: <strong>" . $userObj->getName() . "</strong> <a  href='logout.php'>Log out</a></p>";
                        ?>
                    </div>
                </div>
                <div class="span6">
                    <div id="countdown">
                        <?php
                        if (date("l") == "Friday") {
                            
                        } else {
                            $count = round((strtotime("next Friday") - strtotime(date("Y-m-d H:i:s"))) / 86400) + 1;
                            echo "<p><strong>" . $count . "</strong> days until Friday</p>";
                        }
                        ?>
                    </div>
                    <p>
                        <a class="btn btn-large btn-primary" id="add_debt" data-toggle="modal" href="#addModal"><i class="icon-plus icon-white"></i> Add a new payment</a>
                    </p>
                </div>
            </div>
            <div class="row">
                <div class="span12 notification">

                </div>
            </div>
        </div>

        <div class="container">
            <div id="debts_cont">
                <?php include 'debts.php'; ?>
            </div>
            <div>
                <p><a class="btn" id="view_payments" data-toggle="modal" href="#paymentModal"><i class="icon-list-alt"></i> View all payments</a></p>
            </div>
        </div>

        <div class="container" id="cheevos_cont">
            <?php include 'cheevos.php'; ?>
        </div>



        <!-- Modal -->
        <?php
        $user_arr = User::getAll();

        // Built debt form input
        $debt_form = "";
        foreach ($user_arr as $user_id) {
            $userObj->setId($user_id);
            $userObj->load();
            $debt_form .= "<option class='user' value='$user_id'>" . $userObj->getName() . "</option>\n";
        }
        ?>
        <div id="addModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none; ">
            <form action="form_process.php" method="post" id="modal-form" class="form-inline">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h3>Add Debt</h3>
                </div>
                <div class="modal-body">

                    <div class="input-append date" id="dp3" data-date="<?php echo date('Y-m-d'); ?>" data-date-format="yyyy-mm-dd">
                        <input class="span2 date" size="16" type="text" name="date" readonly='' value="<?php echo date('Y-m-d'); ?>">
                        <span class="add-on"><i class="icon-th"></i></span>
                    </div>
                    <div id="input1" class="clonedInput">
                        <select name='user_id[]'>
                            <option value=''>Select a user</option>
                            <?php echo $debt_form; ?>
                        </select>
                        <div class="input-prepend ">
                            <span class="add-on">&pound;</span>
                            <input class="input-medium drinks" id="drinks_owed1" name="drinks_owed[]" size="16" type="number" placeholder="Drinks" required>
                        </div>
                        <div class="input-prepend ">
                            <span class="add-on">&pound;</span>
                            <input class="input-medium food" id="food_owed1" name="food_owed[]" size="16" type="number" placeholder="Food" required>
                        </div>
                    </div>
                    <button id="btnAdd" class="btn"><i class="icon-plus"></i> Add another attendee</button>
                    <button id="btnDel" class="btn"><i class="icon-trash"></i> Remove attendee</button>
                    <input type="hidden" name="submit_check" value="1"/>

                </div>
                <div class="modal-footer">
                    <button class="btn" data-dismiss="modal">Close</button>
                    <input type="submit" class="btn btn-primary" id="modal-form-submit" value="Submit" />
                </div>
            </form>
        </div>

        <div id="paymentModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none; ">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h3>Payment History</h3>
            </div>
            <div class="modal-body">
                <?php include 'view-payments.php'; ?>
            </div>
            <div class="modal-footer">
                <button class="btn" data-dismiss="modal">Close</button>
                <button class="btn btn-primary" id="modal-form-submit">Submit</button>
            </div>
        </div>

        <script type="text/javascript" src="js/script.js"></script>
    </body>
</html>