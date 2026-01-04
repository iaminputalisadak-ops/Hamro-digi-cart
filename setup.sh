#!/bin/bash

echo "========================================"
echo "Hamro Digi Cart - Automated Setup"
echo "========================================"
echo ""

# Check if PHP is installed
if ! command -v php &> /dev/null; then
    echo "ERROR: PHP is not installed"
    echo "Please install PHP first"
    exit 1
fi

# Check if npm is installed
if ! command -v npm &> /dev/null; then
    echo "ERROR: npm is not installed"
    echo "Please install Node.js and npm first"
    exit 1
fi

echo "[1/3] Setting up backend..."
cd backend
php setup.php
if [ $? -ne 0 ]; then
    echo "Backend setup failed!"
    exit 1
fi
cd ..

echo ""
echo "[2/3] Installing React dependencies..."
npm install
if [ $? -ne 0 ]; then
    echo "npm install failed!"
    exit 1
fi

echo ""
echo "[3/3] Creating environment file..."
if [ ! -f .env ]; then
    echo "REACT_APP_API_URL=http://localhost/backend/api" > .env
    echo "Created .env file with default API URL"
else
    echo ".env file already exists"
fi

echo ""
echo "========================================"
echo "Setup Complete!"
echo "========================================"
echo ""
echo "To start the application:"
echo "1. Start backend: cd backend && php -S localhost:8000"
echo "2. Start frontend: npm start"
echo ""
echo "Admin Panel: http://localhost:8000/admin/login.php"
echo "Username: admin"
echo "Password: admin123"
echo ""






