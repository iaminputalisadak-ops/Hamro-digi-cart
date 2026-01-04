#!/bin/bash

echo "Starting PHP Backend Server..."
echo ""
echo "Backend will be available at:"
echo "  Local:  http://localhost:8000"
echo "  Network: http://YOUR_IP:8000 (accessible from other devices)"
echo "Admin Panel: http://localhost:8000/admin/login.php"
echo ""
echo "Press Ctrl+C to stop the server"
echo ""

cd backend
php -S 0.0.0.0:8000

