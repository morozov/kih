ifdef TRAVIS
PHPUNIT_FLAGS = --coverage-clover=coverage.clover
endif

install:
	composer install
serve:
	php -S localhost:8000 -t public public/index.php
test:
	vendor/bin/phpcs --standard=PSR2 -p --colors src public tests
	vendor/bin/phpunit --color $(PHPUNIT_FLAGS)
	vendor/bin/phpstan analyse -l 7 src
	vendor/bin/phpstan analyse -l 5 tests
	vendor/bin/psalm
	if [ -e vendor/bin/phan ] ; then vendor/bin/phan --progress-bar --color ; fi
	if php -m | grep -i xdebug ; then vendor/bin/infection --min-covered-msi=100 ; fi
coverage:
	$(eval TMPDIR=$(shell mktemp -d))
	vendor/bin/phpunit --coverage-html=$(TMPDIR)
	gnome-www-browser $(TMPDIR)/index.html
