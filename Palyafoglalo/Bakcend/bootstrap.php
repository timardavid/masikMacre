<?php
/**
 * Bootstrap File
 * Autoloads all necessary classes
 */

// Define base path
define('BASE_PATH', __DIR__);

// Load configuration
require_once __DIR__ . '/config/config.php';

// Error handling
require_once __DIR__ . '/middleware/ErrorHandler.php';
ErrorHandler::register();

// CORS handling
require_once __DIR__ . '/middleware/CorsMiddleware.php';
CorsMiddleware::handle();

// Core classes
require_once __DIR__ . '/core/Database.php';
require_once __DIR__ . '/core/Model.php';
require_once __DIR__ . '/core/BaseController.php';
require_once __DIR__ . '/core/Router.php';

// Models
require_once __DIR__ . '/models/RoleModel.php';
require_once __DIR__ . '/models/UserModel.php';
require_once __DIR__ . '/models/SurfaceModel.php';
require_once __DIR__ . '/models/CourtModel.php';
require_once __DIR__ . '/models/CourtImageModel.php';
require_once __DIR__ . '/models/CourtReviewModel.php';
require_once __DIR__ . '/models/BookingModel.php';
require_once __DIR__ . '/models/PricingModel.php';
require_once __DIR__ . '/models/BookingRuleModel.php';

// Services
require_once __DIR__ . '/services/AuthService.php';
require_once __DIR__ . '/services/BookingService.php';
require_once __DIR__ . '/services/PricingService.php';
require_once __DIR__ . '/services/EmailService.php';
require_once __DIR__ . '/services/CourtImageService.php';
require_once __DIR__ . '/services/CourtReviewService.php';

// Middleware
require_once __DIR__ . '/middleware/AuthMiddleware.php';

// Controllers
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/CourtController.php';
require_once __DIR__ . '/controllers/CourtImageController.php';
require_once __DIR__ . '/controllers/CourtReviewController.php';
require_once __DIR__ . '/controllers/BookingController.php';
require_once __DIR__ . '/controllers/PricingController.php';


