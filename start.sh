#!/bin/bash

echo "🚀 Starting Practice Exam Simulator..."
echo ""
echo "This will:"
echo "  ✓ Start PHP/Apache web server"
echo "  ✓ Start MySQL database"
echo "  ✓ Import sample questions"
echo "  ✓ Start phpMyAdmin"
echo ""

# Start Docker containers
docker-compose up -d

echo ""
echo "⏳ Waiting for services to be ready..."
sleep 10

echo ""
echo "✅ Application is ready!"
echo ""
echo "📱 Access the application:"
echo "   🌐 Main App:    http://localhost:8080"
echo "   🗄️  phpMyAdmin:  http://localhost:8081"
echo ""
echo "🔑 Database Credentials:"
echo "   Host: db"
echo "   User: root"
echo "   Pass: root"
echo ""
echo "🛑 To stop: docker-compose down"
echo "📊 To view logs: docker-compose logs -f"
