# build front end
frontend_domain="medrec.gldrp.com"
frontend_files_path="dr-simon-medrec-fe"

cd ./$frontend_files_path
npm install
npm run build

# remove existing files from FE public_html
rm -rf ../$frontend_domain/public_html/*

echo "📁 Moving regular files from build files to FE public_html..."
mv ./dist/spa/* ../$frontend_domain/public_html/

echo "📁 Moving dotfiles from build files to FE public_html..."
mv ./dist/spa/.[!.]* ../$frontend_domain/public_html/..?* ./ 2>/dev/null

# remove build files
rm -rf ./dist