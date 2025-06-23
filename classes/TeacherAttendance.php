<?php

class TeacherAttendance
{
    private $conn;
    private $table_name = 'teacher_attendance';

    public $id;
    public $teacher_id;
    public $date;
    public $status;
    public $is_justified;
    public $justification_document;
    public $absence_start_time;
    public $absence_end_time;
    public $marked_by;
    public $created_at;
    public $updated_at;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function readAll()
    {
        $query = "
            SELECT 
                ta.*,
                t.first_name, t.last_name
            FROM " . $this->table_name . " ta 
            JOIN teachers t ON t.id = ta.teacher_id
            WHERE ta.status = 'absent'
            ORDER BY ta.date DESC
            LIMIT 20
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $rows;
    }

    public function mark()
    {
        $query = "INSERT INTO " . $this->table_name . " SET teacher_id=:teacher_id,
        date=:date, status=:status, absence_start_time=:absence_start_time, 
        absence_end_time=:absence_end_time, marked_by=:marked_by";

        $stmt = $this->conn->prepare($query);

        $this->teacher_id = htmlspecialchars(strip_tags($this->teacher_id));
        $this->date = htmlspecialchars(strip_tags($this->date));
        $this->status = htmlspecialchars(strip_tags($this->status));
        $this->absence_start_time = htmlspecialchars(strip_tags($this->absence_start_time));
        $this->absence_end_time = htmlspecialchars(strip_tags($this->absence_end_time));
        $this->marked_by = htmlspecialchars(strip_tags($this->marked_by));

        $stmt->bindParam(":teacher_id", $this->teacher_id);
        $stmt->bindParam(":date", $this->date);
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":absence_start_time", $this->absence_start_time);
        $stmt->bindParam(":absence_end_time", $this->absence_end_time);
        $stmt->bindParam(":marked_by", $this->marked_by);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    public function countAbsenceToday()
    {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " WHERE date = CURRENT_DATE";

        $stmt = $this->conn->prepare($query);

        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row['total'];
    }

    public function countPresenceToday()
    {
        $query1 = "SELECT COUNT(*) as total FROM " . $this->table_name . " WHERE date = CURRENT_DATE";
        $stmt1 = $this->conn->prepare($query1);
        $stmt1->execute();
        $row = $stmt1->fetch(PDO::FETCH_ASSOC);
        $absence = $row['total'];

        $query2 = "SELECT COUNT(*) as total FROM teachers";
        $stmt2 = $this->conn->prepare($query2);
        $stmt2->execute();
        $row = $stmt2->fetch(PDO::FETCH_ASSOC);

        $presence =  $row['total'];

        return $presence - $absence;
    }

    public function uploadJust($fileName, $teacherId)
    {
        $query = 'UPDATE teacher_attendance SET justification_document = :file, is_justified = 1 WHERE teacher_id = :id';
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':file' => $fileName, ':id' => $teacherId]);
    }

    public function filter($where, $params)
    {
        $query = "
                    SELECT 
                        ta.*,
                        t.first_name, t.last_name
                    FROM " . $this->table_name . " ta 
                    JOIN teachers t ON t.id = ta.teacher_id
                    $where
                    ORDER BY ta.date DESC
                    ";
        $stmt = $this->conn->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $rows;
    }
}
