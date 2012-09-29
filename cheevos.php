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

//----------------------------------------------------------------------------------------------------------------------
// Set up 'Most Paid' section

$highest_str = "";
$highest_totals = array();
$highest_people = array();
$user_payments = $paymentObj->getHighest();

foreach ($user_payments as $name => $total) {
    $highest_str .= "<li><strong>$name</strong> has spent a total of <strong>&pound;" . $total . "</strong></li>";
    $highest_totals[] = floatval($total);
    $highest_people[] = $name;
}

//----------------------------------------------------------------------------------------------------------------------
// Set up 'Otfen Paid' section

$freq_str = "";
$freq_people = array();
$freq_times = array();
$paid_often = $paymentObj->getFrequentPayers();
foreach ($paid_often as $often_name => $often_times) {
    $freq_str .= "<li><strong>$often_name</strong> has paid a total of <strong>$often_times</strong> times</li>";
    $freq_people[] = $often_name;
    $freq_times[] = intval($often_times);
}

//----------------------------------------------------------------------------------------------------------------------
// Set up 'Frquent Visits' section

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
$visit_person = array();
$visit_times = array();
$j = 0;
foreach ($visits as $visitor => $total_visits) {
    if ($j < 3) {
        $visit_str .= "<li><strong>$visitor</strong> has been out a total of <strong>$total_visits</strong> times</li>";
        $visit_person[] = $visitor;
        $visit_times[] = intval($total_visits);
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
            $(document).ready(function(){
                $.jqplot.config.enablePlugins = true;
                var totals = <?php echo json_encode($highest_totals); ?>;
                var person = <?php echo json_encode($highest_people); ?>;
        
                plot1 = $.jqplot('chart_div_1', [totals], {
                    // Only animate if we're not using excanvas (not in IE 7 or IE 8)..
                    animate: !$.jqplot.use_excanvas,
                    seriesDefaults:{
                        renderer:$.jqplot.BarRenderer,
                        pointLabels: { show: true }
                    },
                    axes: {
                        xaxis: {
                            renderer: $.jqplot.CategoryAxisRenderer,
                            ticks: person
                        }
                    },
                    highlighter: { show: false }
                });
            });
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
            $(document).ready(function(){
                $.jqplot.config.enablePlugins = true;
                var freq_total = <?php echo json_encode($freq_times); ?>;
                var freq_person = <?php echo json_encode($freq_people); ?>;
        
                plot2 = $.jqplot('chart_div_2', [freq_total], {
                    // Only animate if we're not using excanvas (not in IE 7 or IE 8)..
                    animate: !$.jqplot.use_excanvas,
                    seriesDefaults:{
                        renderer:$.jqplot.BarRenderer,
                        pointLabels: { show: true }
                    },
                    axes: {
                        xaxis: {
                            renderer: $.jqplot.CategoryAxisRenderer,
                            ticks: freq_person
                        }
                    },
                    highlighter: { show: false }
                });
            });
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
            $(document).ready(function(){
                $.jqplot.config.enablePlugins = true;
                var visits_total = <?php echo json_encode($visit_times); ?>;
                var visits_person = <?php echo json_encode($visit_person); ?>;
        
                plot3 = $.jqplot('chart_div_3', [visits_total], {
                    // Only animate if we're not using excanvas (not in IE 7 or IE 8)..
                    animate: !$.jqplot.use_excanvas,
                    seriesDefaults:{
                        renderer:$.jqplot.BarRenderer,
                        pointLabels: { show: true }
                    },
                    axes: {
                        xaxis: {
                            renderer: $.jqplot.CategoryAxisRenderer,
                            ticks: visits_person
                        }
                    },
                    highlighter: { show: false }
                });
            });
        </script>
        <div id="chart_div_3" class="chart"></div>
    </div>
</div>