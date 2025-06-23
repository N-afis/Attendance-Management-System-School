<?php

class Teacher
{
    private $conn;
    private $table_name = 'teachers';

    public $id;
    public $first_name;
    public $last_name;
    public $email;
    public $gender;
    public $date_of_birth;
    public $created_at;
    public $updated_at;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function create()
    {
        $query = "INSERT INTO " . $this->table_name . " SET first_name=:first_name,
        last_name=:last_name, email=:email, gender=:gender,
        date_of_birth=:date_of_birth";

        $stmt = $this->conn->prepare($query);

        $this->first_name = htmlspecialchars(strip_tags($this->first_name));
        $this->last_name = htmlspecialchars(strip_tags($this->last_name));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->gender = htmlspecialchars(strip_tags($this->gender));
        $this->date_of_birth = htmlspecialchars(strip_tags($this->date_of_birth));

        $stmt->bindParam(":first_name", $this->first_name);
        $stmt->bindParam(":last_name", $this->last_name);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":gender", $this->gender);
        $stmt->bindParam(":date_of_birth", $this->date_of_birth);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    public function readall()
    {
        $query = "SELECT*FROM " . $this->table_name . " 
        ORDER BY last_name, first_name";

        $stmt = $this->conn->prepare($query);

        $stmt->execute();

        return $stmt;
    }

    public function readOne()
    {
        $query = "SELECT*FROM " . $this->table_name . " WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':id', $this->id);

        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row;
    }

    public function update()
    {
        $query = "UPDATE " . $this->table_name . " SET first_name=:first_name,
        last_name=:last_name, email=:email, gender=:gender,
        date_of_birth=:date_of_birth 
        WHERE id=:id";

        $stmt = $this->conn->prepare($query);

        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->first_name = htmlspecialchars(strip_tags($this->first_name));
        $this->last_name = htmlspecialchars(strip_tags($this->last_name));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->gender = htmlspecialchars(strip_tags($this->gender));
        $this->date_of_birth = htmlspecialchars(strip_tags($this->date_of_birth));

        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":first_name", $this->first_name);
        $stmt->bindParam(":last_name", $this->last_name);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":gender", $this->gender);
        $stmt->bindParam(":date_of_birth", $this->date_of_birth);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    public function delete()
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $this->id = intval($this->id);

        $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function countAll()
    {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name;

        $stmt = $this->conn->prepare($query);

        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row['total'];
    }

    public function searchByKeyword($keyword, $limit, $offset)
    {
        $sql = "SELECT * FROM " . $this->table_name . " 
            WHERE first_name LIKE :kw 
               OR last_name LIKE :kw2 
               OR email LIKE :kw3 
               ORDER BY last_name, first_name
            LIMIT :lim OFFSET :off";

        $stmt = $this->conn->prepare($sql);

        $kw = "%{$keyword}%";

        // Correct binding
        $stmt->bindValue(':kw', $kw, PDO::PARAM_STR);
        $stmt->bindValue(':kw2', $kw, PDO::PARAM_STR);
        $stmt->bindValue(':kw3', $kw, PDO::PARAM_STR);
        $stmt->bindValue(':lim', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':off', (int)$offset, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt;
    }



    public function countByKeyword($keyword)
    {
        $sql = "SELECT COUNT(*) as total FROM " . $this->table_name . " 
            WHERE first_name LIKE :kw 
               OR last_name LIKE :kw2 
               OR email LIKE :kw3";

        $stmt = $this->conn->prepare($sql);
        $kw = "%{$keyword}%";
        $stmt->bindValue(':kw', $kw, PDO::PARAM_STR);
        $stmt->bindValue(':kw2', $kw, PDO::PARAM_STR);
        $stmt->bindValue(':kw3', $kw, PDO::PARAM_STR);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'] ?? 0;
    }



    public function existsByEmail()
    {
        $query = "SELECT id FROM " . $this->table_name . " WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $this->email);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public function readPaginated($start, $limit)
    {
        $query = "SELECT*FROM " . $this->table_name . "  
              ORDER BY last_name, first_name
              LIMIT :start, :limit";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':start', $start, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt;
    }
}
