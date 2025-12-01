<?php

class PersonnelService {
    private $personnelModel;

    // A konstruktor a Personnel Model objektumot fogadja
    public function __construct($personnelModel) {
        $this->personnelModel = $personnelModel;
    }

    // Összes aktív dolgozó lekérdezése
    public function getAll() {
        $stmt = $this->personnelModel->getActive();
        $num = $stmt->rowCount();

        if ($num > 0) {
            $personnel_arr = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $personnel_arr[] = $row;
            }
            return ['status' => 'success', 'data' => $personnel_arr];
        } else {
            return ['status' => 'not_found', 'message' => 'Nem található dolgozó.'];
        }
    }

    // Új dolgozó létrehozása
    public function create($data) {
        if (empty($data->role) || empty($data->name) || !isset($data->order_number)) {
            return ['status' => 'bad_request', 'message' => 'Hiányzó adatok.'];
        }

        if ($this->personnelModel->create($data->role, $data->name, $data->order_number)) {
            return ['status' => 'created', 'message' => 'Dolgozó sikeresen létrehozva.'];
        } else {
            return ['status' => 'server_error', 'message' => 'A dolgozó létrehozása sikertelen.'];
        }
    }

    // Dolgozó adatainak frissítése
    public function update($id, $data) {
        if (empty($data->role) || empty($data->name) || !isset($data->order_number)) {
            return ['status' => 'bad_request', 'message' => 'Hiányzó adatok.'];
        }

        if ($this->personnelModel->update($id, $data->role, $data->name, $data->order_number)) {
            return ['status' => 'success', 'message' => 'Dolgozó sikeresen frissítve.'];
        } else {
            return ['status' => 'not_found', 'message' => 'A dolgozó frissítése sikertelen.'];
        }
    }

    // Dolgozó logikai törlése
    public function delete($id) {
        if ($this->personnelModel->softDelete($id)) {
            return ['status' => 'success', 'message' => 'Dolgozó sikeresen törölve.'];
        } else {
            return ['status' => 'not_found', 'message' => 'A dolgozó törlése sikertelen.'];
        }
    }
}

?>