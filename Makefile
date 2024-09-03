# Container image
IMAGE_REPO ?= ghcr.io/phpcfdi/cfditopdf
IMAGE_TAG ?= latest

.DEFAULT_GOAL := help
.PHONY: build run help

build: ## Build the container image
	docker build -t $(IMAGE_REPO):$(IMAGE_TAG) .

run: ## Run container passing `cmd` make argument as the containr command
	@docker run --rm \
	  --volume $(shell pwd)/data:/data \
	  --user $(shell id -u ${USER}):$(shell id -g ${USER}) \
	  $(IMAGE_REPO):$(IMAGE_TAG) \
	  $(cmd)

help: ## Print this help.
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-20s\033[0m %s\n", $$1, $$2}'
