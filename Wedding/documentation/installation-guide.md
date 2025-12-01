# 📖 Installation Guide

## 🚀 Quick Installation

### Step 1: Upload Files
1. Upload all files to your web hosting service
2. Make sure the folder structure is preserved

### Step 2: Replace Images
1. Open the `assets/images/` folder
2. Replace placeholder images with your own photos
3. Recommended size: minimum 800x600px

### Step 3: Customize Content
1. Open the `index.html` file
2. Modify names, dates and information
3. Update contact information

### Step 4: Customize Colors
1. Open the `assets/css/style.css` file
2. Find the `:root` section
3. Modify color variables

## 🎨 Detailed Customization

### Modifying Names
```html
<!-- In the hero section -->
<h1 class="hero-title">
    <span class="title-line">Elena</span>
    <span class="title-ampersand">&</span>
    <span class="title-line">Marcus</span>
</h1>
```

### Modifying Date
```html
<!-- In hero section -->
<p class="hero-date">September 14, 2024</p>

<!-- In JavaScript file for countdown timer -->
const weddingDate = new Date('2024-09-14T14:00:00').getTime();
```

### Modifying Event Information
```html
<!-- In Events section -->
<div class="event-card">
    <div class="event-details">
        <h3>Ceremony</h3>
        <p class="event-time">2:00 PM - 3:00 PM</p>
        <p class="event-location">St. Stephen's Basilica</p>
        <p class="event-address">Budapest, St. Stephen's Square 1</p>
    </div>
</div>
```

### Modifying Contact Information
```html
<!-- In RSVP section -->
<div class="contact-item">
    <i class="fas fa-phone"></i>
    <span>Elena: +36 30 123 4567</span>
</div>
<div class="contact-item">
    <i class="fas fa-envelope"></i>
    <span>elena.marcus.wedding@gmail.com</span>
</div>
```

## 🌈 Color Palette Customization

### Modifying CSS Variables
```css
:root {
    /* Gold colors */
    --primary-gold: #D4AF37;
    --secondary-gold: #B8860B;
    
    /* Rose colors */
    --accent-rose: #E8B4B8;
    --soft-pink: #F5E6E8;
    
    /* Burgundy colors */
    --deep-burgundy: #722F37;
    
    /* Neutral colors */
    --cream-white: #FDFCFA;
    --charcoal: #2C2C2C;
    --soft-gray: #F8F9FA;
}
```

### Color Palette Ideas
- **Classic**: Gold + white + black
- **Romantic**: Rose + gold + white
- **Modern**: Navy + gold + white
- **Natural**: Green + gold + white
- **Vintage**: Burgundy + gold + cream

## 📝 Content Editing

### Modifying Story Section
```html
<div class="timeline-item">
    <div class="timeline-date">2019</div>
    <div class="timeline-content">
        <h3>First Meeting</h3>
        <p>Your own story...</p>
    </div>
</div>
```

### Replacing Gallery Images
1. Create 6-12 images with minimum 800x600px resolution
2. Name them: `gallery1.jpg`, `gallery2.jpg`, etc.
3. Replace in the `assets/images/` folder
4. Update alt text and descriptions

### Modifying Gift Information
```html
<div class="gift-option">
    <h4>Bank Transfer</h4>
    <p>Your bank name</p>
    <p>Your name</p>
    <p>Your account number</p>
</div>
```

## 🔧 Special Settings

### Adding Google Fonts
```html
<link href="https://fonts.googleapis.com/css2?family=YourFont:wght@400;600;700&display=swap" rel="stylesheet">
```

### Replacing Favicon
1. Create a 32x32px or 64x64px icon
2. Name it `favicon.ico`
3. Place in the `assets/images/` folder

### Modifying Meta Tags
```html
<title>Your Name & Partner - Wedding Invitation</title>
<meta name="description" content="Your own description about the wedding">
```

## 📱 Mobile Optimization

### Image Optimization
- Use WebP format for better compression
- Create different sized images for different devices
- Use lazy loading for faster loading

### Touch Optimization
- Check that all buttons are at least 44px high
- Make sure text is readable on mobile
- Test on different browsers

## 🚀 Performance Optimization

### Image Optimization
```bash
# Use the following tools:
- TinyPNG (image compression)
- ImageOptim (Mac)
- Compressor.io (online)
```

### CSS and JS Minification
```bash
# CSS minification
- CSS Minifier
- CleanCSS

# JavaScript minification
- UglifyJS
- Terser
```

## 🔍 SEO Optimization

### Adding Meta Tags
```html
<meta name="keywords" content="wedding, invitation, website">
<meta name="author" content="Your name">
<meta property="og:title" content="Wedding invitation">
<meta property="og:description" content="Join us for our wedding">
<meta property="og:image" content="assets/images/hero-image.jpg">
```

### Adding Alt Text
```html
<img src="gallery1.jpg" alt="Elena and Marcus first meeting">
```

## 🛠 Troubleshooting

### Common Issues

**1. Images not showing**
- Check file names and paths
- Make sure images are uploaded

**2. Colors not changing**
- Check CSS syntax
- Cache clearing may be needed

**3. Animations not working**
- Check that JavaScript file is loading
- Check console for errors

**4. Mobile view issues**
- Check viewport meta tag
- Test on different devices

### Debug Tips
- Use browser Developer Tools
- Check console for errors
- Test on different browsers
- Use Lighthouse audit

## 📞 Support

If you need additional help:
- Read the README.md file
- Check CSS and HTML syntax
- Use browser Developer Tools
- Contact us if you have questions

---

**Happy installation and congratulations on your wedding! 💕**