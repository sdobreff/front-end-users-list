
# Gitpod docker image for WordPress | https://github.com/luizbills/gitpod-wordpress
# License: MIT (c) 2020 Luiz Paulo "Bills"
# Version: 0.8
FROM sdobreff/gitpod

USER root

ENV REPO_NAME=$(basename $GITPOD_REPO_ROOT)

RUN ln -s ${$GITPOD_REPO_ROOT} /var/www/html/wp-content/plugins && \
    chown gitpod:gitpod /var/www/html/wp-content/plugins/{$REPO_NAME}
