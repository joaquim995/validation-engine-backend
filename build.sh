#!/bin/bash

echo "🔧 Installing dependencies..."
composer install --no-dev --optimize-autoloader

echo "📁 Setting up database..."
# Create database directory if it doesn't exist
mkdir -p database

# For SQLite: Create database file
if [ "$DB_CONNECTION" = "sqlite" ]; then
    touch database/database.sqlite
    chmod 664 database/database.sqlite
    echo "✅ SQLite database file created"
fi

echo "🗄️ Running migrations..."
php artisan migrate --force

echo "⚡ Caching configuration..."
php artisan config:clear
php artisan route:clear
php artisan config:cache
php artisan route:cache

echo "✅ Build complete!"
