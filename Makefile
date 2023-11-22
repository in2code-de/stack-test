## Show this help
help:
	echo "$(EMOJI_interrobang) Makefile version $(VERSION) help "
	echo ''
	echo 'About this help:'
	echo '  Commands are ${BLUE}blue${RESET}'
	echo '  Targets are ${YELLOW}yellow${RESET}'
	echo '  Descriptions are ${GREEN}green${RESET}'
	echo ''
	echo 'Usage:'
	echo '  ${BLUE}make${RESET} ${YELLOW}<target>${RESET}'
	echo ''
	echo 'Targets:'
	awk '/^[a-zA-Z\-\_0-9]+:/ { \
		helpMessage = match(lastLine, /^## (.*)/); \
		if (helpMessage) { \
			helpCommand = substr($$1, 0, index($$1, ":")+1); \
			helpMessage = substr(lastLine, RSTART + 3, RLENGTH); \
			printf "  ${YELLOW}%-${TARGET_MAX_CHAR_NUM}s${RESET} ${GREEN}%s${RESET}\n", helpCommand, helpMessage; \
		} \
	} \
	{ lastLine = $$0 }' $(MAKEFILE_LIST)

## Wait for the mysql container to be fully provisioned
.mysql-wait:
	echo "$(EMOJI_ping_pong) Checking DB up and running"
	while ! docker compose exec -T mysql mysql -uroot -proot app -e "SELECT 1;" &> /dev/null; do \
		echo "$(EMOJI_face_with_rolling_eyes) Waiting for database ..."; \
		sleep 1; \
	done;

## Removes all containers and volumes
destroy: stop
	echo "$(EMOJI_litter) Removing the project"
	docker compose down -v --remove-orphans

## Starts docker compose up -d
start: .docker-pull .docker-start
	make urls

## Stop all containers
stop:
	echo "$(EMOJI_stop) Shutting down"
	docker compose stop
	sleep 0.4
	docker compose down --remove-orphans

## Install required packages on this computer, skips installation if already present
.install-packages:
	if [[ "$$OSTYPE" == "linux-gnu" ]]; then \
		if [[ "$$(command -v certutil > /dev/null; echo $$?)" -ne 0 ]]; then sudo apt install libnss3-tools; fi; \
		if [[ "$$(command -v mkcert > /dev/null; echo $$?)" -ne 0 ]]; then sudo curl -L https://github.com/FiloSottile/mkcert/releases/download/v1.4.1/mkcert-v1.4.1-linux-amd64 -o /usr/local/bin/mkcert; sudo chmod +x /usr/local/bin/mkcert; fi; \
	elif [[ "$$OSTYPE" == "darwin"* ]]; then \
	    BREW_LIST=$$(brew ls --formula); \
		if [[ ! $$BREW_LIST == *"mkcert"* ]]; then brew install mkcert; fi; \
		if [[ ! $$BREW_LIST == *"nss"* ]]; then brew install nss; fi; \
		if [[ ! $$BREW_LIST == *"mutagen"* ]]; then brew install mutagen-io/mutagen/mutagen; fi; \
		if [[ ! $$BREW_LIST == *"mutagen-compose"* ]]; then brew install mutagen-io/mutagen/mutagen-compose; fi; \
	fi;
	mkcert -install > /dev/null

## Create required directories
.create-directories:
	echo "$(EMOJI_dividers) Creating required directories"
	[[ -d $$HOME/.dinghy/certs/ ]] || mkdir -p $$HOME/.dinghy/certs/
	[[ -d $$HOME/.phive ]] || mkdir -p $$HOME/.phive
	[[ -d $$HOME/.composer/cache ]] || mkdir -p $$HOME/.composer/cache
	[[ -f $$HOME/.composer/auth.json ]] || echo "{}" > $$HOME/.composer/auth.json

## Create SSL certificates for dinghy and starting project
.create-certificate:
	echo "$(EMOJI_secure) Creating SSL certificates for dinghy http proxy"
	if [[ ! -f $(HOME)/.dinghy/certs/local.co-stack-test.com.key ]]; then mkcert -cert-file $(HOME)/.dinghy/certs/local.co-stack-test.com.crt -key-file $(HOME)/.dinghy/certs/local.co-stack-test.com.key "*.local.co-stack-test.com"; fi;

## Update all images related to this project
.docker-pull:
	echo "$(EMOJI_fishing_pole) Updating all docker images for this project"
	docker compose pull
	docker compose build --pull --parallel

## Start the project
.docker-start:
	echo "$(EMOJI_musical_score) Starting the docker compose project"
	docker compose up -d

## To start an existing project incl. repo cloning
install-project: .install-packages .create-directories destroy .add-hosts-entry .create-certificate .docker-pull .docker-start .mysql-wait .print-online

## Outputs the success message that the project is online
.print-online:
	echo "---------------------"
	echo ""
	echo "The project is online $(EMOJI_thumbsup)"
	echo ""
	echo 'Stop the project with "make stop"'
	echo ""
	echo "---------------------"
	make urls

