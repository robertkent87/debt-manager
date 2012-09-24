<?php

class Debt {

    private $id;
    private $payment_id;
    private $amount;
    private $owed_by;
    private $owed_to;
    private $archived;

    public function load() {
        $sql = "SELECT id, payment_id, amount, owed_by, owed_to, archived
                FROM debts
                WHERE id = " . mysql_real_escape_string($this->getId());

        $result = mysql_query($sql);

        if (mysql_errno()) {
            return false;
        } else {
            $num = mysql_num_rows($result);
            if ($num != 0) {
                list($this->id, $this->payment_id, $this->amount, $this->owed_by, $this->owed_to, $this->archived) = mysql_fetch_row($result);
            }
            return true;
        }
    }

    public function create() {
        $sql = "INSERT INTO debts (payment_id, amount, owed_by, owed_to, archived)
                    VALUES (
                        '" . mysql_real_escape_string($this->getPayment_id()) . "',
                        '" . mysql_real_escape_string($this->getAmount()) . "',
                        '" . mysql_real_escape_string($this->getOwed_by()) . "',
                        '" . mysql_real_escape_string($this->getOwed_to()) . "',
                        '" . mysql_real_escape_string($this->getArchived()) . "'
                    )";

        $result = mysql_query($sql);
        if (mysql_errno()) {
            return false;
        } else {
            $insertid = mysql_insert_id();
            $this->setId($insertid);

            return $insertid;
        }
    }

    public function update() {
        $sql = "UPDATE debts SET
                payment_id = '" . mysql_real_escape_string($this->getPayment_id()) . "',
                amount = '" . mysql_real_escape_string($this->getAmount()) . "',
                owed_by = '" . mysql_real_escape_string($this->getOwed_by()) . "',
                owed_to = '" . mysql_real_escape_string($this->getOwed_to()) . "',
                archived = '" . mysql_real_escape_string($this->getArchived()) . "'
                WHERE id = " . mysql_real_escape_string($this->getId());

        $result = mysql_query($sql);
        //exit($sql);
        if (mysql_errno()) {
            LogSql::log(date("Y-m-d H:i:s") . " " . mysql_error() . ", " . __FILE__ . " [" . __LINE__ . "]");
            return false;
        } else {
            return true;
        }
    }

    public static function getTotalDebts() {
        $arr = array();
        $sql = "SELECT u.name, SUM(d.amount) 
                FROM debts d
                LEFT JOIN users u
                ON d.owed_by = u.id
                GROUP BY d.owed_by
                ORDER BY SUM(d.amount) DESC";
        $result = mysql_query($sql);
        if (mysql_errno()) {
            echo mysql_error();
        }
        while ($result && $ret = mysql_fetch_row($result)) {
            $arr[$ret[0]] = $ret[1];
        }
        return $arr;
    }

    public function getOwedTo($user_id) {
        $arr = array();
        $sql = "SELECT id FROM debts
                WHERE owed_to = " . mysql_escape_string($user_id) . "
                AND owed_by != " . mysql_escape_string($user_id) . "
                ORDER BY amount DESC";

        $result = mysql_query($sql);
        if (mysql_errno()) {
            echo mysql_error();
        }
        while ($result && $ret = mysql_fetch_row($result)) {
            $arr[] = $ret[0];
        }
        return $arr;
    }

    public function getOwedBy($user_id) {
        $arr = array();
        $sql = "SELECT id FROM debts
                WHERE owed_by = " . mysql_escape_string($user_id) . "
                AND owed_to != " . mysql_escape_string($user_id) . "
                ORDER BY amount DESC";

        $result = mysql_query($sql);
        if (mysql_errno()) {
            echo mysql_error();
        }
        while ($result && $ret = mysql_fetch_row($result)) {
            $arr[] = $ret[0];
        }
        return $arr;
    }

    public function getDebtsByPayment($payment_id) {
        $arr = array();
        $sql = "SELECT id FROM debts
                WHERE payment_id = " . mysql_escape_string($payment_id);

        $result = mysql_query($sql);
        if (mysql_errno()) {
            echo mysql_error();
        }
        while ($result && $ret = mysql_fetch_row($result)) {
            $arr[] = $ret[0];
        }
        return $arr;
    }

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getPayment_id() {
        return $this->payment_id;
    }

    public function setPayment_id($payment_id) {
        $this->payment_id = $payment_id;
    }

    public function getAmount() {
        return number_format($this->amount, 2);
    }

    public function setAmount($amount) {
        $this->amount = $amount;
    }

    public function getOwed_by() {
        return $this->owed_by;
    }

    public function setOwed_by($owed_by) {
        $this->owed_by = $owed_by;
    }

    public function getOwed_to() {
        return $this->owed_to;
    }

    public function setOwed_to($owed_to) {
        $this->owed_to = $owed_to;
    }

    public function getArchived() {
        return $this->archived;
    }

    public function setArchived($archived) {
        $this->archived = $archived;
    }

}

?>
