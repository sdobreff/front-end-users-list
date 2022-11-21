
# Gitpod docker image for WordPress | https://github.com/luizbills/gitpod-wordpress
# License: MIT (c) 2020 Luiz Paulo "Bills"
# Version: 0.8
FROM sdobreff/gitpod

RUN cat $HOME/.init >> $HOME/.bashrc && \
    . $HOME/.bashrc
