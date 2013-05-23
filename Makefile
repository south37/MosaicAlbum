PHP=$(shell which php)
CURL=$(shell which curl)
PHPUNIT=phpunit.phar
TESTRUNNER=vendor/bin/testrunner
HOST=localhost
PORT=9999

all: test

setup:
	$(PHP) -r "eval('?>'.file_get_contents('https://getcomposer.org/installer'));"
	$(CURL) -SslO https://raw.github.com/brtriver/dbup/master/dbup.phar
	$(CURL) -SslO http://cs.sensiolabs.org/get/php-cs-fixer.phar
install: setup
	$(PHP) composer.phar install
	$(PHP) vendor/bin/testrunner compile -p vendor/autoload.php
test:
	$(PHP) $(PHPUNIT) --tap --colors ./tests
testrunner:
	$(TESTRUNNER) "phpunit"  --preload-script ./vendor/autoload.php  --phpunit-config ./phpunit.xml --autotest ./tests ./src
fixer:
	$(PHP) php-cs-fixer.phar ./src --level=all
server:
	$(PHP) -S $(HOST):$(PORT) -t ./public_html
mig-up:
	$(PHP) dbup.phar up
