<?php

class Profile
{
    private $conn;
    private $table_name = 'admins';

    public $email;
    public $img_path;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getByEmail($email)
    {
        $query = 'SELECT img_path FROM ' . $this->table_name . ' WHERE email = ?';
        $stmt = $this->conn->prepare($query);
        $email = htmlspecialchars(strip_tags($email));
        $stmt->execute([$email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return $row;
        }

        return null;
    }

    public function delete()
    {
        $query = "UPDATE admins SET img_path = '../assets/images/img_user.png' WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $this->email = htmlspecialchars(strip_tags($this->email));
        if ($stmt->execute([$this->email])) {
            return true;
        }

        return false;
    }

    public function change()
    {
        $query = 'UPDATE ' . $this->table_name . ' 
                    SET img_path = ?  
                    WHERE email = ?';
        $stmt = $this->conn->prepare($query);
        $this->email = htmlspecialchars(strip_tags($this->email));
        if ($stmt->execute([$this->img_path, $this->email])) {
            return true;
        }

        return false;
    }
}
