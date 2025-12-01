# 🚀 Database Setup Guide - FitLife Studio

## 📋 Prerequisites

- MAMP/XAMPP installed and running
- MySQL server running
- PHP 7.4+ enabled
- Web browser for testing

## 🗄️ Database Setup

### 1. Start MAMP/XAMPP
```bash
# Start MAMP
# Make sure MySQL and Apache are running
# Default ports: Apache (8888), MySQL (8889)
```

### 2. Access phpMyAdmin
```
URL: http://localhost:8888/phpMyAdmin/
Username: root
Password: root (default MAMP password)
```

### 3. Create Database
1. Click "New" in phpMyAdmin
2. Database name: `fitness_studio`
3. Collation: `utf8mb4_unicode_ci`
4. Click "Create"

### 4. Import Database Schema
1. Select the `fitness_studio` database
2. Click "Import" tab
3. Choose file: `database.sql`
4. Click "Go"

### 5. Verify Tables
You should see these tables:
- `services`
- `classes`
- `about_content`
- `contact_info`
- `contact_submissions`
- `site_settings`

## ⚙️ Configuration

### 1. Database Connection
Edit `config/database.php`:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'fitness_studio');
define('DB_USER', 'root');
define('DB_PASS', 'root'); // MAMP default
define('DB_CHARSET', 'utf8mb4');
```

### 2. Site URL
Update the site URL in `config/database.php`:
```php
define('SITE_URL', 'http://localhost:8888/Codeing/Code/');
```

## 🧪 Testing the Setup

### 1. Test Database Connection
Visit: `http://localhost:8888/Codeing/Code/api.php?endpoint=site-settings`

Expected response:
```json
{
    "status": 200,
    "message": "Site settings retrieved successfully",
    "data": {
        "site_name": "FitLife Studio",
        "site_tagline": "Your Health Journey Starts Here",
        ...
    }
}
```

### 2. Test Services Endpoint
Visit: `http://localhost:8888/Codeing/Code/api.php?endpoint=services`

### 3. Test Classes Endpoint
Visit: `http://localhost:8888/Codeing/Code/api.php?endpoint=classes`

### 4. Test Contact Form
1. Open the website: `http://localhost:8888/Codeing/Code/`
2. Scroll to Contact section
3. Fill out the form
4. Submit and check for success message

## 📊 Sample Data

The database comes with sample data:

### Services
- Personal Training
- Yoga & Pilates  
- Group Classes

### Classes
- Morning Yoga (Mon, Wed, Fri 7:00-8:00 AM)
- HIIT Training (Tue, Thu 6:00-7:00 PM)
- Pilates (Wed, Fri 8:00-9:00 AM)
- Spinning (Mon, Wed 7:00-8:00 PM)
- Zumba (Sat 10:00-11:00 AM)
- CrossFit (Sun 9:00-10:00 AM)

### About Content
- Professional Trainers
- Flexible Schedule
- Holistic Approach

### Contact Info
- Phone: +1 (555) 123-4567
- Email: info@fitnessstudio.com
- Address: 123 Fitness Street, Health City, HC 12345
- Hours: Mon-Fri: 6:00 AM - 10:00 PM, Sat-Sun: 8:00 AM - 8:00 PM

## 🔧 Customization

### 1. Update Site Settings
```sql
UPDATE site_settings SET setting_value = 'Your Studio Name' WHERE setting_key = 'site_name';
UPDATE site_settings SET setting_value = 'Your Tagline' WHERE setting_key = 'site_tagline';
```

### 2. Add New Services
```sql
INSERT INTO services (title, description, icon, features) VALUES
('New Service', 'Service description', '🏋️', '["Feature 1", "Feature 2", "Feature 3"]');
```

### 3. Add New Classes
```sql
INSERT INTO classes (title, description, schedule, category, icon, max_participants, price, instructor) VALUES
('New Class', 'Class description', 'Monday 6:00-7:00 PM', 'evening', '💪', 20, 25.00, 'Instructor Name');
```

### 4. Update Contact Information
```sql
UPDATE contact_info SET content = 'Your Phone Number' WHERE type = 'phone';
UPDATE contact_info SET content = 'your@email.com' WHERE type = 'email';
UPDATE contact_info SET content = 'Your Address' WHERE type = 'address';
```

## 🎨 Color Customization

### 1. Update CSS Variables
Edit `assets/css/style.css`:
```css
:root {
    --primary-color: #22c55e;    /* Green 500 */
    --secondary-color: #16a34a; /* Green 600 */
    --accent-color: #4ade80;    /* Green 400 */
}
```

### 2. Update Database Colors
```sql
UPDATE site_settings SET setting_value = '#your-color' WHERE setting_key = 'primary_color';
```

## 🔒 Security Features

### 1. CSRF Protection
- All forms include CSRF tokens
- Tokens are validated server-side
- Tokens expire after 1 hour

### 2. Input Validation
- Server-side validation for all inputs
- XSS protection with htmlspecialchars()
- SQL injection protection with prepared statements

### 3. Error Handling
- Errors are logged to `error.log`
- User-friendly error messages
- No sensitive information exposed

## 📱 Features

### 1. Dynamic Content Loading
- All content loads from database
- Real-time updates without page refresh
- Responsive design for all devices

### 2. Contact Form
- Validates all inputs
- Stores submissions in database
- Sends email notifications (optional)

### 3. Class Filtering
- Filter classes by time (morning/evening/weekend)
- Smooth animations
- Responsive grid layout

### 4. SEO Optimized
- Semantic HTML structure
- Meta tags from database
- Clean URLs
- Fast loading times

## 🚨 Troubleshooting

### Common Issues

#### 1. Database Connection Failed
```
Error: Database connection failed
Solution: Check MAMP is running and credentials are correct
```

#### 2. API Endpoints Not Working
```
Error: 404 Not Found
Solution: Check .htaccess file and Apache mod_rewrite
```

#### 3. Contact Form Not Submitting
```
Error: CSRF token validation failed
Solution: Check JavaScript is loading content-loader.js
```

#### 4. Content Not Loading
```
Error: CORS or network error
Solution: Check API endpoints are accessible
```

### Debug Mode
Enable debug mode in `config/database.php`:
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

## 📈 Performance Optimization

### 1. Database Indexing
```sql
CREATE INDEX idx_classes_category ON classes(category);
CREATE INDEX idx_contact_status ON contact_submissions(status);
```

### 2. Caching
- Enable browser caching for static assets
- Use CDN for external resources
- Minify CSS and JavaScript for production

### 3. Image Optimization
- Use WebP format for images
- Implement lazy loading
- Compress images before upload

## 🔄 Backup & Maintenance

### 1. Database Backup
```bash
mysqldump -u root -p fitness_studio > backup.sql
```

### 2. Regular Maintenance
- Clean old contact submissions
- Update site settings
- Monitor error logs
- Update dependencies

---

**Setup Complete! 🎉**

Your FitLife Studio website is now ready with:
- ✅ Dynamic content from database
- ✅ Green color scheme for health & wellness
- ✅ English language interface
- ✅ Contact form with validation
- ✅ Responsive design
- ✅ SEO optimization

Visit: `http://localhost:8888/Codeing/Code/` to see your website!
