<?php

class StudentAttendance
{
    private $conn;
    private $table_name = 'student_attendance';

    public $id;
    public $student_id;
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

    public function mark()
    {
        $query = "INSERT INTO " . $this->table_name . " SET student_id=:student_id,
        date=:date, status=:status, absence_start_time=:absence_start_time, 
        absence_end_time=:absence_end_time, marked_by=:marked_by";

        $stmt = $this->conn->prepare($query);

        $this->student_id = htmlspecialchars(strip_tags($this->student_id));
        $this->date = htmlspecialchars(strip_tags($this->date));
        $this->status = htmlspecialchars(strip_tags($this->status));
        $this->absence_start_time = htmlspecialchars(strip_tags($this->absence_start_time));
        $this->absence_end_time = htmlspecialchars(strip_tags($this->absence_end_time));
        $this->marked_by = htmlspecialchars(strip_tags($this->marked_by));

        $stmt->bindParam(":student_id", $this->student_id);
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

        $query2 = "SELECT COUNT(*) as total FROM students";
        $stmt2 = $this->conn->prepare($query2);
        $stmt2->execute();
        $row = $stmt2->fetch(PDO::FETCH_ASSOC);

        $presence =  $row['total'];

        return $presence - $absence;
    }

    public function topAbsent()
    {
        $query = "SELECT 
              s.first_name,
              s.last_name,
              c.name AS class_name,
              f.name AS filiere_name,
              COUNT(sa.student_id) AS absentTimes 
          FROM " . $this->table_name . " sa 
          JOIN students s ON s.id = sa.student_id
          JOIN classes c ON c.id = s.class_id
          JOIN filieres f ON f.id = c.filiere_id
          WHERE sa.status = 'absent'
            AND MONTH(sa.date) = MONTH(CURRENT_DATE())
            AND YEAR(sa.date) = YEAR(CURRENT_DATE())
          GROUP BY sa.student_id
          ORDER BY absentTimes DESC
          LIMIT 3";


        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $rows;
    }

    public function absencesByFiliere()
    {
        $query = "
    SELECT 
        f.name AS filiere_name,
        COUNT(sa.student_id) AS absentTimes
    FROM " . $this->table_name . " sa 
    JOIN students s ON s.id = sa.student_id
    JOIN classes c ON c.id = s.class_id
    JOIN filieres f ON f.id = c.filiere_id
    WHERE sa.status = 'absent'
      AND MONTH(sa.date) = MONTH(CURRENT_DATE())
      AND YEAR(sa.date) = YEAR(CURRENT_DATE())
    GROUP BY f.id
    ORDER BY absentTimes DESC
    LIMIT 4
";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $rows;
    }

    public function readAll()
    {
        $query = "
            SELECT 
                sa.*,
                f.name AS filiere_name,
                c.name AS class_name,
                s.first_name, s.last_name,
            FROM " . $this->table_name . " sa 
            JOIN students s ON s.id = sa.student_id
            JOIN classes c ON c.id = s.class_id
            JOIN filieres f ON f.id = c.filiere_id
            WHERE sa.status = 'absent'
            ORDER BY sa.date
            LIMIT 20
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $rows;
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

    public function filterCountAll($where, $params)
    {
        $query = "
        SELECT COUNT(*) FROM " . $this->table_name . " sa
        JOIN students s ON s.id = sa.student_id
        JOIN classes c ON c.id = s.class_id
        JOIN filieres f ON f.id = c.filiere_id
        JOIN poles p ON p.id = f.pole_id
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
                    SELECT 
                        sa.*,
                        f.name AS filiere_name,
                        c.name AS class_name,
                        s.first_name, s.last_name
                    FROM " . $this->table_name . " sa 
                    JOIN students s ON s.id = sa.student_id
                    JOIN classes c ON c.id = s.class_id
                    JOIN filieres f ON f.id = c.filiere_id
                    JOIN poles p ON p.id = f.pole_id
                    $where
                    ORDER BY sa.date DESC
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

    public function readPaginated($start, $limit)
    {
        $query = "SELECT 
                sa.*,
                f.name AS filiere_name,
                c.name AS class_name,
                s.first_name, s.last_name
            FROM " . $this->table_name . " sa
            JOIN students s ON s.id = sa.student_id
            JOIN classes c ON c.id = s.class_id
            JOIN filieres f ON f.id = c.filiere_id
            WHERE sa.status = 'absent'
            ORDER BY sa.date DESC
            LIMIT :start, :limit";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':start', $start, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt;
    }

    public function uploadJust($fileName, $studentId)
    {
        $query = 'UPDATE student_attendance SET justification_document = :file, is_justified = 1 WHERE student_id = :id';
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':file' => $fileName, ':id' => $studentId]);
    }
}
