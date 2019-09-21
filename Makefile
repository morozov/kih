install:
	composer install
serve:
	php -S localhost:8000 -t public public/index.php
test:
	vendor/bin/phpcs
	vendor/bin/phpunit
	vendor/bin/phpstan analyse
	vendor/bin/psalm
	vendor/bin/phan --progress-bar --color
	if php -m | grep -i xdebug ; then vendor/bin/infection --min-msi=100 ; fi
coverage:
	$(eval TMPDIR=$(shell mktemp -d))
	vendor/bin/phpunit --coverage-html=$(TMPDIR)
	gnome-www-browser $(TMPDIR)/index.html
