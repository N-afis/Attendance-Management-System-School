<?php

class Student
{
    private $conn;
    private $table_name = 'students';

    public $id;
    public $first_name;
    public $last_name;
    public $email;
    public $class_id;
    public $class_name;
    public $filiere_name;
    public $pole_name;
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
        last_name=:last_name, email=:email, class_id=:class_id, gender=:gender,
        date_of_birth=:date_of_birth";

        $stmt = $this->conn->prepare($query);

        $this->first_name = htmlspecialchars(strip_tags($this->first_name));
        $this->last_name = htmlspecialchars(strip_tags($this->last_name));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->class_id = htmlspecialchars(strip_tags($this->class_id));
        $this->gender = htmlspecialchars(strip_tags($this->gender));
        $this->date_of_birth = htmlspecialchars(strip_tags($this->date_of_birth));

        $stmt->bindParam(":first_name", $this->first_name);
        $stmt->bindParam(":last_name", $this->last_name);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":class_id", $this->class_id);
        $stmt->bindParam(":gender", $this->gender);
        $stmt->bindParam(":date_of_birth", $this->date_of_birth);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    public function readall()
    {
        $query = "SELECT s.*, c.name as class_name, f.name as filiere_name, p.name as pole_name 
          FROM " . $this->table_name . " s  
          LEFT JOIN classes c ON s.class_id = c.id 
          LEFT JOIN filieres f ON c.filiere_id = f.id 
          LEFT JOIN poles p ON f.pole_id = p.id 
          ORDER BY c.name, s.last_name, s.first_name 
          LIMIT 20";

        $stmt = $this->conn->prepare($query);

        $stmt->execute();

        return $stmt;
    }

    public function readOne()
    {
        $query = "SELECT 
                    s.*, 
                    c.name AS class_name, 
                    c.filiere_id,
                    f.name AS filiere_name, 
                    f.pole_id,
                    p.name AS pole_name
                    FROM students s
                    JOIN classes c ON s.class_id = c.id
                    JOIN filieres f ON c.filiere_id = f.id
                    JOIN poles p ON f.pole_id = p.id
                    WHERE s.id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':id', $this->id);

        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row;
    }

    public function update()
    {
        $query = "UPDATE " . $this->table_name . " SET first_name=:first_name,
        last_name=:last_name, email=:email, class_id=:class_id, gender=:gender,
        date_of_birth=:date_of_birth 
        WHERE id=:id";

        $stmt = $this->conn->prepare($query);

        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->first_name = htmlspecialchars(strip_tags($this->first_name));
        $this->last_name = htmlspecialchars(strip_tags($this->last_name));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->class_id = htmlspecialchars(strip_tags($this->class_id));
        $this->gender = htmlspecialchars(strip_tags($this->gender));
        $this->date_of_birth = htmlspecialchars(strip_tags($this->date_of_birth));

        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":first_name", $this->first_name);
        $stmt->bindParam(":last_name", $this->last_name);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":class_id", $this->class_id);
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

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Execute query
        $stmt->execute();

        // Get row
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row['total'];
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
        $query = "SELECT s.*, c.name as class_name, f.name as filiere_name, p.name as pole_name 
              FROM " . $this->table_name . " s  
              LEFT JOIN classes c ON s.class_id = c.id 
              LEFT JOIN filieres f ON c.filiere_id = f.id 
              LEFT JOIN poles p ON f.pole_id = p.id 
              ORDER BY c.name, s.last_name, s.first_name
              LIMIT :start, :limit";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':start', $start, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt;
    }

    public function filterCountAll($where, $params)
    {
        $query = "
            SELECT COUNT(*) FROM " . $this->table_name . " s
            JOIN classes c ON s.class_id = c.id
            JOIN filieres f ON c.filiere_id = f.id
            JOIN poles p ON f.pole_id = p.id
            $where
        ";

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Execute query
        $stmt->execute($params);

        // Get row
        $totalRecords = $stmt->fetchColumn();

        return $totalRecords;
    }

    public function filter($where, $params, $limit, $offset)
    {
        $query = "
                SELECT s.*, 
                    c.name AS class_name, 
                    f.name AS filiere_name, 
                    p.name AS pole_name
                    FROM " . $this->table_name . " s
                    JOIN classes c ON s.class_id = c.id
                    JOIN filieres f ON c.filiere_id = f.id
                    JOIN poles p ON f.pole_id = p.id
                    $where
                    ORDER BY c.name ASC, s.last_name ASC, s.first_name ASC
                    LIMIT :limit OFFSET :offset
                    ";
        $stmt = $this->conn->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);

        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $rows;
    }
}
