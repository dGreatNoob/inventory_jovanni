#!/bin/bash

# Docker Cleanup Script for Inventory Jovanni
# This script removes old Docker images and ensures only the latest production image is running

set -e

echo "🧹 Docker Cleanup Script"
echo "========================"
echo ""

# Check if Docker is installed
if ! command -v docker &> /dev/null; then
    echo "❌ Docker is not installed."
    exit 1
fi

# Check if Docker Compose is installed
if command -v docker-compose &> /dev/null; then
    DOCKER_COMPOSE_CMD="docker-compose"
elif docker compose version &> /dev/null; then
    DOCKER_COMPOSE_CMD="docker compose"
else
    echo "❌ Docker Compose is not installed."
    exit 1
fi

COMPOSE_FILE="docker-compose.prod.yml"

# Ask for confirmation
echo "⚠️  This will:"
echo "   1. Stop and remove all Inventory Jovanni containers"
echo "   2. Remove all old Inventory Jovanni images"
echo "   3. Remove unused Docker images and build cache"
echo ""
read -p "Are you sure you want to continue? (yes/no): " CONFIRM

if [ "$CONFIRM" != "yes" ]; then
    echo "❌ Cleanup cancelled."
    exit 0
fi

echo ""
echo "🛑 Stopping and removing containers..."
$DOCKER_COMPOSE_CMD -f $COMPOSE_FILE down -v 2>/dev/null || true
echo "   ✅ Containers stopped and removed"

echo ""
echo "🗑️  Removing old Inventory Jovanni images..."
# Remove all images with inventory_jovanni in the name
docker images | grep "inventory_jovanni" | awk '{print $3}' | xargs -r docker rmi -f 2>/dev/null || true
echo "   ✅ Old images removed"

echo ""
echo "🧹 Cleaning up unused Docker resources..."
# Remove dangling images (untagged)
docker image prune -f
# Remove unused build cache
docker builder prune -f
echo "   ✅ Unused resources cleaned"

echo ""
echo "📦 Rebuilding latest production image..."
$DOCKER_COMPOSE_CMD -f $COMPOSE_FILE build --no-cache app
echo "   ✅ Latest image built"

echo ""
echo "🚀 Starting fresh containers..."
$DOCKER_COMPOSE_CMD -f $COMPOSE_FILE up -d
echo "   ✅ Containers started"

echo ""
echo "📊 Current Docker images:"
docker images | grep -E "inventory_jovanni|REPOSITORY" | head -5

echo ""
echo "📊 Current containers:"
docker ps | grep inventory-jovanni

echo ""
echo "======================================"
echo "✅ Cleanup complete!"
echo ""
echo "Only the latest production image is now running."
echo "======================================"
