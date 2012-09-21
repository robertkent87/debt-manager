<?php
include_once 'header.php';
include_once 'users.class.php';
include_once 'debts.class.php';
include_once 'payments.class.php';

if (!isset($userObj))
    $userObj = new User();

if (!isset($paymentObj))
    $paymentObj = new Payment();

if (!isset($debtObj))
    $debtObj = new Debt();

$highest_str = "";
$highest_str_chart = "";
$highest_data_str = "";
$user_payments = $paymentObj->getHighest();

foreach ($user_payments as $name => $total) {
    $highest_str .= "<li><strong>$name</strong> has spent a total of <strong>&pound;" . $total . "</strong></li>";
    $highest_data_str .= "['$name',$total],";
}


$freq_str = "";
$freq_data_str = "";
$paid_often = $paymentObj->getFrequentPayers();
foreach ($paid_often as $often_name => $often_times) {
    $freq_str .= "<li><strong>$often_name</strong> has paid a total of <strong>$often_times</strong> times</li>";
    $freq_data_str .= "['$often_name',$often_times],";
}

$user_arr = $userObj->getAll();
$payments_arr = $paymentObj->getAll();
$visits = array();

foreach ($payments_arr as $payments_id) {
    $paymentObj->setId($payments_id);
    $paymentObj->load();

    $debts_arr = $debtObj->getDebtsByPayment($payments_id);

    foreach ($debts_arr as $debt_id) {
        $debtObj->setId($debt_id);
        $debtObj->load();

        $userObj->setId($debtObj->getOwed_by());
        $userObj->load();

        @$visits[$userObj->getName()]++;
    }

    $userObj->setId($paymentObj->getUser_id());
    $userObj->load();
    @$visits[$userObj->getName()]++;
}

arsort($visits);

$visit_str = "";
$visit_data_str = "";
$j = 0;
foreach ($visits as $visitor => $total_visits) {
    if ($j < 3) {
        $visit_str .= "<li><strong>$visitor</strong> has been out a total of <strong>$total_visits</strong> times</li>";
        $visit_data_str .= "['$visitor',$total_visits],";
    }
    $j++;
}
?>

<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<h2 class="underline">Badges</h2>
<div class="row">
    <div class="span4">
        <h3>Big Spenders</h3>
        <p><small><em>Top 3 who have paid the most in total</em></small></p>
        <ol>
            <?php echo $highest_str; ?>    
        </ol>
        <script type="text/javascript">
            google.load("visualization", "1", {packages:["corechart"]});
            google.setOnLoadCallback(drawChart);
            function drawChart() {
                var data = google.visualization.arrayToDataTable([
                    ['Name', 'Spent'],
<?php echo $highest_data_str; ?>
                            ]);

                            var options = {
                                width: 370,
                                legend:{position:'none'},
                                chartArea: {'width': '80%', 'height': '80%'},
                                colors:['#3366CC','#CC3300', '#FF9900']
                            };

                            var chart = new google.visualization.ColumnChart(document.getElementById('chart_div_1'));
                            chart.draw(data, options);
                        }
        </script>
        <div id="chart_div_1" class="chart"></div>
    </div>
    <div class="span4">
        <h3>Frequent Flyer Miles</h3>
        <p><small><em>Top 3 who have paid the most often</em></small></p>
        <ol>
            <?php echo $freq_str; ?>
        </ol>
        <script type="text/javascript">
            google.load("visualization", "1", {packages:["corechart"]});
            google.setOnLoadCallback(drawChart);
            function drawChart() {
                var data2 = google.visualization.arrayToDataTable([
                    ['Name', 'Visits'],
<?php echo $freq_data_str; ?>
                            ]);

                            var options2 = {
                                width: 370,
                                legend:{position:'none'},
                                chartArea: {'width': '80%', 'height': '80%'},
                                colors:['#3366CC','#CC3300', '#FF9900']
                            };

                            var chart2 = new google.visualization.ColumnChart(document.getElementById('chart_div_2'));
                            chart2.draw(data2, options2);
                        }
        </script>
        <div id="chart_div_2" class="chart"></div>
    </div>
    <div class="span4">
        <h3>Repeat Offenders</h3>
        <p><small><em>Top 3 who have gone out the most often</em></small></p>
        <ol>
            <?php echo $visit_str; ?>
        </ol>
        <script type="text/javascript">
            google.load("visualization", "1", {packages:["corechart"]});
            google.setOnLoadCallback(drawChart);
            function drawChart() {
                var data3 = google.visualization.arrayToDataTable([
                    ['Name', 'Visits'],
<?php echo $visit_data_str; ?>
                            ]);

                            var options3 = {
                                width: 370,
                                legend:{position:'none'},
                                chartArea: {'width': '80%', 'height': '80%'},
                                colors:['#3366CC','#CC3300', '#FF9900']
                            };

                            var chart3 = new google.visualization.ColumnChart(document.getElementById('chart_div_3'));
                            chart3.draw(data3, options3);
                        }
        </script>
        <div id="chart_div_3" class="chart"></div>
    </div>
</div>