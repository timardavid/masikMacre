<?php
require_once __DIR__ . '/../Model/PageModel.php';
require_once __DIR__ . '/../Model/ProductModel.php';
require_once __DIR__ . '/../Model/ServiceModel.php';
require_once __DIR__ . '/../Model/ContactModel.php';
require_once __DIR__ . '/../Config/EmailConfig.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class HomeController {
    private $pageModel;
    private $productModel;
    private $serviceModel;
    private $contactModel;
    
    public function __construct() {
        $this->pageModel = new PageModel();
        $this->productModel = new ProductModel();
        $this->serviceModel = new ServiceModel();
        $this->contactModel = new ContactModel();
    }
    
    public function index() {
        $data = [
            'page' => $this->pageModel->getHomePage(),
            'services' => $this->serviceModel->getAllServices(),
            'products' => $this->productModel->getAllProducts(),
            'contact' => $this->contactModel->getContactInfo()
        ];
        
        return $data;
    }
    
    public function getPage($slug) {
        $page = $this->pageModel->getPageBySlug($slug);
        if (!$page) {
            return null;
        }
        
        return [
            'page' => $page,
            'contact' => $this->contactModel->getContactInfo()
        ];
    }
    
    public function getProducts() {
        return [
            'products' => $this->productModel->getAllProducts(),
            'categories' => $this->productModel->getCategories(),
            'contact' => $this->contactModel->getContactInfo()
        ];
    }
    
    public function getServices() {
        return [
            'services' => $this->serviceModel->getAllServices(),
            'contact' => $this->contactModel->getContactInfo()
        ];
    }
    
    public function sendContactEmail($data) {
        // Validate required fields
        if (empty($data['name']) || empty($data['email']) || empty($data['message'])) {
            return [
                'success' => false,
                'message' => 'Kérjük, töltse ki az összes kötelező mezőt.'
            ];
        }
        
        // Validate email format
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return [
                'success' => false,
                'message' => 'Kérjük, adjon meg egy érvényes e-mail címet.'
            ];
        }
        
        try {
            // Create PHPMailer instance
            $mail = new PHPMailer(true);
            
            // Server settings
            $mail->isSMTP();
            $mail->Host = EmailConfig::SMTP_HOST;
            $mail->SMTPAuth = true;
            $mail->Username = EmailConfig::SMTP_USERNAME;
            $mail->Password = EmailConfig::SMTP_PASSWORD;
            $mail->SMTPSecure = EmailConfig::SMTP_ENCRYPTION;
            $mail->Port = EmailConfig::SMTP_PORT;
            $mail->CharSet = 'UTF-8';
            
            // Recipients
            $mail->setFrom(EmailConfig::FROM_EMAIL, EmailConfig::FROM_NAME);
            $mail->addAddress(EmailConfig::TO_EMAIL, EmailConfig::TO_NAME);
            $mail->addReplyTo($data['email'], $data['name']);
            
            // Content
            $mail->isHTML(true);
            $mail->Subject = EmailConfig::SUBJECT;
            
            $htmlMessage = "
                <h2>Új üzenet érkezett a weboldalról</h2>
                <p><strong>Név:</strong> " . htmlspecialchars($data['name']) . "</p>
                <p><strong>E-mail:</strong> " . htmlspecialchars($data['email']) . "</p>";
            
            if (!empty($data['phone'])) {
                $htmlMessage .= "<p><strong>Telefonszám:</strong> " . htmlspecialchars($data['phone']) . "</p>";
            }
            
            $htmlMessage .= "
                <p><strong>Üzenet:</strong></p>
                <p>" . nl2br(htmlspecialchars($data['message'])) . "</p>
                <hr>
                <p><em>Ez az üzenet a Leib Elsősegély weboldal kapcsolat formjából érkezett.</em></p>
            ";
            
            $mail->Body = $htmlMessage;
            
            // Plain text version
            $textMessage = "Új üzenet érkezett a weboldalról:\n\n";
            $textMessage .= "Név: " . htmlspecialchars($data['name']) . "\n";
            $textMessage .= "E-mail: " . htmlspecialchars($data['email']) . "\n";
            if (!empty($data['phone'])) {
                $textMessage .= "Telefonszám: " . htmlspecialchars($data['phone']) . "\n";
            }
            $textMessage .= "\nÜzenet:\n" . htmlspecialchars($data['message']) . "\n";
            $textMessage .= "\n---\nEz az üzenet a Leib Elsősegély weboldal kapcsolat formjából érkezett.";
            
            $mail->AltBody = $textMessage;
            
            $mail->send();
            
            return [
                'success' => true,
                'message' => 'Köszönjük üzenetét! Hamarosan felvesszük Önnel a kapcsolatot.'
            ];
            
        } catch (Exception $e) {
            error_log("Email sending failed: " . $mail->ErrorInfo);
            return [
                'success' => false,
                'message' => 'Hiba történt az üzenet küldése során. Kérjük, próbálja újra később.'
            ];
        }
    }
}
