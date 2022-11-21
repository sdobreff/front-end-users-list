
# Gitpod docker image for WordPress | https://github.com/luizbills/gitpod-wordpress
# License: MIT (c) 2020 Luiz Paulo "Bills"
# Version: 0.8
FROM sdobreff/gitpod

USER root

RUN apt-get install -y \
	sudo