<?php
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
?>
<div class="alert">
    The <strong>Total Paid</strong> column displays the total paid <em>for others.</em>
</div>
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