## Print Project URIs
urls:
	echo "$(EMOJI_telescope) Project URLs:"; \
	echo ''; \
	printf "  %-17s %s\n" "Local Frontend:" "https://$(HOST_LOCAL)/"; \
	printf "  %-17s %s\n" "Local Backend:" "https://$(HOST_LOCAL)/typo3/"; \
	printf "  %-17s %s\n" "Foreign Frontend:" "https://$(HOST_FOREIGN)/"; \
	printf "  %-17s %s\n" "Foreign Backend:" "https://$(HOST_FOREIGN)/typo3/"; \
	printf "  %-17s %s\n" "Local Solr:" "https://$(SOLR_LOCAL)/"; \
	printf "  %-17s %s\n" "Foreign Solr:" "https://$(SOLR_FOREIGN)/"; \
	printf "  %-17s %s\n" "Local Minio:" "https://$(MINIO_LOCAL)/"; \
	printf "  %-17s %s\n" "Foreign Minio:" "https://$(MINIO_FOREIGN)/"; \
	printf "  %-17s %s\n" "Mail:" "https://$(MAIL_HOST)/";

## Create the hosts entry for the custom project URL (non-dinghy convention)
.add-hosts-entry:
	echo "$(EMOJI_monkey) Creating Hosts Entry (if not set yet)"
	SERVICES=$$(command -v getent > /dev/null && echo "getent ahostsv4" || echo "dscacheutil -q host -a name"); \
	if [ ! "$$($$SERVICES web.local.co-stack-test.com | grep 127.0.0.1 > /dev/null; echo $$?)" -eq 0 ]; then sudo bash -c 'echo "127.0.0.1 web.local.co-stack-test.com mail.local.co-stack-test.com" >> /etc/hosts; echo "Entry was added"'; else echo 'Entry already exists'; fi;

## Creates a bash process in the local-php container
login-php:
	echo "$(EMOJI_elephant) Opening a shell in the local-php container"
	docker compose exec -u app php bash

# SETTINGS
TARGET_MAX_CHAR_NUM := 25
MAKEFLAGS += --silent
SHELL := /bin/bash
VERSION := 1.0.0

# COLORS
RED     := $(shell tput -Txterm setaf 1)
GREEN   := $(shell tput -Txterm setaf 2)
YELLOW  := $(shell tput -Txterm setaf 3)
BLUE    := $(shell tput -Txterm setaf 4)
MAGENTA := $(shell tput -Txterm setaf 5)
CYAN    := $(shell tput -Txterm setaf 6)
WHITE   := $(shell tput -Txterm setaf 7)
RESET   := $(shell tput -Txterm sgr0)

# EMOJIS (some are padded right with whitespace for text alignment)
EMOJI_litter := "🚮️"
EMOJI_interrobang := "⁉️ "
EMOJI_floppy_disk := "💾️"
EMOJI_dividers := "🗂️ "
EMOJI_up := "🆙️"
EMOJI_receive := "📥️"
EMOJI_robot := "🤖️"
EMOJI_stop := "🛑️"
EMOJI_package := "📦️"
EMOJI_secure := "🔐️"
EMOJI_explodinghead := "🤯️"
EMOJI_rocket := "🚀️"
EMOJI_plug := "🔌️"
EMOJI_leftright := "↔️ "
EMOJI_upright := "↗️ "
EMOJI_thumbsup := "👍️"
EMOJI_telescope := "🔭️"
EMOJI_monkey := "🐒️"
EMOJI_elephant := "🐘️"
EMOJI_dolphin := "🐬️"
EMOJI_helicopter := "🚁️"
EMOJI_broom := "🧹"
EMOJI_nutandbolt := "🔩"
EMOJI_crystal_ball := "🔮"
EMOJI_triangular_ruler := "📐"
EMOJI_ping_pong := "🏓"
EMOJI_face_with_rolling_eyes := "🙄"
EMOJI_eyes := "👀"
EMOJI_fire := "🔥"
EMOJI_runningshirt := "🎽"
EMOJI_evergreen_tree := "🌲"
EMOJI_luggage := "🧳"
EMOJI_fishing_pole := "🎣"
EMOJI_musical_score := "🎼"
EMOJI_nerd_face := "🤓"
EMOJI_digit_zero := "0️"
EMOJI_digit_one := "1️"
EMOJI_digit_two := "2️"
EMOJI_digit_three := "3️"
EMOJI_digit_four := "4️"
EMOJI_digit_seven := "7️"
EMOJI_pig_nose := "🐽"
EMOJI_customs := "🛃"
EMOJI_hot_face := "🥵"
EMOJI_cold_face := "🥶"
EMOJI_hourglass_not_done := "⏳"
EMOJI_bullseye := "🎯"
EMOJI_trumpet := "🎺"
EMOJI_video_camera := "📹"
