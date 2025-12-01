<?php
// A CORS (Cross-Origin Resource Sharing) fejlécek beállítása
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Függőségek betöltése
require_once '../db_connection.php'; // Itt a kapcsolatot tartalmazó fájl
require_once '../Model/Personnel.php';
require_once '../Service/personnel_service.php';

// Adatbázis-kapcsolat inicializálása a $pdo változón keresztül
$db = $pdo;

// Model és Service objektumok létrehozása
$personnelModel = new Personnel($db);
$personnelService = new PersonnelService($personnelModel);

// HTTP kérés metódusának lekérdezése
$request_method = $_SERVER["REQUEST_METHOD"];

switch($request_method) {
    case 'GET':
        $response = $personnelService->getAll();
        http_response_code(200);
        echo json_encode($response['data']);
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        $response = $personnelService->create($data);
        if ($response['status'] === 'created') {
            http_response_code(201);
        } elseif ($response['status'] === 'bad_request') {
            http_response_code(400);
        } else {
            http_response_code(503);
        }
        echo json_encode(['message' => $response['message']]);
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"));
        $id = isset($_GET['id']) ? $_GET['id'] : die();
        $response = $personnelService->update($id, $data);
        if ($response['status'] === 'success') {
            http_response_code(200);
        } elseif ($response['status'] === 'not_found') {
            http_response_code(404);
        } elseif ($response['status'] === 'bad_request') {
            http_response_code(400);
        } else {
            http_response_code(503);
        }
        echo json_encode(['message' => $response['message']]);
        break;

    case 'DELETE':
        $id = isset($_GET['id']) ? $_GET['id'] : die();
        $response = $personnelService->delete($id);
        if ($response['status'] === 'success') {
            http_response_code(200);
        } elseif ($response['status'] === 'not_found') {
            http_response_code(404);
        } else {
            http_response_code(503);
        }
        echo json_encode(['message' => $response['message']]);
        break;

    default:
        http_response_code(405);
        echo json_encode(array("message" => "Method not allowed."));
        break;
}
?>