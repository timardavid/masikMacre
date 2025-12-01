<?php

// CORS fejlécek beállítása - EZ KRITIKUS ÉLES KÖRNYEZETBEN!
header('Access-Control-Allow-Origin: *'); // Fejlesztési fázisban "mindenhol", élesben szigorítani!
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

// OPTIONS kérés kezelése (CORS preflight kéréshez)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Composer autoload fájl betöltése - EZ AZ ELSŐ SOR LEGYEN A SCRIPTBEN!
// Ez betölti az összes Composerrel telepített könyvtárat, beleértve a PHPMailert is.
// Győződj meg róla, hogy ez az elérési út helyes!
// Ha az uzenet_feldolgozo.php a Backend mappában van, akkor kettővel feljebb van a vendor mappa.
require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP; // Opcionális, de ajánlott az SMTP debug-hoz


// Környezeti változók betöltése (.env fájlból)
require_once __DIR__ . '/load_env.php';

// Email konfiguráció betöltése
require_once __DIR__ . '/Config/EmailConfig.php';

// 1. Adatbázis kapcsolódás betöltése
require_once 'db_connection.php'; 

// 2. Ellenőrizze, hogy az űrlapot POST kéréssel küldték-e be
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 3. JSON adatok begyűjtése a kérés törzséből
    $json_data = file_get_contents('php://input');
    $data = json_decode($json_data, true);

    // Ellenőrizzük, hogy a JSON dekódolás sikeres volt-e
    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400); // Bad Request
        echo json_encode([
            "status" => "error",
            "message" => "Hibás JSON formátum a kérésben.",
            "details" => json_last_error_msg()
        ]);
        exit();
    }

    // 4. Adatok kinyerése és tisztítása a dekódolt JSON-ból
    $nev = isset($data['nev']) ? htmlspecialchars(trim($data['nev'])) : NULL;
    $email = isset($data['email']) ? htmlspecialchars(trim($data['email'])) : NULL;
    $targy = isset($data['targy']) ? htmlspecialchars(trim($data['targy'])) : NULL;
    $uzenet = isset($data['uzenet']) ? htmlspecialchars(trim($data['uzenet'])) : NULL;

    // 5. Adatok validálása
    $errors = [];
    if (empty($email)) {
        $errors[] = "Az e-mail cím megadása kötelező.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Érvénytelen e-mail cím formátum.";
    }

    if (!empty($errors)) {
        http_response_code(400); // Bad Request
        echo json_encode([
            "status" => "error",
            "message" => "Adatbeviteli hiba",
            "details" => $errors
        ]);
        exit();
    }

    $response_message = "Üzenetét sikeresen elküldtük és rögzítettük!";
    $response_status = "success";

    // 6. Adatok mentése az adatbázisba
    try {
        $stmt = $pdo->prepare("INSERT INTO uzenetek (nev, email, targy, uzenet, ip_cim) VALUES (:nev, :email, :targy, :uzenet, :ip_cim)");

        $ip_cim = $_SERVER['REMOTE_ADDR'];

        $stmt->bindParam(':nev', $nev);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':targy', $targy);
        $stmt->bindParam(':uzenet', $uzenet);
        $stmt->bindParam(':ip_cim', $ip_cim);

        $stmt->execute();

    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode([
            "status" => "error",
            "message" => "Hiba történt az üzenet mentése során.",
            // "details" => $e->getMessage() // Csak fejlesztéshez!
        ]);
        error_log("Adatbázis hiba az üzenet mentésekor: " . $e->getMessage());
        exit();
    }

    // 7. Email küldés PHPMailerrel
    $mail = new PHPMailer(true); // Az 'true' engedélyezi a kivételeket

    try {
        // Email konfiguráció betöltése (környezeti változókból vagy .env fájlból)
        $emailConfig = EmailConfig::getSMTPConfig();
        
        // SMTP beállítások
        $mail->isSMTP();                                            // SMTP használata
        $mail->Host       = $emailConfig['host'];                   // SMTP szerver címe
        $mail->SMTPAuth   = true;                                   // SMTP hitelesítés engedélyezése
        $mail->Username   = $emailConfig['username'];               // SMTP felhasználónév
        $mail->Password   = $emailConfig['password'];               // SMTP jelszó (App Password)
        
        // Titkosítás beállítása
        if ($emailConfig['encryption'] === 'ssl') {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = 465;
        } else {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $emailConfig['port'] ?: 587;
        }
        
        // SMTP beállítások további finomhangolása
        global $isProduction;
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => $isProduction,  // Éles környezetben ellenőrizzük a tanúsítványt
                'verify_peer_name' => $isProduction,
                'allow_self_signed' => !$isProduction  // Fejlesztésben engedélyezzük
            )
        );
        
        // Timeout beállítások
        $mail->Timeout = 60;
        
        // Debug mód - csak fejlesztési környezetben vagy ha kifejezetten kell
        if (!$isProduction || (isset($_ENV['SMTP_DEBUG']) && $_ENV['SMTP_DEBUG'] === 'true')) {
            $mail->SMTPDebug = SMTP::DEBUG_SERVER;
            $debug_output = '';
            $mail->Debugoutput = function($str, $level) use (&$debug_output) {
                $debug_output .= "[$level] $str\n";
                error_log("PHPMailer Debug [$level]: $str", 3, __DIR__ . '/../logs/php_errors.log');
            };
        }
        
        // Karakterkódolás
        $mail->CharSet = 'UTF-8';
        $mail->Encoding = 'base64';

        // Feladó adatai
        $mail->setFrom($emailConfig['from_email'], $emailConfig['from_name']);
        
        // Reply-to beállítása
        if (!empty($emailConfig['reply_to_email'])) {
            $mail->addReplyTo($emailConfig['reply_to_email']);
        } else {
            $mail->addReplyTo($email, $nev ?: 'Üzenet küldő');
        }

        // Címzett adatai
        $mail->addAddress($emailConfig['to_email']);
       
        // Tartalom
        $mail->isHTML(false); // Egyszerű szöveges email (true ha HTML-t akarsz küldeni)
        $mail->Subject = "Himesházi Óvoda, Családi Bölcsőde és Konyha: " . ($targy ?: 'Üzenet a weboldalról');

        $email_body = "Név: " . ($nev ?: 'Nincs megadva') . "\n";
        $email_body .= "Email: " . $email . "\n";
        $email_body .= "Tárgy: " . ($targy ?: 'Nincs tárgy') . "\n\n";
        $email_body .= "Üzenet:\n" . ($uzenet ?: 'Nincs üzenet') . "\n\n";
        $email_body .= "IP cím: " . $ip_cim;
        $email_body .= "\n\n---\nEz egy automatikus üzenet a Himesházi Óvoda weboldaláról.";

        $mail->Body = $email_body;

        if (!$mail->send()) {
            throw new Exception("Az email küldése sikertelen volt: " . $mail->ErrorInfo);
        }
        
        // Az email sikeresen elküldve
        error_log("Email sikeresen elküldve: " . $email . " -> leibbea81@gmail.com", 3, __DIR__ . '/../logs/php_errors.log');
        
    } catch (Exception $e) {
        // Hiba történt az email küldésekor - részletes naplózás
        $error_message = "Hiba az email küldésekor: " . $e->getMessage();
        $phpmailer_error = '';
        
        if (isset($mail)) {
            $phpmailer_error = $mail->ErrorInfo;
            $error_message .= " | PHPMailer ErrorInfo: " . $phpmailer_error;
        }
        
        // Speciális hibaüzenetek kezelése
        if (strpos($phpmailer_error, 'BadCredentials') !== false || 
            strpos($phpmailer_error, 'Could not authenticate') !== false) {
            $error_message .= "\nPROBLÉMA: A Gmail hitelesítés sikertelen!\n";
            $error_message .= "MEGOLDÁS:\n";
            $error_message .= "1. Ellenőrizd, hogy be van-e kapcsolva a kétfaktoros hitelesítés a Gmail fiókban\n";
            $error_message .= "2. Hozz létre egy új App Password-t a Google fiókban (myaccount.google.com -> Biztonság -> App jelszavak)\n";
            $error_message .= "3. Az új App Password-t (16 karakter, szóközök nélkül) add meg a uzenet_feldolgozo.php fájl 108. sorában\n";
        }
        
        error_log($error_message, 3, __DIR__ . '/../logs/php_errors.log');
        
        $response_message .= " Az email küldése azonban sikertelen volt.";
        // A felhasználónak továbbra is "sikeres" üzenet jelenik meg, de belsőleg tudjuk, hogy az email nem ment el.
    }

    // 8. Sikeres mentés és email küldési kísérlet után küldjön 200 OK választ
    http_response_code(200); // OK
    echo json_encode([
        "status" => $response_status,
        "message" => $response_message
    ]);
    exit();

} else {
    // Ha nem POST kéréssel érkezett ide valaki, küldjön 405 Method Not Allowed választ
    http_response_code(405); // Method Not Allowed
    echo json_encode([
        "status" => "error",
        "message" => "Csak POST kérések engedélyezettek erre a végpontra."
    ]);
    exit();
}

