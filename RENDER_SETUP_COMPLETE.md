# 🚀 Complete Render.com Setup Guide

## Problem: Backend Works Locally but Not on Render

Your backend works with MySQL locally, but Render requires different configuration.

---

## 🎯 Solution: Choose One Option

### **Option 1: PostgreSQL (RECOMMENDED for Production)**

Render's free tier includes PostgreSQL. This is the best option for production.

#### Step 1: Create PostgreSQL Database on Render

1. Go to https://dashboard.render.com
2. Click **"New +"** → **"PostgreSQL"**
3. Configure:
   - **Name**: `validation-engine-db`
   - **Database**: `validation_engine`
   - **User**: (auto-generated)
   - **Region**: Same as your web service
   - **Plan**: Free
4. Click **"Create Database"**
5. Wait 1-2 minutes for database to be created

#### Step 2: Get Database Connection Info

After creation, you'll see:
- **Internal Database URL** (use this!)
- **External Database URL**
- **PSQL Command**

Click on "Info" tab and note these values:
- Hostname
- Port
- Database
- Username  
- Password

#### Step 3: Configure Environment Variables on Render

Go to your **web service** (backend) → **Environment**:

Add these variables:

```
APP_NAME=Validation Engine
APP_ENV=production
APP_KEY=base64:YOUR_KEY_HERE
APP_DEBUG=false
APP_URL=https://validation-engine-backend.onrender.com

DB_CONNECTION=pgsql
DB_HOST=<hostname from database info>
DB_PORT=5432
DB_DATABASE=<database name>
DB_USERNAME=<username from database info>
DB_PASSWORD=<password from database info>

LOG_CHANNEL=stack
LOG_LEVEL=error

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
```

**To generate APP_KEY:**
```bash
php artisan key:generate --show
```
Copy the entire output including `base64:` prefix.

#### Step 4: Update Build Script

Update `build.sh`:

```bash
#!/bin/bash

# Install dependencies
composer install --no-dev --optimize-autoloader

# Run migrations
php artisan migrate --force

# Cache configuration
php artisan config:cache
php artisan route:cache
```

#### Step 5: Deploy

```bash
git add .
git commit -m "Configure for PostgreSQL on Render"
git push origin main
```

Render will auto-deploy. Watch the logs!

---

### **Option 2: SQLite (SIMPLE, Good for Testing)**

SQLite doesn't require a separate database service.

#### Step 1: Update Build Script

Update `build.sh`:

```bash
#!/bin/bash

# Install dependencies
composer install --no-dev --optimize-autoloader

# Create SQLite database file
mkdir -p database
touch database/database.sqlite
chmod 664 database/database.sqlite

# Run migrations
php artisan migrate --force

# Cache configuration
php artisan config:cache
php artisan route:cache
```

#### Step 2: Set Environment Variables on Render

Go to your web service → **Environment**:

```
APP_NAME=Validation Engine
APP_ENV=production
APP_KEY=base64:YOUR_KEY_HERE
APP_DEBUG=false
APP_URL=https://validation-engine-backend.onrender.com

DB_CONNECTION=sqlite
DB_DATABASE=/opt/render/project/src/database/database.sqlite

LOG_CHANNEL=stack
LOG_LEVEL=error

SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=sync
```

#### Step 3: Deploy

```bash
git add .
git commit -m "Configure for SQLite on Render"
git push origin main
```

---

## 🔧 Render Service Configuration

### Build Command
```bash
bash build.sh
```

### Start Command
```bash
php -S 0.0.0.0:$PORT -t public
```

### Environment
- Runtime: **PHP 8.2** or **PHP 8.3**

---

## 🧪 Testing After Deployment

### 1. Check Render Logs

In Render dashboard → Your service → **Logs**

Look for:
- ✅ `Migration table created successfully`
- ✅ `Migrated: 2025_10_03_060652_create_validation_rules_table`
- ✅ `Server started`

❌ If you see errors like:
- `SQLSTATE[HY000] [2002] Connection refused` → Check database connection
- `Database file not found` → SQLite file wasn't created
- `No APP_KEY` → Generate and set APP_KEY

### 2. Test API Endpoints

Open these in your browser:

**Root:**
```
https://validation-engine-backend.onrender.com/
```
Expected: `{"message":"Validation Engine API","status":"running","version":"1.0.0"}`

**API Test:**
```
https://validation-engine-backend.onrender.com/api/test
```
Expected: `{"message":"API is working!","timestamp":"..."}`

**Validation Rules:**
```
https://validation-engine-backend.onrender.com/api/validation_rules
```
Expected: `[]` (empty array)

### 3. Test from Frontend

Go to: `https://validation-engine-frontend.netlify.app`

- ✅ No CORS errors
- ✅ Admin page loads validation rules
- ✅ Can create new rules
- ✅ Can validate work orders

---

## 🐛 Common Issues & Solutions

### Issue 1: "SQLSTATE[HY000] [2002] Connection refused"

**Solution:**
- Database credentials are wrong
- Database service not running
- Check DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD

### Issue 2: "No application encryption key has been specified"

**Solution:**
```bash
php artisan key:generate --show
```
Copy the output and set it as APP_KEY environment variable on Render.

### Issue 3: "Database file not found" (SQLite)

**Solution:**
- Make sure `build.sh` creates the database file
- Check permissions: `chmod 664 database/database.sqlite`
- Verify path: `/opt/render/project/src/database/database.sqlite`

### Issue 4: "Route not found" or 404 errors

**Solution:**
- Check Start Command is: `php -S 0.0.0.0:$PORT -t public`
- Verify routes: In Render shell, run `php artisan route:list`
- Clear cache: `php artisan cache:clear && php artisan config:clear`

### Issue 5: CORS errors still appearing

**Solution:**
- Verify CORS middleware is in `bootstrap/app.php`
- Check response headers in browser DevTools
- Make sure the request reaches the backend (check Render logs)

---

## 📊 Render Service Health Check

### In Render Dashboard

**Metrics to check:**
- ✅ CPU usage < 50%
- ✅ Memory usage < 512MB
- ✅ HTTP requests returning 200
- ✅ No deployment failures

### In Render Shell

Access shell: Dashboard → Your Service → **Shell**

Run these commands:
```bash
# Check PHP version
php -v

# List routes
php artisan route:list

# Check database connection
php artisan tinker
>>> DB::connection()->getPdo();

# Check if migrations ran
php artisan migrate:status
```

---

## 🎯 Quick Checklist

- [ ] Database created (PostgreSQL) OR build.sh updated (SQLite)
- [ ] Environment variables set on Render
- [ ] APP_KEY generated and set
- [ ] DB_* variables configured correctly
- [ ] build.sh updated
- [ ] Code pushed to Git
- [ ] Render auto-deployed successfully
- [ ] Logs show no errors
- [ ] All test URLs return expected results
- [ ] Frontend can connect to backend

---

## 🆘 Still Not Working?

### Collect This Information:

1. **Render Logs** (last 50 lines)
2. **Environment variables** (names only, not values)
3. **Error messages** from browser console
4. **Which option** you chose (PostgreSQL or SQLite)

### Try This:

1. **Manual Redeploy:**
   - Render Dashboard → Your Service → "Manual Deploy" → "Clear build cache & deploy"

2. **Check Database:**
   - If PostgreSQL: Go to database service, check it's "Available"
   - If SQLite: In Render shell, run `ls -la database/`

3. **Verify Build:**
   - Check build logs for migration output
   - Look for "Migration table created successfully"

---

**Good luck! Your backend will be running smoothly in a few minutes!** 🚀
