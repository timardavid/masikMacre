<?php
require_once __DIR__ . '/../db_connection.php';
require_once __DIR__ . '/../Model/StaffMember.php';

class StaffMemberService {
    private $pdo;

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }
    

    public function getAll($onlyActive = true) {
        $stmt = $this->pdo->prepare("CALL sp_get_staff_members(:onlyActive)");
        $stmt->bindValue(':onlyActive', $onlyActive ? 1 : 0, PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        $staffMembers = [];
        foreach ($rows as $row) {
            $staffMembers[] = new StaffMember($row);
        }
        return $staffMembers;
    }
}
