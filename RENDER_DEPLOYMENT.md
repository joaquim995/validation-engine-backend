# Laravel Backend Deployment on Render

This guide explains how to deploy your Laravel validation engine backend to Render.com.

## ✅ CORS Configuration Fixed

The CORS middleware has been updated to properly handle requests from your Netlify frontend.

**Changes Made:**
- Updated `app/Http/Middleware/Cors.php` to handle preflight OPTIONS requests correctly
- Added proper CORS headers including `Access-Control-Allow-Credentials`
- Middleware is already registered in `bootstrap/app.php`

## 🚀 Deployment Steps

### 1. Push Your Code to Git

Make sure all your changes are committed and pushed:

```bash
git add .
git commit -m "Fix CORS for Netlify frontend"
git push origin main
```

### 2. Render Configuration

Your `Procfile` is already configured:
```
web: php artisan serve --host 0.0.0.0 --port 10000
```

### 3. Environment Variables on Render

Go to your Render dashboard and set these environment variables:

**Required Variables:**
- `APP_NAME`: "Validation Engine"
- `APP_ENV`: "production"
- `APP_KEY`: (Generate with `php artisan key:generate --show`)
- `APP_DEBUG`: "false"
- `APP_URL`: "https://validation-engine-backend.onrender.com"

**Database Variables** (if using PostgreSQL on Render):
- `DB_CONNECTION`: "pgsql"
- `DB_HOST`: (Provided by Render)
- `DB_PORT`: "5432"
- `DB_DATABASE`: (Your database name)
- `DB_USERNAME`: (Provided by Render)
- `DB_PASSWORD`: (Provided by Render)

**Or for SQLite** (simpler option):
- `DB_CONNECTION`: "sqlite"
- `DB_DATABASE`: "/opt/render/project/src/database/database.sqlite"

### 4. Build Command on Render

Set the build command in Render dashboard:

```bash
composer install --no-dev --optimize-autoloader && php artisan config:cache && php artisan route:cache && php artisan view:cache
```

### 5. Start Command

The start command is already in your `Procfile`:
```
php artisan serve --host 0.0.0.0 --port 10000
```

## 🔧 Post-Deployment

### Run Migrations

After deployment, you may need to run migrations. In Render's shell:

```bash
php artisan migrate --force
```

### Clear Cache (if needed)

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
```

## ✅ Verify CORS is Working

1. Check your Render logs after deployment
2. Visit your Netlify app: `https://validation-engine-frontend.netlify.app`
3. Open browser DevTools (F12) → Network tab
4. Make an API request (e.g., try to load validation rules)
5. Check the response headers - you should see:
   - `Access-Control-Allow-Origin: *`
   - `Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS`

## 🌐 API Endpoints

Your API is available at: `https://validation-engine-backend.onrender.com/api`

**Endpoints:**
- `POST /api/validation_rules/evaluate` - Validate form data
- `GET /api/validation_rules` - Get all validation rules
- `POST /api/validation_rules` - Create a new rule
- `PUT /api/validation_rules/{id}` - Update a rule
- `DELETE /api/validation_rules/{id}` - Delete a rule

## 🐛 Troubleshooting

### CORS Errors Still Occurring

If you still see CORS errors:

1. **Check Render logs** to ensure the middleware is being applied
2. **Verify the request URL** matches your backend URL exactly
3. **Clear browser cache** and try again

### 500 Internal Server Error

1. Check `APP_KEY` is set in Render environment variables
2. Run `php artisan key:generate --show` locally and copy the key
3. Make sure database connection is configured correctly

### Database Connection Failed

For SQLite:
1. Create the database file in the build command:
   ```bash
   touch database/database.sqlite
   ```
2. Make sure `storage/` directory is writable

For PostgreSQL:
1. Create a PostgreSQL database in Render
2. Copy the internal database URL from Render
3. Set the DB_* environment variables

## 📝 Local Development

To run locally:

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

Your local API will be available at: `http://localhost:8000/api`

---

**Backend is now configured and ready to accept requests from your Netlify frontend!** 🎉
