#!/bin/bash
# setup.sh
# This script sets up the booking_app database using the setup.sql file.

# --- Configuration ---
DB_HOST="localhost"
DB_USER="webdev"        # MySQL username
DB_PASS="W3bD£velopment"    # MySQL password
DB_NAME="booking_app"

# --- Create the database ---
echo "Creating database '$DB_NAME' if it doesn't exist..."
mysql -u"$DB_USER" -p"$DB_PASS" -h "$DB_HOST" -e "CREATE DATABASE IF NOT EXISTS \`$DB_NAME\` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

if [ $? -ne 0 ]; then
    echo "Error creating database. Please check your credentials and MySQL connection."
    exit 1
fi

# --- Import the SQL file ---
echo "Importing setup.sql into database '$DB_NAME'..."
mysql -u"$DB_USER" -p"$DB_PASS" -h "$DB_HOST" "$DB_NAME" < setup.sql

if [ $? -eq 0 ]; then
    echo "Database setup complete."
else
    echo "Error importing setup.sql."
    exit 1
fi
