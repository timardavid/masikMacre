<?php
/**
 * Email konfiguráció kezelése
 * Biztonságos környezeti változók használata éles környezetben
 */

class EmailConfig {
    /**
     * Környezeti változókból vagy alapértelmezett értékekből tölti be az email beállításokat
     */
    public static function getConfig() {
        // Betöltjük a config.php-t, hogy hozzáférjünk a $isProduction változóhoz
        if (!isset($GLOBALS['isProduction'])) {
            // Ha nem töltődött be a config.php, próbáljuk megállapítani
            $GLOBALS['isProduction'] = ($_SERVER['HTTP_HOST'] ?? '') !== 'localhost' && 
                                        ($_SERVER['HTTP_HOST'] ?? '') !== '127.0.0.1' &&
                                        !str_contains($_SERVER['HTTP_HOST'] ?? '', '.local');
        }
        $isProduction = $GLOBALS['isProduction'];
        
        // Környezeti változók betöltése (.env fájlból vagy szerver környezeti változókból)
        // FONTOS: Éles környezetben MINDIG használj .env fájlt vagy környezeti változókat!
        // Fejlesztési környezetben itt lehet megadni alapértelmezett értékeket
        $config = [
            'smtp_host' => $_ENV['SMTP_HOST'] ?? $_SERVER['SMTP_HOST'] ?? ($isProduction ? '' : 'smtp.gmail.com'),
            'smtp_port' => (int)($_ENV['SMTP_PORT'] ?? $_SERVER['SMTP_PORT'] ?? ($isProduction ? 587 : 587)),
            'smtp_username' => $_ENV['SMTP_USERNAME'] ?? $_SERVER['SMTP_USERNAME'] ?? ($isProduction ? '' : 'timar.david1974@gmail.com'),
            // FONTOS: Frissítsd ezt az értéket az új Gmail App Password-rel!
            // Ha nincs .env fájl, ide írd be az új App Password-t (szóközök nélkül!)
            'smtp_password' => $_ENV['SMTP_PASSWORD'] ?? $_SERVER['SMTP_PASSWORD'] ?? ($isProduction ? '' : ''),
            'smtp_encryption' => $_ENV['SMTP_ENCRYPTION'] ?? $_SERVER['SMTP_ENCRYPTION'] ?? 'tls', // 'tls' vagy 'ssl'
            'from_email' => $_ENV['EMAIL_FROM'] ?? $_SERVER['EMAIL_FROM'] ?? ($isProduction ? '' : 'timar.david1974@gmail.com'),
            'from_name' => $_ENV['EMAIL_FROM_NAME'] ?? $_SERVER['EMAIL_FROM_NAME'] ?? 'Himesházi Óvoda Website',
            'to_email' => $_ENV['EMAIL_TO'] ?? $_SERVER['EMAIL_TO'] ?? ($isProduction ? '' : 'leibbea81@gmail.com'),
            'reply_to_email' => $_ENV['EMAIL_REPLY_TO'] ?? $_SERVER['EMAIL_REPLY_TO'] ?? '',
        ];
        
        // Éles környezetben ellenőrizzük, hogy minden kötelező beállítás meg van-e adva
        if ($isProduction) {
            $required = ['smtp_host', 'smtp_username', 'smtp_password', 'from_email', 'to_email'];
            $missing = [];
            
            foreach ($required as $key) {
                if (empty($config[$key])) {
                    $missing[] = $key;
                }
            }
            
            if (!empty($missing)) {
                error_log("HIBA: Hiányzó email konfigurációs változók éles környezetben: " . implode(', ', $missing));
                throw new Exception("Email konfigurációs hiba: hiányzó beállítások éles környezetben");
            }
        }
        
        return $config;
    }
    
    /**
     * SMTP beállítások visszaadása PHPMailer-hez
     */
    public static function getSMTPConfig() {
        $config = self::getConfig();
        
        return [
            'host' => $config['smtp_host'],
            'port' => $config['smtp_port'],
            'username' => $config['smtp_username'],
            'password' => $config['smtp_password'],
            'encryption' => $config['smtp_encryption'],
            'from_email' => $config['from_email'],
            'from_name' => $config['from_name'],
            'to_email' => $config['to_email'],
            'reply_to_email' => $config['reply_to_email'],
        ];
    }
}

