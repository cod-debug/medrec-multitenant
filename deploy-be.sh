#!/bin/bash
backend_domain="medrecbe.gldrp.com"
backend_files_path="dr-simon-medrec-be"

# Copy regular files from dr-simon-medrec-be
echo "📁 Copying regular files from $backend_files_path..."
cp -r ./$backend_files_path/* ./$backend_domain/

# Copy files that starts from dot (.)
echo "📁 Copying dotfiles from $backend_files_path..."
cp -r ./$backend_files_path/.[!.]* ./$backend_domain/..?* ./ 2>/dev/null

# Copy regular files from public folder to public_html
echo "📁 Copying regular files from public to public_html..."
cp -r ./$backend_files_path/public/* ./$backend_domain/public_html/

# Copy files that starts from dot (.) from public folder to public_html
echo "📁 Copying dotfiles from public to public_html..."
cp -r ./$backend_files_path/public/.[!.]* ./$backend_domain/public/..?* ./$backend_domain/public_html/ 2>/dev/null

echo "✅ All files copied successfully."

# setup back end
cd ./$backend_domain
composer2 install
php artisan optimize
php artisan migrate

