# Makefile readme (ru): <http://linux.yaroslavl.ru/docs/prog/gnu_make_3-79_russian_manual.html>

# cross-platform helper
OS := $(if $(ComSpec),Windows_NT,Unix)
define wl
$(if $(filter Windows_NT,$(OS)),$1,$2)
endef

# variables
SLASH :=$(call wl,\,/)

# commands
RM := $(call wl,del /Q /F /S,rm -rf)
COPY := $(call wl,copy,cp)
MKDIR := $(call wl,mkdir,mkdir -p)

# default rule
all: update

# rules
.PHONY: update
update:
	$(MAKE) cache
	php artisan migrate --force

.PHONY: init
init:
	php artisan key:generate
	$(MAKE) cache
	php artisan migrate --seed --force

.PHONY: reinit
reinit:
	$(RM) storage/files/*
	php artisan key:generate
	$(MAKE) cache
	php artisan migrate:refresh --seed --force

.PHONY: cache
cache:
	php artisan config:cache
