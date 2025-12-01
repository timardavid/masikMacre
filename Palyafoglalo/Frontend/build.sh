#!/bin/bash

# Build script for MAMP production deployment

echo "🚀 Building Frontend for production..."

# Install dependencies if node_modules doesn't exist
if [ ! -d "node_modules" ]; then
    echo "📦 Installing dependencies..."
    npm install
fi

# Build
echo "🔨 Building..."
npm run build

# Copy build files to root
echo "📋 Copying build files..."
cp -r dist/* .

echo "✅ Build complete!"
echo "🌐 Open http://localhost/Palyafoglalo/Frontend/ in your browser"

