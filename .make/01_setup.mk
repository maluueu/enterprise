##@ [Repository setup]

#
# =================================================================
# Setup the repository locally
# =================================================================
#
.PHONY: setup
setup: .make/.env .docker/.env tests/.env.testing vendor composer.lock node_modules package-lock.json ## Initializes the repository or checks if everything is still up to date.

#
# =================================================================
# Install composer dependencies
# =================================================================
#
# If the timestamp of composer.json or composer.lock (if present)
# is newer than the vendor folder we need to install composer
# dependencies.
#
vendor: composer.json $(wildcard composer.lock) ## Install composer dependencies (in docker)
	composer install
	touch vendor # Need to update file timestamp so that we dont run this again if composer has no new
				 # dependencies.

#
# =================================================================
# Install node dependencies
# =================================================================
#
# If the timestamp of package.json or package.lock (if present)
# is newer than the vendor folder we need to install node
# dependencies.
#
node_modules: package.json $(wildcard package-lock.json) ## Install npm dependencies in a docker container.
	$(MAYBE_RUN_NODE_IN_DOCKER) npm install
	touch node_modules # Need to update file timestamp so that we dont run this again if node has no new
				 # dependencies.

#
# =================================================================
# Update composer dependencies
# =================================================================
#
# If the composer.json file is modified we need to update
# our composer.lock and composer.lock file and dependencies.
#
composer.lock: composer.json ## Update composer dependencies (in docker)
	composer update
	touch composer.lock # Need to update file timestamp so that we dont run this again if composer has no new
						# dependencies.

#
# =================================================================
# Update node dependencies
# =================================================================
#
# If the package.json file is modified we need to update
# our package.lock file and node dependencies.
#
package-lock.json: package.json ## Update npm dependencies in a docker container.
	$(MAYBE_RUN_NODE_IN_DOCKER) npm update
	touch package-lock.json # Need to update file timestamp so that we dont run this again if node has no new
				 # dependencies.

#
# =================================================================
# Create the .env file for docker
# =================================================================
#
.docker/.env: .docker/.env.dist ## Create a new docker .env file or check if the current one is up to date
	@if [ -f .docker/.env ]; \
		then\
			echo 'The .env.dist docker file has changed. Please check your .env docker file and adjust the modified values (This message will not be displayed again)';\
			touch .docker/.env.dist;\
			exit 1;\
		else\
  			cp .docker/.env.dist .docker/.env;\
			echo 'Created new .env file for make for base';\
	fi

#
# =================================================================
# Create the .env.testing file for codeception
# =================================================================
#
tests/.env.testing: tests/.env.testing.dist ## Create a new codeception .env.testing file or check if the current one is up to date
	@if [ -f tests/.env.testing ]; \
		then\
			echo 'The .env.testing.dist file has changed. Please check your .env.testing file and adjust the modified values (This message will not be displayed again)';\
			touch tests/.env.testing;\
			exit 1;\
		else\
  			cp tests/.env.testing.dist tests/.env.testing;\
			echo 'Created new .env.testing file for codeception';\
	fi

#
# =================================================================
# Create the .env file for make
# =================================================================
#
.make/.env: .make/.env.dist ## Create a new make .env file or check if the current one is up to date
	@if [ -f .make/.env ]; \
		then\
			echo 'The .env.dist make file has changed. Please check your .make/.env file and adjust the modified values (This message will not be displayed again)';\
			touch .make/.env;\
			exit 1;\
		else\
  			cp .make/.env.dist .make/.env;\
			echo 'Created new .env file for make';\
	fi