<?php
include 'config.inc.php';
include 'users.class.php';
include 'debts.class.php';
include 'payments.class.php';

if (!isset($_COOKIE['user_id'])) {
    header("Location: login.php");
    exit();
}

$is_ajax = false;

if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    $is_ajax = true;
}

$user_id = $_COOKIE['user_id'];
$total = 0;

// Connect to database
$con = mysql_connect(DB_HOST, DB_USER, DB_PW);
if (!$con) {
    die('Could not connect: ' . mysql_error());
}

mysql_select_db(DB_NAME, $con);

// if the form has been submitted
if (isset($_POST['submit_check'])):
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

    header("Location: dashboard.php");
    exit();
else:
    // Get list of users for form
    $user_arr = User::getAll();

    // Built debt form input
    $userObj = new User;

    $debt_form = "<select name='user_id[]'>\n";
    $debt_form .= "<option>Select a user</option>\n";

    foreach ($user_arr as $user_id) {
        $userObj->setId($user_id);
        $userObj->load();

        $debt_form .= "<option value='$user_id'>" . $userObj->getName() . "</option>\n";
    }

    $debt_form .= "</select>\n";
    $debt_form .= "<div class=\"input-prepend \">
                        <span class=\"add-on\">&pound;</span>
                        <input class=\"input-medium drinks\" id=\"drinks_owed1\" name=\"drinks_owed[]\" size=\"16\" type=\"number\" placeholder=\"Drinks\">
                  </div>\n";
    $debt_form .= "<div class=\"input-prepend \">
                        <span class=\"add-on\">&pound;</span>
                        <input class=\"input-medium food\" id=\"food_owed1\" name=\"food_owed[]\" size=\"16\" type=\"number\" placeholder=\"Food\">
                  </div>\n";

    if (!$is_ajax) {
        include 'header.php';
        echo "<h3>Add Debt</h3>";
    }
    ?>

    <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post" id="modal-form" class="form-inline">
        <div class="input-append date" id="dp3" data-date="<?php echo date('Y-m-d'); ?>" data-date-format="yyyy-mm-dd">
            <input class="span2" size="16" type="text" name="date" readonly='' value="<?php echo date('Y-m-d'); ?>">
            <span class="add-on"><i class="icon-th"></i></span>
        </div>
        <div id="input1" class="clonedInput">
            <?php echo $debt_form; ?>
        </div>
        <button id="btnAdd" class="btn"><i class="icon-plus"></i> Add another attendee</button>
        <button id="btnDel" class="btn"><i class="icon-trash"></i> Remove attendee</button>
        <input type="hidden" name="submit_check" value="1"/>
        <?php
        if (!$is_ajax) {
            echo "<input type=\"submit\" value=\"Submit\" class=\"btn btn-primary\" />";
        }
        ?>
    </form>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#dp3').datepicker();
            
            $('#btnDel').attr('disabled','disabled');
                    
            $('#btnAdd').click(function(e) {
                e.preventDefault();
                var num     = $('.clonedInput').length; 
                var newNum  = new Number(num + 1);      
                                                    
                var newElem = $('#input' + num).clone().attr('id', 'input' + newNum);
                                                    
                newElem.find('input :first').attr('id', 'user_id' + newNum);
                newElem.find('.drinks').attr('id', 'drinks_owed' + newNum).val('');
                newElem.find('.food').attr('id', 'food_owed' + newNum).val('');
                                     
                // insert the new element after the last "duplicatable" input field
                $('#input' + num).after(newElem);
                                                    
                $('#btnDel').removeAttr('disabled');
                                                    
                if (newNum == 6)
                    $('#btnAdd').attr('disabled','disabled');
            });
                                     
            $('#btnDel').click(function(e) {
                e.preventDefault();
                var num = $('.clonedInput').length; // how many "duplicatable" input fields we currently have
                $('#input' + num).remove();     // remove the last element
                                     
                                                    
                $('#btnAdd').removeAttr('disabled');
                                     
                                                    
                if (num-1 == 1)
                    $('#btnDel').attr('disabled','disabled');
            });
        });
    </script>

    <?php
    mysql_close($con);

    if (!$is_ajax) {
        include 'footer.php';
    }
endif;
?>
