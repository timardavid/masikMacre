<?php
require_once __DIR__ . '/../Model/Enrollment.php';

class EnrollmentService {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Lekéri a beiratkozási adatokat adott évhez
     */
    public function getEnrollment(string $school_year): ?Enrollment {
        $stmt = $this->pdo->prepare("CALL sp_get_enrollment(:year)");
        $stmt->bindParam(':year', $school_year, PDO::PARAM_STR);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor(); // fontos MySQL-nél

        if ($row) {
            return new Enrollment($row);
        }
        return null;
    }

    /**
     * Mentés / frissítés tárolt eljárással
     */
    public function saveEnrollment(array $data): bool {
        $sql = "CALL sp_set_enrollment(
            :school_year, :period_text, :start_date, :end_date, :status,
            :notice, :documents, :mandatory_condition, :optional_condition,
            :signature_place_date, :signature_name, :signature_title
        )";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':school_year'          => $data['school_year'],
            ':period_text'          => $data['period_text'],
            ':start_date'           => $data['start_date'] ?? null,
            ':end_date'             => $data['end_date'] ?? null,
            ':status'               => $data['status'] ?? 'hamarosan',
            ':notice'               => $data['notice'] ?? null,
            ':documents'            => $data['documents'] ?? null,
            ':mandatory_condition'  => $data['mandatory_condition'] ?? null,
            ':optional_condition'   => $data['optional_condition'] ?? null,
            ':signature_place_date' => $data['signature_place_date'] ?? null,
            ':signature_name'       => $data['signature_name'] ?? null,
            ':signature_title'      => $data['signature_title'] ?? null,
        ]);
    }
}
