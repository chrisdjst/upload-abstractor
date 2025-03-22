
.PHONY: test build clean

test:
	vendor/bin/phpunit

build:
	composer dump-autoload -o

clean:
	rm -rf vendor composer.lock
