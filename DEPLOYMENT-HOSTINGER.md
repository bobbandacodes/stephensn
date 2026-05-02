# Hostinger Deployment Guide

## Your Hostinger Account Details

**Domain:** `plum-gaur-348103.hostingersite.com`
**Database Name:** `u763598602_stephensn`
**Database User:** `u763598602_stephensn`
**Database Password:** `Stepehen_SN_2026`

## Quick Git Deployment

### Option 1: Git Push Deployment (Recommended)
1. All configuration is already set up in `config-production.php`
2. Add all files to Git: `git add .`
3. Commit changes: `git commit -m "Ready for Hostinger deployment"`
4. Push to GitHub: `git push origin main`
5. Deploy via Hostinger Git integration (see below)

### Option 2: Manual File Upload
1. Upload all files via Hostinger File Manager
2. Rename `config-production.php` to `config.php`
3. Set permissions and test

## Pre-Deployment Checklist

### 1. Database Setup
- [x] Database already exists: `u763598602_stephensn`
- [x] Database user configured: `u763598602_stephensn`
- [x] Credentials configured in `config-production.php`
- [ ] Import your database using phpMyAdmin in Hostinger panel

### 2. Configuration Updates
- [x] Database credentials configured in `config-production.php`
- [x] `BASE_URL` set to `https://plum-gaur-348103.hostingersite.com`
- [x] `CONTACT_EMAIL` set to `info@plum-gaur-348103.hostingersite.com`
- [ ] Rename `config-production.php` to `config.php` (backup original first)

### 3. File Upload
- [ ] Upload all files to Hostinger via File Manager or FTP
- [ ] Ensure directory structure is maintained
- [ ] Verify `.htaccess` file is uploaded

### 4. Permissions
- [ ] Set directory permissions to 755
- [ ] Set file permissions to 644
- [ ] Ensure `assets/uploads/` directory is writable (755)

### 5. Upload Directory Setup
- [ ] Create `assets/uploads/` directory if it doesn't exist
- [ ] Set permissions to 755 for uploads directory
- [ ] Test upload functionality in admin panel

## Post-Deployment Steps

### 1. Testing
- [ ] Test homepage loads correctly
- [ ] Test all navigation links work
- [ ] Test admin login and functionality
- [ ] Test image upload functionality
- [ ] Test contact forms if any

### 2. SSL Certificate
- [ ] Ensure SSL certificate is active (Hostinger provides free SSL)
- [ ] Test HTTPS redirect works
- [ ] Update any hardcoded HTTP links to HTTPS

### 3. Performance
- [ ] Enable caching in Hostinger control panel
- [ ] Test page load speeds
- [ ] Verify compression is working (check .htaccess)

## Important Notes

### Database Connection
Hostinger uses `localhost` as database host, not an external IP address.

### PHP Version
Ensure Hostinger is running PHP 7.4 or higher for this application.

### File Paths
The application uses relative paths, so no path changes should be needed beyond the config file.

### Security
- The `.htaccess` file includes security headers
- Database credentials are protected
- Admin area should be password protected

### Troubleshooting

#### 500 Internal Server Error
- Check `.htaccess` syntax
- Verify PHP version compatibility
- Check file permissions

#### Database Connection Error
- Verify database credentials in config.php
- Ensure database user has proper permissions
- Check that database exists

#### Upload Issues
- Verify uploads directory permissions (755)
- Check PHP upload limits in Hostinger panel
- Ensure sufficient disk space

## Hostinger Specific Settings

### Recommended PHP Settings (via Hostinger Control Panel)
- `memory_limit`: 256M
- `upload_max_filesize`: 64M
- `post_max_size`: 64M
- `max_execution_time`: 300

### Email Configuration
If using contact forms, configure SMTP settings in Hostinger panel or use Hostinger's email service.

## Backup Strategy
- Regular database backups via Hostinger control panel
- File backups before major updates
- Keep local copy of all files

## Support
- Hostinger 24/7 live chat support
- Check Hostinger knowledge base for common issues
- Review error logs in Hostinger control panel
