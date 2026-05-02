# Git Deployment Steps for Hostinger

## Step 1: Access Hostinger Control Panel
1. Log in to your Hostinger account
2. Go to "Hosting" → "Manage" for your domain
3. In the left sidebar, click "Git"

## Step 2: Set Up Git Repository
1. Click "Set up Git" or "Add Repository"
2. Enter the repository URL: `https://github.com/bobbandacodes/stephensn.git`
3. Select branch: `main`
4. Click "Continue"

## Step 3: Deploy Files
1. Click "Deploy" to pull files from GitHub
2. Wait for deployment to complete
3. You should see "Deployment successful" message

## Step 4: Configure Production Settings
1. Go to "File Manager" in Hostinger
2. Navigate to your website files
3. Find `includes/config-production.php`
4. Rename it to `config.php` (backup the original first)
5. Set file permissions to 644 if needed

## Step 5: Set Up Database
1. Go to "Databases" in Hostinger control panel
2. Click "phpMyAdmin" for your database: `u763598602_stephensn`
3. Click "Import" tab
4. Choose your SQL file (if you have one)
5. Click "Go" to import

## Step 6: Create Uploads Directory
1. In File Manager, create folder: `assets/uploads`
2. Set permissions to 755
3. Test upload functionality

## Step 7: Test Website
1. Visit: `https://plum-gaur-348103.hostingersite.com`
2. Check if homepage loads
3. Test admin login at `/admin/`
4. Test image uploads

## Troubleshooting

### If deployment fails:
- Check repository URL is correct: `https://github.com/bobbandacodes/stephensn.git`
- Ensure branch is `main`
- Try redeploying

### If 500 error occurs:
- Check if `config.php` exists
- Verify database credentials
- Check file permissions

### If database connection fails:
- Verify database name: `u763598602_stephensn`
- Verify user: `u763598602_stephensn`
- Verify password: `Stepehen_SN_2026`

### If uploads don't work:
- Create `assets/uploads/` directory
- Set permissions to 755
- Check PHP upload limits

## Quick Commands Summary
- Repository: `https://github.com/bobbandacodes/stephensn.git`
- Branch: `main`
- Database: `u763598602_stephensn`
- Domain: `plum-gaur-348103.hostingersite.com`

## Need Help?
If you get stuck at any step, let me know the specific error message and I'll help you resolve it!
