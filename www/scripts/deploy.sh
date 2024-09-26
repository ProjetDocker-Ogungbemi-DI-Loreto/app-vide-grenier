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
git reset --hard
git checkout $BRANCH
git pull origin $BRANCH
chmod +x ./www/scripts/*

if [ $BRANCH == "dev" ]; then
  sed -i '/^\s*root\s*/c\    root /var/www/vide-grenier-dev/public;' .docker/nginx/site.template
elif [ $BRANCH == "stage" ]; then
  sed -i '/^\s*root\s*/c\    root /var/www/vide-grenier-stage/public;' .docker/nginx/site.template
elif [ $BRANCH == "main" ]; then
  sed -i '/^\s*root\s*/c\    root /var/www/vide-grenier-main/public;' .docker/nginx/site.template
else
  echo "Unknown branch: $BRANCH"
  sed -i '/^\s*root\s*/c\    root /var/www/vide-grenier/public;' .docker/nginx/site.template
fi

# Load the environment variables and run docker-compose
export $(grep -v '^#' $ENV_FILE | xargs)
docker compose down -v
docker compose -f docker-compose.yml up -d --build
