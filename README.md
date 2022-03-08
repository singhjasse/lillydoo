Update database name in .env

composer install
npm install
npm run dev
php bin/console doctrine:migrations:migrate

symfony server:start
