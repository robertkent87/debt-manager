<?php

class Payment {

    private $id;
    private $total;
    private $date;
    private $user_id;

    public function load() {
        $sql = "SELECT id, total, date, user_id
                FROM payments
                WHERE id = " . mysql_real_escape_string($this->getId());

        $result = mysql_query($sql);

        if (mysql_errno()) {
            return false;
        } else {
            $num = mysql_num_rows($result);
            if ($num != 0) {
                list($this->id, $this->total, $this->date, $this->user_id) = mysql_fetch_row($result);
            }
            return true;
        }
    }

    public function create() {
        $sql = "INSERT INTO payments (total, date, user_id)
                    VALUES (
                        '" . mysql_real_escape_string($this->getTotal()) . "',
                        NOW(),
                        '" . mysql_real_escape_string($this->getUser_id()) . "'
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
        $sql = "UPDATE payments SET
                total = '" . mysql_real_escape_string($this->getTotal()) . "',
                date = '" . mysql_real_escape_string($this->getDate()) . "',
                user_id = '" . mysql_real_escape_string($this->getUser_id()) . "'
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

    public static function getAll() {
        $arr = array();
        $sql = "SELECT id FROM payments
                ORDER BY date ASC";

        $result = mysql_query($sql);
        if (mysql_errno()) {
            echo mysql_error();
        }
        while ($result && $ret = mysql_fetch_row($result)) {
            $arr[] = $ret[0];
        }
        return $arr;
    }

    public static function getAllByUser($user_id) {
        $arr = array();
        $sql = "SELECT id FROM payments
                WHERE user_id = '$user_id'";

        $result = mysql_query($sql);
        if (mysql_errno()) {
            echo mysql_error();
        }
        while ($result && $ret = mysql_fetch_row($result)) {
            $arr[] = $ret[0];
        }
        return $arr;
    }

    public static function getHighest() {
        $arr = array();

        $sql = "SELECT u.name, SUM(p.total)
                FROM payments p
                JOIN users u
                ON p.user_id = u.id
                GROUP BY u.name
                ORDER BY SUM(p.total) DESC
                LIMIT 3";

        $result = mysql_query($sql);
        if (mysql_errno()) {
            echo mysql_error();
        }
        while ($result && $ret = mysql_fetch_row($result)) {
            $arr[$ret[0]] = $ret[1];
        }

        return $arr;
    }

    public static function getFrequentPayers() {
        $arr = array();

        $sql = "SELECT u.name, COUNT(p.user_id)
                FROM payments p
                JOIN users u
                ON p.user_id = u.id
                GROUP BY u.name
                ORDER BY COUNT(p.user_id) DESC
                LIMIT 3";

        $result = mysql_query($sql);
        if (mysql_errno()) {
            echo mysql_error();
        }
        while ($result && $ret = mysql_fetch_row($result)) {
            $arr[$ret[0]] = $ret[1];
        }

        return $arr;
    }

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getTotal() {
        return $this->total;
    }

    public function setTotal($total) {
        $this->total = $total;
    }

    public function getDate() {
        return $this->date;
    }

    public function setDate($date) {
        $this->date = $date;
    }

    public function getUser_id() {
        return $this->user_id;
    }

    public function setUser_id($user_id) {
        $this->user_id = $user_id;
    }

}

?>
