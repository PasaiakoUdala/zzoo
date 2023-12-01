#!/bin/bash

APP = zzoo
USER_ID = $(shell id -u)
GROUP_ID= $(shell id -g)
user==www-data

help:
	@echo 'Erabiltzeko: make [target]'
	@echo
	@echo 'Agindu erabilgarriak:'
	@egrep '^(.+)\:\ ##\ (.+)' ${MAKEFILE_LIST} | column -t -c 2 -s ":#"

build: ## Sortu Docker irudiak
	docker compose --env-file .env.local build

build-force: ## Sortu Docker irudiak aurrekoak ezabatzen (--force-rm --no-cache)
	docker compose --env-file .env.local build --force-rm --no-cache

restart: ## kontainerrak stop & start
	$(MAKE) stop && $(MAKE) run

run: ## kontenedorea abiarazi .env.local erabiliz
	docker compose --env-file .env.local up -d

stop: ## kontenedorea gelditu
	docker compose down

ssh: ## kontenedorera ssh sarbidea
	docker compose exec api bash

cc: ## symfony cache clear
	docker compose exec api ./bin/console c:c
