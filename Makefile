COVERAGE_MIN ?= 80

install:
	composer install

lint:
	composer exec phpcs -- --standard=PSR12 bin src tests tools

lint-fix:
	composer exec phpcbf -- --standard=PSR12 bin src tests tools

test:
	composer exec phpunit tests

test-coverage:
	composer exec phpunit tests -- --coverage-clover build/logs/clover.xml
	php tools/coverage-threshold.php build/logs/clover.xml $(COVERAGE_MIN)
