<?php
require_once __DIR__ . '/../Model/Szemelyzet.php';

class SzemelyzetService {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function getAll(): array {
        $stmt = $this->pdo->query("CALL sp_get_staff()");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return array_map(fn($r) => Szemelyzet::fromArray($r), $rows);
    }

    public function getGrouped(): array {
        $all = $this->getAll();
        $out = [];
        foreach ($all as $s) {
            $key = $s->toArray()['role_name'];
            if (!isset($out[$key])) $out[$key] = [];
            $out[$key][] = [
                'id' => $s->toArray()['id'],
                'name' => $s->toArray()['name']
            ];
        }
        return $out;
    }

    public function add(string $name, string $roleName): void {
        $stmt = $this->pdo->prepare("CALL sp_add_staff(:name, :role)");
        $stmt->execute([':name' => $name, ':role' => $roleName]);
    }
}
