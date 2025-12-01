<?php
/**
 * Production Deployment Script
 * Run this script to prepare the application for production
 */

echo "🚀 Himesházi Óvoda - Production Deployment\n";
echo "==========================================\n\n";

// Check if running from command line
if (php_sapi_name() !== 'cli') {
    die("❌ This script must be run from command line\n");
}

// Step 1: Create necessary directories
echo "📁 Creating directories...\n";
$directories = [
    '../logs',
    '../uploads',
    '../uploads/images',
    '../uploads/documents'
];

foreach ($directories as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
        echo "✅ Created: $dir\n";
    } else {
        echo "ℹ️  Exists: $dir\n";
    }
}

// Step 2: Set proper permissions
echo "\n🔒 Setting permissions...\n";
$permissions = [
    '../logs' => '755',
    '../uploads' => '755',
    '../uploads/images' => '755',
    '../uploads/documents' => '755',
    '.htaccess' => '644'
];

foreach ($permissions as $path => $perm) {
    if (file_exists($path)) {
        chmod($path, octdec($perm));
        echo "✅ Set permissions $perm for: $path\n";
    }
}

// Step 3: Create production config
echo "\n⚙️  Creating production configuration...\n";
if (file_exists('config.production.php')) {
    copy('config.production.php', 'config.php');
    echo "✅ Production config activated\n";
} else {
    echo "❌ Production config not found\n";
}

// Step 4: Database setup instructions
echo "\n🗄️  Database Setup Instructions:\n";
echo "1. Create database: CREATE DATABASE himeshazi_ovoda CHARACTER SET utf8mb4 COLLATE utf8mb4_hungarian_ci;\n";
echo "2. Create user: CREATE USER 'ovoda_user'@'localhost' IDENTIFIED BY 'secure_password';\n";
echo "3. Grant permissions: GRANT SELECT, INSERT, UPDATE, DELETE ON himeshazi_ovoda.* TO 'ovoda_user'@'localhost';\n";
echo "4. Import data: mysql -u ovoda_user -p himeshazi_ovoda < Database/himeshazi_ovoda.sql\n";

// Step 5: Security checklist
echo "\n🔐 Security Checklist:\n";
echo "□ Update .env file with production values\n";
echo "□ Set strong database password\n";
echo "□ Configure SSL certificate\n";
echo "□ Update CORS allowed origins\n";
echo "□ Test all API endpoints\n";
echo "□ Verify file upload security\n";
echo "□ Check error logging\n";

// Step 6: Performance optimization
echo "\n⚡ Performance Optimization:\n";
echo "□ Enable PHP OPcache\n";
echo "□ Configure gzip compression\n";
echo "□ Set up CDN for static files\n";
echo "□ Optimize database indexes\n";

echo "\n✅ Deployment preparation complete!\n";
echo "📋 Next steps:\n";
echo "1. Upload files to production server\n";
echo "2. Run: php deploy.php\n";
echo "3. Configure web server\n";
echo "4. Import database\n";
echo "5. Test all functionality\n";
echo "6. Monitor logs for errors\n\n";

echo "🎉 Ready for production! 🎉\n";
?>
