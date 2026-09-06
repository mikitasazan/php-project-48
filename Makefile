COVERAGE_MIN ?= 80

install:
	composer install

lint:
	composer exec phpcs -- --standard=PSR12 bin src tests

lint-fix:
	composer exec phpcbf -- --standard=PSR12 bin src tests

test:
	composer exec phpunit tests

test-coverage:
	composer exec phpunit tests -- --coverage-clover build/logs/clover.xml
	@php -r '$$m = simplexml_load_file("build/logs/clover.xml")->project->metrics; \
		$$total = (int) $$m["statements"]; \
		$$covered = (int) $$m["coveredstatements"]; \
		$$pct = $$total > 0 ? $$covered / $$total * 100 : 100.0; \
		printf("Total coverage: %.2f%% (min %d%%)\n", $$pct, $(COVERAGE_MIN)); \
		exit($$pct + 1e-9 < $(COVERAGE_MIN) ? 1 : 0);'
