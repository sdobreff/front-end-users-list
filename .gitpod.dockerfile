
# Gitpod docker image for WordPress | https://github.com/luizbills/gitpod-wordpress
# License: MIT (c) 2020 Luiz Paulo "Bills"
# Version: 0.8
FROM sdobreff/gitpod

USER gitpod

COPY .init.sh /home/github/.bashrc
