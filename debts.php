<?php
include_once 'header.php';
include_once 'users.class.php';
include_once 'debts.class.php';
include_once 'payments.class.php';

if (!isset($userObj)) {
    $userObj = new User();
    $userObj->setId($user_id);
    $userObj->load();
}

$debtObj = new Debt();
?>
<div class="row">
    <div class="span12">
        <h3 class="underline">Total Debts</h3>
        <table id="debt-table" class="table table-striped">
            <?php 
            
            $total_debts = Debt::getTotalDebts();
            $total_str = "";
            
            if(is_array($total_debts) && count($total_debts) > 0){
                foreach ($total_debts as $total_name => $total_owed) {
                    $total_str .= "<tr><td>$total_name</td><td>&pound;$total_owed</td></tr>";
                }
            }
            
            ?>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Total Owed</th>
                </tr>
            </thead>
            <tbody>
                <?php echo $total_str; ?>
            </tbody>
        </table>
    </div>
    <div class="span6">
        <h3 class="">Owed To You</h3>
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
        <h3 class="">Owed By You</h3>
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