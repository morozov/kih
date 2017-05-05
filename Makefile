.PHONY: coverage

serve:
	php -S localhost:8000 public/index.php
install:
	composer install
test:
	phpunit
coverage:
	$(eval TMPDIR=$(shell mktemp -d))
	phpunit --coverage-html=$(TMPDIR)
	gnome-www-browser $(TMPDIR)/index.html
