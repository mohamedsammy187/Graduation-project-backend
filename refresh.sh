#!/bin/bash


echo "🛑 Stopping any running Laravel server..."
pkill -f "php artisan serve" 2>/dev/null

echo "🧹 Clearing all Laravel caches..."
php artisan optimize:clear
rm -rf bootstrap/cache/*.php

echo "🗑️ Dropping & recreating database schema..."
php artisan migrate:fresh --seed



echo "🚀 Starting Laravel server..."
php artisan serve
