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

rm .env
cp $ENV_FILE .env

if [ $BRANCH == "dev" ]; then
  sed -i '/^\s*root\s*/c\    root /var/www/vide-grenier-dev/public;' .docker/nginx/site.template
  DB_CONTAINER=vide-grenier-dev_db
elif [ $BRANCH == "stage" ]; then
  sed -i '/^\s*root\s*/c\    root /var/www/vide-grenier-stage/public;' .docker/nginx/site.template
  DB_CONTAINER=vide-grenier-stage_db
elif [ $BRANCH == "main" ]; then
  sed -i '/^\s*root\s*/c\    root /var/www/vide-grenier-main/public;' .docker/nginx/site.template
  DB_CONTAINER=vide-grenier-main_db
else
  echo "Unknown branch: $BRANCH"
  sed -i '/^\s*root\s*/c\    root /var/www/vide-grenier/public;' .docker/nginx/site.template
  DB_CONTAINER=vide-grenier_db
fi

# Load the environment variables and run docker-compose
export $(grep -v '^#' $ENV_FILE | xargs)
docker compose down -v
docker compose -f docker-compose.yml up -d --build

sleep 20

docker cp ./www/scripts/restore_db.sh  $DB_CONTAINER:/restore_db.sh
docker cp ./www/scripts/restore_db.sh  $DB_CONTAINER:/dump_db.sh
docker cp ./www/sql/db-vide-grenier.sql $DB_CONTAINER:/init.sql
docker exec -it $DB_CONTAINER sh -c "/restore_db.sh /init.sql"