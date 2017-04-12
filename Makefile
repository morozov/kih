serve:
	php -S localhost:8000 public/index.php
install:
	composer install
test:
	phpunit
