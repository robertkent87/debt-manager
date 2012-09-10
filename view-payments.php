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

// Connect to database
$con = mysql_connect(DB_HOST, DB_USER, DB_PW);
if (!$con) {
    die('Could not connect: ' . mysql_error());
}

mysql_select_db(DB_NAME, $con);

$userObj = new User();
$debtObj = new Debt();

$paymentObj = new Payment();
$payments_arr = $paymentObj->getAll();

$payments_str = "";

foreach ($payments_arr as $payments_id) {
    $paymentObj->setId($payments_id);
    $paymentObj->load();

    $attendees = array();
    $debts_arr = $debtObj->getDebtsByPayment($payments_id);

    foreach ($debts_arr as $debt_id) {
        $debtObj->setId($debt_id);
        $debtObj->load();

        $userObj->setId($debtObj->getOwed_by());
        $userObj->load();

        $attendees[] = $userObj->getName();
    }


    $userObj->setId($paymentObj->getUser_id());
    $userObj->load();
    $attendees[] = $userObj->getName();

    $payments_str .= "<tr>";
    $payments_str .= "<td>" . date('D, d M Y', strtotime($paymentObj->getDate())) . "</td>";
    $payments_str .= "<td>" . implode(", ", $attendees) . "</td>";
    $payments_str .= "<td>" . $userObj->getName() . "</td>";
    $payments_str .= "<td>&pound;" . $paymentObj->getTotal() . "</td>";
    $payments_str .= "</tr>";
}

if (!$is_ajax) {
    include 'header.php';
    echo "<h3>Payment History</h3>";
}
?>

<table id="payment-table" class="table table-striped">
    <thead>
        <tr>
            <th>Date</th>
            <th>Attendees</th>
            <th>Paid by</th>
            <th>Total Paid</th>
        </tr>
    </thead>
    <tbody>
        <?php echo $payments_str; ?>
    </tbody>
</table>

<script type="text/javascript">
    $(document).ready(function() { 
        $("#payment-table").tablesorter(); 
    }); 
</script>

<?php
mysql_close($con);
if (!$is_ajax) {
    include 'footer.php';
}
?>