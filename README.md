# BrickMMO Stats Application

A comprehensive web analytics application built with PHP and W3.CSS for tracking page views, user behavior, and website statistics.

## Features

- Page view tracking with browser and OS detection
- Admin panel with asset management and statistics
- Real-time analytics dashboard with filtering
- CSV export functionality
- User management with role-based access
- Responsive design with W3.CSS framework

## Installation

### Requirements
- PHP 7.4+
- MySQL 5.7+
- Web server (Apache/Nginx)

### Setup
1. Create MySQL database `brickmmo_stats`
2. Import `setup.sql`
3. Configure database in `includes/config.php`
4. Access via web browser

### Default Login
- Username: `admin`
- Password: `password`

## Usage

### Asset Creation
1. Login to admin panel
2. Create new asset
3. Copy tracking code snippet
4. Add to your website

### Tracking Code
```html
<script src="tracker.js" data-asset-id="1"></script>
```

## File Structure
```
admin/              # Admin panel
includes/           # Core PHP files  
demo/              # Demo website 1
demo2/             # Demo website 2
tracker.js         # Tracking script
track.php          # Tracking endpoint
setup.sql          # Database setup
```

## Database Schema

- `assets` - Website tracking configurations
- `stats` - Page view tracking data
- `users` - Admin user accounts

## API

**POST /track.php**
```json
{
  "asset_id": 1,
  "url": "https://example.com",
  "browser": "Chrome",
  "os": "Windows 10",
  "referrer": "https://google.com"
}
```

## Developer
Tarun Shokeen - HTTP 5310 Capstone Project
   DB_HOST=localhost
   DB_USERNAME=root
   DB_PASSWORD=your_password
   DB_DATABASE=brickmmo_stats
   ```

4. **Access the Application**
   - Open `http://localhost/BrickMMO_stats-v1/` in your browser
   - Login to admin panel: `http://localhost/BrickMMO_stats-v1/admin/`
   - Default credentials: `admin` / `password` (change immediately!)

## 📊 Usage

### Creating a New Asset

1. Login to the admin panel
2. Navigate to "Assets" → "New Asset"
3. Enter asset name and description
4. Copy the generated tracking code
5. Paste into your website's HTML

### Tracking Code Integration

#### Method 1: Basic Script Tag
```html
<script src="/BrickMMO_stats-v1/tracker.js" data-asset-id="1"></script>
```

#### Method 2: Global Variable
```html
<script>window.BRICKMMO_ASSET_ID = 1;</script>
<script src="/BrickMMO_stats-v1/tracker.js"></script>
```

#### Method 3: CDN Integration
```html
<script src="https://cdn.brickmmo.com/stats/tracker.js" data-asset-id="1"></script>
```

### Viewing Statistics

1. Access the admin panel
2. Navigate to "Stats" to view all analytics
3. Filter by specific assets using the dropdown
4. View detailed breakdowns by browser, OS, and referrers
5. Export data for further analysis

## 🔧 Technical Details

### File Structure
```
BrickMMO_stats-v1/
├── admin/                  # Admin panel files
│   ├── index.php          # Dashboard
│   ├── login.php          # Authentication
│   ├── assets.php         # Asset management
│   ├── stats.php          # Statistics viewer
│   ├── users.php          # User management
│   └── auth.php           # Authentication functions
├── includes/              # Core PHP files
│   ├── config.php         # Configuration
│   ├── connect.php        # Database connection
│   ├── functions.php      # Utility functions
│   ├── header.php         # HTML header
│   └── footer.php         # HTML footer
├── tracker.js             # JavaScript tracking script
├── track.php              # Tracking endpoint
├── index.php              # Main landing page
├── setup.sql              # Database setup script
├── .env                   # Environment variables
└── README.md              # This file
```

### Database Schema

#### Assets Table
- `id`: Unique asset identifier
- `name`: Asset display name
- `description`: Optional description
- `snippet`: Generated tracking code
- `created_at`: Creation timestamp

#### Stats Table
- `id`: Unique tracking record ID
- `asset_id`: References assets table
- `url`: Tracked page URL
- `ip_address`: Visitor IP address
- `browser`: Detected browser
- `os`: Detected operating system
- `user_agent`: Full user agent string
- `referrer`: Referring URL
- `viewed_at`: Tracking timestamp

#### Users Table
- `id`: Unique user ID
- `username`: Login username
- `password`: Hashed password
- `role`: User role (admin/user)
- `created_at`: Account creation date

### API Endpoints

#### POST /track.php
Receives tracking data from JavaScript and stores in database.

**Request Body:**
```json
{
  "asset_id": 1,
  "url": "https://example.com/page",
  "browser": "Chrome",
  "os": "Windows 10",
  "referrer": "https://google.com",
  "screen_width": 1920,
  "screen_height": 1080,
  "viewport_width": 1200,
  "viewport_height": 800,
  "timezone": "America/New_York",
  "language": "en-US"
}
```

**Response:**
```json
{
  "status": "success",
  "message": "Tracking data saved successfully",
  "insert_id": 123,
  "asset_id": 1
}
```

## 🔒 Security Features

- **Password Hashing**: All passwords stored with PHP password_hash()
- **SQL Injection Protection**: Prepared statements throughout
- **CSRF Protection**: Session-based authentication
- **Role-based Access**: Admin/User permission levels
- **Input Sanitization**: All user input properly escaped

## 🌐 CDN Integration

For production deployment:

1. Upload `tracker.js` to your CDN
2. Update tracking snippets to use CDN URL
3. Configure CORS headers if needed
4. Consider caching strategies for optimal performance

## 🧪 Testing

### Local Testing
1. Create a test HTML file with tracking code
2. Open in browser and verify data appears in admin panel
3. Test different browsers and devices
4. Verify statistics accuracy

### Debug Mode
Enable debug mode by adding to tracking code:
```html
<script>window.BRICKMMO_DEBUG = true;</script>
```

## 📈 Performance Considerations

- **Lightweight Tracking**: Minimal JavaScript footprint
- **Asynchronous Loading**: Non-blocking script execution
- **Database Indexing**: Optimized queries for large datasets
- **Efficient Queries**: Pagination and filtering for large data sets

## 🤝 Contributing

This project is part of the BrickMMO ecosystem. For contributions:

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Submit a pull request

## 📄 License

This project is part of BrickMMO and follows the same licensing terms.

## 👨‍💻 Developer

**Tarun Shokeen**
- Course: HTTP 5310 Capstone
- Professor: Adam Thomas (codeadamca)
- Institution: Humber College

## 🔗 Related Projects

- [BrickMMO Parts v2](https://github.com/BrickMMO/parts-v2) - Reference implementation
- [BrickMMO CDN](https://github.com/BrickMMO/cdn-brickmmo) - Content delivery network
- [BrickMMO Main Site](https://brickmmo.com) - Official website

## 📞 Support

For technical support or questions:
- Create an issue in this repository
- Contact the development team through BrickMMO channels
- Refer to the [BrickMMO documentation](https://brickmmo.com)

---

**Note**: Remember to change default passwords and review security settings before deploying to production!

