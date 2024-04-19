<?php
class Lecture
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAllLectures()
    {
        $query = "SELECT * FROM rozvrh;";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        $lectures = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $lectures;
    }

    public function getLectureById($id)
    {
        $query = "SELECT den, cas_od, cas_do, typ_akcie, nazov_predmetu, miestnost, vyucujuci FROM rozvrh WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $lecture = $stmt->fetch(PDO::FETCH_ASSOC);
        return $lecture ? $lecture : "Lecture not found";
    }

    public function addLecture($data)
    {
        $checkOverlapQuery = "SELECT COUNT(*) as count FROM rozvrh WHERE den = :den AND NOT (:cas_do <= cas_od OR :cas_od >= cas_do);";

        $checkStmt = $this->pdo->prepare($checkOverlapQuery);
        $checkStmt->execute([
            ':den' => $data['den'],
            ':cas_od' => $data['cas_od'],
            ':cas_do' => $data['cas_do'],
        ]);
        $result = $checkStmt->fetch();
        if ($result['count'] > 0) {
            // Time slot overlap found, return an error or false
            return false; // Adjust this according to how you want to handle overlaps
        }
        $query = "INSERT INTO rozvrh (den, cas_od, cas_do, typ_akcie, nazov_predmetu, miestnost, vyucujuci) VALUES (:den, :cas_od, :cas_do, :typ_akcie, :nazov_predmetu, :miestnost, :vyucujuci)";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute($data);
    }

    public function updateLecture($id, $data)
    {
        $data['id'] = $id;
        $checkOverlapQuery = "SELECT COUNT(*) as count FROM rozvrh WHERE den = :den AND NOT (:cas_do <= cas_od OR :cas_od >= cas_do) AND id != :id;";

        $checkStmt = $this->pdo->prepare($checkOverlapQuery);
        $checkStmt->execute([
            ':den' => $data['den'],
            ':cas_od' => $data['cas_od'],
            ':cas_do' => $data['cas_do'],
            ':id' => $id,
        ]);

        $result = $checkStmt->fetch();
        if ($result['count'] > 0) {
            // Time slot overlap found, return an error or false
            return false; // Adjust this according to how you want to handle overlaps
        }

        // If no overlap, proceed with the update
        $updateQuery = "UPDATE rozvrh SET den = :den, cas_od = :cas_od, cas_do = :cas_do, typ_akcie = :typ_akcie, nazov_predmetu = :nazov_predmetu, miestnost = :miestnost, vyucujuci = :vyucujuci WHERE id = :id";
        $stmt = $this->pdo->prepare($updateQuery);
        return $stmt->execute($data);
    }

    public function deleteLecture($id)
    {
        // First, check if the ID exists in the database
        $checkQuery = "SELECT COUNT(*) FROM rozvrh WHERE id = :id";
        $checkStmt = $this->pdo->prepare($checkQuery);
        $checkStmt->bindParam(':id', $id, PDO::PARAM_INT);
        $checkStmt->execute();
        $rowCount = $checkStmt->fetchColumn();

        // If the ID exists, rowCount will be greater than 0
        if ($rowCount > 0) {
            // Proceed with the deletion
            $deleteQuery = "DELETE FROM rozvrh WHERE id = :id";
            $deleteStmt = $this->pdo->prepare($deleteQuery);
            $deleteStmt->bindParam(':id', $id, PDO::PARAM_INT);
            $success = $deleteStmt->execute();

            // Return true to indicate successful deletion
            return $success;
        } else {
            // Return false or any indication that the ID was not found
            return false;
        }
    }
}
