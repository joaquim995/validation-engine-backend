# Deploy Laravel Backend to Render.com

## 🚨 IMPORTANT: Current Issue & Fix

Your backend is returning 404 errors because of the Procfile configuration. Follow these steps to fix it:

## ✅ Quick Fix Steps

### 1. **First, test your backend locally:**

```bash
cd E:\work\Angular\validation-engine-backend
php artisan route:list
```

This will show you all available routes. You should see routes like:
- `GET|HEAD  api/validation_rules`
- `POST      api/validation_rules`
- etc.

### 2. **Push all changes to Git:**

```bash
git add .
git commit -m "Fix Render deployment with updated Procfile and CORS"
git push origin main
```

### 3. **Update Render Configuration:**

Go to your Render dashboard and update these settings:

**Build Command:**
```bash
composer install --no-dev --optimize-autoloader && php artisan config:cache && php artisan route:cache && php artisan view:cache
```

**Start Command:**
```bash
php -S 0.0.0.0:$PORT -t public
```

### 4. **Set Environment Variables on Render:**

Make sure these are set in your Render dashboard:

```
APP_NAME=Validation Engine
APP_ENV=production
APP_KEY=base64:YOUR_APP_KEY_HERE
APP_DEBUG=false
APP_URL=https://validation-engine-backend.onrender.com

DB_CONNECTION=sqlite
DB_DATABASE=/opt/render/project/src/database/database.sqlite

LOG_CHANNEL=stack
LOG_LEVEL=error
```

**To generate APP_KEY:**
```bash
php artisan key:generate --show
```
Copy the output and paste it as the APP_KEY value.

### 5. **Redeploy on Render:**

After pushing your changes and updating the configuration, Render will automatically redeploy.

## 🧪 Testing After Deployment

### Test 1: Check if backend is running
Open in browser:
```
https://validation-engine-backend.onrender.com/
```
Should return: `{"message":"Validation Engine API","status":"running","version":"1.0.0"}`

### Test 2: Check API endpoint
Open in browser:
```
https://validation-engine-backend.onrender.com/api/validation_rules
```
Should return: `[]` or a list of validation rules

### Test 3: Check CORS headers
In your browser console on Netlify app:
```javascript
fetch('https://validation-engine-backend.onrender.com/api/validation_rules')
  .then(response => {
    console.log('CORS Headers:', response.headers);
    return response.json();
  })
  .then(data => console.log('Data:', data));
```

## 🔧 Alternative: Using Heroku-style Procfile

If the above doesn't work, try this Procfile instead:

```
web: vendor/bin/heroku-php-apache2 public/
```

And add to composer.json:
```json
"require": {
    "ext-mbstring": "*",
    "ext-pdo": "*",
    "ext-pdo_sqlite": "*"
}
```

## 📋 Files Modified

- ✅ `Procfile` - Updated to use $PORT variable
- ✅ `app/Http/Middleware/Cors.php` - Fixed CORS headers
- ✅ `routes/web.php` - Added test routes
- ✅ `build.sh` - Build script for Render
- ✅ `render.yaml` - Optional Render configuration

## 🐛 Troubleshooting

### Still getting 404 errors?

1. **Check Render logs:**
   - Go to your Render dashboard
   - Click on your service
   - Click "Logs"
   - Look for any errors during deployment

2. **Verify routes are registered:**
   Connect to Render shell and run:
   ```bash
   php artisan route:list
   ```

3. **Clear all caches:**
   ```bash
   php artisan cache:clear
   php artisan config:clear
   php artisan route:clear
   php artisan view:clear
   ```

4. **Check database:**
   Make sure the SQLite database file exists:
   ```bash
   ls -la database/database.sqlite
   ```

### CORS errors still happening?

Make sure:
1. The CORS middleware is being applied (check bootstrap/app.php)
2. The middleware is being loaded before the routes
3. Try adding `X-Requested-With` to allowed headers

## 💡 Pro Tip

For better performance and reliability on Render, consider:
1. Using PostgreSQL instead of SQLite
2. Adding Redis for caching
3. Using a proper web server (Apache/Nginx)

---

**After following these steps, your backend should work perfectly with your Netlify frontend!** 🚀
