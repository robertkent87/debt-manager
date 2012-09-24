<?php

class User {

    private $id;
    private $name;
    private $payment_order;
    private $image;

    public function load() {
        $sql = "SELECT id, name, payment_order, image
                FROM users
                WHERE id = " . mysql_real_escape_string($this->getId());

        $result = mysql_query($sql);

        if (mysql_errno()) {
            echo mysql_error();
            return false;
        } else {
            $num = mysql_num_rows($result);
            if ($num != 0) {
                list($this->id, $this->name, $this->payment_order, $this->image) = mysql_fetch_row($result);
            }
            return true;
        }
    }

    public function create() {
        $sql = "INSERT INTO users (name, payment_order)
                    VALUES (
                        '" . mysql_real_escape_string($this->getName()) . "',
                        '" . mysql_real_escape_string($this->getPayment_order()) . "'
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
        $sql = "UPDATE users SET
                name = '" . mysql_real_escape_string($this->getName()) . "',
                payment_order = '" . mysql_real_escape_string($this->getPayment_order()) . "',
                image = '" . mysql_real_escape_string($this->getImage()) . "'
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
        $sql = "SELECT id FROM users
                ORDER BY payment_order ASC";

        $result = mysql_query($sql);
        if (mysql_errno()) {
            echo mysql_error();
        } 
        while ($result && $ret = mysql_fetch_row($result)) {
            $arr[] = $ret[0];
        }
        return $arr;
    }
    
    public static function getAllOthers($user_id) {
        $arr = array();
        $sql = "SELECT id FROM users
                WHERE id != $user_id
                ORDER BY payment_order ASC";

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

    public function getName() {
        return $this->name;
    }
    
    public function getImage() {
        return $this->image;
    }

    public function setImage($image) {
        $this->image = $image;
    }

    
    public function setName($name) {
        $this->name = $name;
    }

    public function getPayment_order() {
        return $this->payment_order;
    }

    public function setPayment_order($payment_order) {
        $this->payment_order = $payment_order;
    }
}

?>
