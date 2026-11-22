# Facebook & Google Login Setup Guide

## Overview
This guide will help you set up Facebook and Google OAuth login for your health center system.

## Files Created
1. `config/oauth_config.php` - OAuth configuration file
2. `process/google_callback.php` - Google OAuth callback handler
3. `process/facebook_callback.php` - Facebook OAuth callback handler
4. Updated `login.php` - Added OAuth buttons with dynamic configuration check

## Step-by-Step Setup

### Step 1: Google OAuth Setup

1. **Go to Google Cloud Console**
   - Visit: https://console.cloud.google.com/
   - Sign in with your Google account

2. **Create or Select Project**
   - Click "Select a project" at the top
   - Click "NEW PROJECT"
   - Enter project name (e.g., "Health Center System")
   - Click "CREATE"

3. **Enable Google+ API**
   - Go to "APIs & Services" > "Library"
   - Search for "Google+ API"
   - Click on it and click "ENABLE"

4. **Create OAuth 2.0 Credentials**
   - Go to "APIs & Services" > "Credentials"
   - Click "CREATE CREDENTIALS" > "OAuth client ID"
   - If prompted, configure consent screen:
     - Choose "External"
     - Enter app name: "Barangay Health Center"
     - Enter user support email
     - Click "SAVE" and continue
   - Select "Web application"
   - Application name: "Health Center Login"
   - Authorized redirect URIs:
     - Add: `http://localhost/healthsystem/process/google_callback.php`
     - (For production, add your actual domain)
   - Click "CREATE"

5. **Copy Credentials**
   - Copy the "Client ID"
   - Copy the "Client Secret"

### Step 2: Facebook OAuth Setup

1. **Go to Facebook Developers**
   - Visit: https://developers.facebook.com/
   - Sign in with your Facebook account

2. **Create New App**
   - Click "Create App"
   - Select "Website" as app type
   - Enter display name: "Barangay Health Center"
   - Enter contact email
   - Click "Create App"

3. **Add Facebook Login Product**
   - In the app dashboard, click "Add Product"
   - Find "Facebook Login" and click "Set Up"
   - Click "Web" platform
   - Enter site URL: `http://localhost/healthsystem/`
   - Click "Save"

4. **Configure OAuth Redirect URI**
   - Go to "Facebook Login" > "Settings"
   - Under "Valid OAuth Redirect URIs", add:
     - `http://localhost/healthsystem/process/facebook_callback.php`
   - Click "Save Changes"

5. **Copy Credentials**
   - Go to "Settings" > "Basic"
   - Copy the "App ID"
   - Copy the "App Secret"

### Step 3: Update Configuration File

Open `config/oauth_config.php` and update the credentials:

```php
// Google OAuth Configuration
define('GOOGLE_CLIENT_ID', 'YOUR_ACTUAL_GOOGLE_CLIENT_ID');
define('GOOGLE_CLIENT_SECRET', 'YOUR_ACTUAL_GOOGLE_CLIENT_SECRET');
define('GOOGLE_REDIRECT_URI', 'http://localhost/healthsystem/process/google_callback.php');

// Facebook OAuth Configuration
define('FACEBOOK_APP_ID', 'YOUR_ACTUAL_FACEBOOK_APP_ID');
define('FACEBOOK_APP_SECRET', 'YOUR_ACTUAL_FACEBOOK_APP_SECRET');
define('FACEBOOK_REDIRECT_URI', 'http://localhost/healthsystem/process/facebook_callback.php');
define('FACEBOOK_GRAPH_VERSION', 'v18.0');
```

**Important:** Replace the placeholder values with your actual credentials.

### Step 4: Test the OAuth Login

1. Go to `http://localhost/healthsystem/login.php`
2. You should see "Continue with Facebook" and "Continue with Google" buttons
3. Click either button to test
4. If successful, you'll be redirected to the dashboard

## How It Works

### For New Users:
1. User clicks "Continue with Facebook/Google"
2. Redirected to Facebook/Google for authorization
3. After authorization, callback handler receives user data
4. System checks if email exists in database
5. If email doesn't exist, creates new account automatically
6. Username is generated from email prefix (e.g., user@gmail.com → "user")
7. Role is set to "patient" by default
8. User is logged in and redirected to dashboard

### For Existing Users:
1. User clicks "Continue with Facebook/Google"
2. Redirected to Facebook/Google for authorization
3. After authorization, callback handler receives user data
4. System checks if email exists in database
5. If email exists, logs in the user
6. User is redirected to dashboard

## Features

- ✅ Automatic account creation for new users
- ✅ Automatic login for existing users
- ✅ Secure OAuth 2.0 implementation
- ✅ Email-based user identification
- ✅ Dynamic configuration check (buttons disabled if not configured)
- ✅ Error handling with user-friendly messages

## Security Notes

- **Never commit `oauth_config.php` with real credentials** to version control
- **Use environment variables** for production credentials
- **HTTPS is required** for production OAuth (not localhost)
- **Add your production domain** to authorized redirect URIs
- **Review permissions** regularly in Google Console and Facebook Developer

## Troubleshooting

### "OAuth not configured" message
- Make sure you updated `config/oauth_config.php` with actual credentials
- Check that you replaced the placeholder values

### "Authorization failed" error
- Check your redirect URI matches exactly (including http/https)
- Verify your Client ID/App ID is correct
- Ensure the OAuth consent screen is configured

### "Failed to obtain access token" error
- Check Client Secret/App Secret is correct
- Verify the redirect URI matches what's in the console
- Make sure your app is not in "Testing" mode for production

### User creation fails
- Check database connection
- Verify users table has correct structure
- Check for duplicate email errors

## Production Deployment

For production, you need to:

1. **Update redirect URIs** to your actual domain:
   ```
   https://yourdomain.com/process/google_callback.php
   https://yourdomain.com/process/facebook_callback.php
   ```

2. **Enable HTTPS** (required for OAuth in production)

3. **Use environment variables** instead of hardcoded credentials:
   ```php
   define('GOOGLE_CLIENT_ID', getenv('GOOGLE_CLIENT_ID'));
   define('GOOGLE_CLIENT_SECRET', getenv('GOOGLE_CLIENT_SECRET'));
   ```

4. **Set your app to "Production" mode** in Google Console and Facebook Developer

5. **Add privacy policy URL** in your app settings

## Alternative: Use Composer Libraries

For a more robust implementation, consider using OAuth libraries:

```bash
composer require league/oauth2-google
composer require league/oauth2-facebook
```

The current implementation uses native cURL for simplicity and doesn't require Composer.

## Support

If you encounter issues:
1. Check the error messages displayed on the login page
2. Review the browser console for JavaScript errors
3. Check the PHP error logs
4. Verify your OAuth credentials are correct
5. Ensure redirect URIs match exactly
