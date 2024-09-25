#!/bin/bash

# Check for the environment argument (dev, preprod, prod)
if [ -z "$1" ]; then
  echo "Usage: deploy.sh <env (dev, preprod, prod)>"
  exit 1
fi

ENV=$1

# Checkout the correct git branch
if [ "$ENV" == "dev" ]; then
  BRANCH="dev"
  ENV_FILE=".env.dev"
elif [ "$ENV" == "preprod" ]; then
  BRANCH="stage"
  ENV_FILE=".env.stage"
elif [ "$ENV" == "prod" ]; then
  BRANCH="main"
  ENV_FILE=".env.prod"
else
  echo "Unknown environment: $ENV"
  exit 1
fi

# Checkout on the right brancg and pull the latest changes from the repository
git checkout $BRANCH
git pull origin $BRANCH

sed -i '/^\s*root\s*/c\    root /var/www/vide-grenier-$BRANCH/public;' .docker/nginx/site.template

# Load the environment variables and run docker-compose
export $(grep -v '^#' $ENV_FILE | xargs) && docker-compose up -d --build
