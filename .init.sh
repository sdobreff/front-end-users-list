#!/bin/bash

export REPO_NAME=$(basename $GITPOD_REPO_ROOT)

sudo ln -s $GITPOD_REPO_ROOT /var/www/html/wp-content/plugins
sudo chown gitpod:gitpod /var/www/html/wp-content/plugins/$REPO_NAME

sudo ln -s $GITPOD_REPO_ROOT/gitpod-vscode /var/www/html/.vscode
sudo chown gitpod:gitpod /var/www/html/.vscode
