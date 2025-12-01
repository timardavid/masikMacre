<?php
// A db_connection.php fájl felelős az adatbázis-kapcsolatért
require_once '../db_connection.php';

class Personnel {
    private $conn;

    // A konstruktor a Database osztálytól kapott kapcsolatot fogadja
    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Összes aktív dolgozó lekérdezése
     * @return PDOStatement
     */
    public function getActive() {
        $query = "CALL GetActivePersonnel()";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    /**
     * Új dolgozó hozzáadása
     * @param string $role
     * @param string $name
     * @param int $order_number
     * @return bool
     */
    public function create($role, $name, $order_number) {
        $query = "CALL AddNewPersonnel(:role, :name, :order_number)";
        $stmt = $this->conn->prepare($query);

        $role = htmlspecialchars(strip_tags($role));
        $name = htmlspecialchars(strip_tags($name));
        $order_number = htmlspecialchars(strip_tags($order_number));

        $stmt->bindParam(":role", $role);
        $stmt->bindParam(":name", $name);
        $stmt->bindParam(":order_number", $order_number, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Dolgozó adatainak frissítése ID alapján
     * @param int $id
     * @param string $role
     * @param string $name
     * @param int $order_number
     * @return bool
     */
    public function update($id, $role, $name, $order_number) {
        $query = "CALL UpdatePersonnel(:id, :role, :name, :order_number)";
        $stmt = $this->conn->prepare($query);

        $id = htmlspecialchars(strip_tags($id));
        $role = htmlspecialchars(strip_tags($role));
        $name = htmlspecialchars(strip_tags($name));
        $order_number = htmlspecialchars(strip_tags($order_number));

        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->bindParam(":role", $role);
        $stmt->bindParam(":name", $name);
        $stmt->bindParam(":order_number", $order_number, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Dolgozó logikai törlése ID alapján
     * @param int $id
     * @return bool
     */
    public function softDelete($id) {
        $query = "CALL SoftDeletePersonnel(:id)";
        $stmt = $this->conn->prepare($query);

        $id = htmlspecialchars(strip_tags($id));
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);

        return $stmt->execute();
    }
}
?>