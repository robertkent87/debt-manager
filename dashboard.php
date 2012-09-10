<?php
include 'config.inc.php';
include 'users.class.php';
include 'debts.class.php';
include 'payments.class.php';

if (!isset($_COOKIE['user_id'])) {
    header("Location: login.php");
    exit();
}

// Connect to database
$con = mysql_connect(DB_HOST, DB_USER, DB_PW);
if (!$con) {
    die('Could not connect: ' . mysql_error());
}

mysql_select_db(DB_NAME, $con);

$user_id = $_COOKIE['user_id'];

$debtObj = new Debt();

$userObj = new User();
$userObj->setId($user_id);
$userObj->load();



include 'header.php';
?>
<div id="" class="header container">
    <div class="row">
        <div class="span6">
            <h1>Weather Debt Manager</h1>
            <div id="user">
                <?php echo $user_str; ?>
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
                <a class="btn btn-large btn-primary" id="add_debt" data-toggle="modal" href="#myModal"><i class="icon-plus icon-white"></i> Add a new payment</a>
            </p>
        </div>
    </div>

</div>

<div class="container">
    <div class="row">
        <div class="span6">
            <h3 class="underline">Owed To You</h3>
            <?php
            $debt_arr = array();
            $db_debt_arr = $debtObj->getOwedTo($user_id);

            foreach ($db_debt_arr as $db_debt_id) {
                $debtObj->setId($db_debt_id);
                $debtObj->load();
                $debt_ower = $debtObj->getOwed_by();

                @$debt_arr[$debt_ower] += $debtObj->getAmount();
            }
            
            foreach ($debt_arr as $debt_user_id => $amount) {
                $userObj->setId($debt_user_id);
                $userObj->load();

                $owee = $userObj->getName();

                echo "<p><strong>$owee</strong> owes you &pound;$amount</p>";
            }
            ?>
        </div>

        <div class="span6">
            <h3 class="underline">Owed By You</h3>
            <?php
            $owed_arr = array();
            $db_owed_arr = $debtObj->getOwedBy($user_id);

            foreach ($db_owed_arr as $owed_id) {
                $debtObj->setId($owed_id);
                $debtObj->load();
                $debt_owee = $debtObj->getOwed_to();

                @$owed_arr[$debt_owee] += $debtObj->getAmount();
            }

            foreach ($owed_arr as $owed_user_id => $owed_amount) {
                $userObj->setId($owed_user_id);
                $userObj->load();

                $ower = $userObj->getName();

                echo "<p>You owe <strong>$ower</strong> &pound;$owed_amount</p>";
            }
            ?>
        </div>
    </div>
    <div>
        <p><a class="btn" id="view_payments" data-toggle="modal" href="#myModal"><i class="icon-list-alt"></i> View all payments</a></p>
    </div>
</div>

<div class="container">
    <?php include 'cheevos.php'; ?>
</div>

</div>

<!-- Modal -->
<div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none; ">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3>Add Debt</h3>
    </div>
    <div class="modal-body">
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal">Close</button>
        <button class="btn btn-primary" id="modal-form-submit">Submit</button>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        $('#add_debt').click(function(){
            if ($(window).width() > 480){
                $.post('add_debt.php', function(data) {
                    $('.modal-body').html(data);
                    $('.modal-header h3').html("Add Debt");
                    $('.modal-footer .btn-primary').show();
                });
            } else {
                window.location = 'add_debt.php';
            }
        }); 
        
        $('#view_payments').click(function(){
            if ($(window).width() > 480){
                $.post('view-payments.php', function(data) {
                    $('.modal-body').html(data);
                    $('.modal-header h3').html("Payment History");
                    $('.modal-footer .btn-primary').hide();
                });
            } else {
                window.location = 'view-payments.php';
            }
        }); 
        
        $('#modal-form-submit').on('click', function(e){
            // We don't want this to act as a link so cancel the link action
            e.preventDefault();

            // Find form and submit it
            $('#modal-form').submit();
            $('#myModal').modal('hide');
            location.reload();
        });

    });
</script>
</div>
<?php
include 'footer.php';

mysql_close($con);
?>