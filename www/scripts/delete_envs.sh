#!/bin/bash

# Usage: remove-environment.sh <env (dev, preprod, prod)>

# Vérifie si un argument d'environnement est fourni
if [ -z "$1" ]; then
    echo "Usage: remove-environment.sh <env (dev, preprod, prod)>"
    exit 1
fi

ENV=$1

# Détermine le nom des fichiers et services associés en fonction de l'environnement
if [ "$ENV" == "dev" ]; then
    ENV_FILE=".env.dev"
    CONTAINER_PREFIX="vide-grenier-dev"
elif [ "$ENV" == "preprod" ]; then
    ENV_FILE=".env.stage"
    CONTAINER_PREFIX="vide-grenier-stage"
elif [ "$ENV" == "prod" ]; then
    ENV_FILE=".env.prod"
    CONTAINER_PREFIX="vide-grenier-main"
else
    echo "Environnement inconnu : $ENV"
    exit 1
fi

echo "Suppression de l'environnement $ENV..."

# 1. Arrête et supprime les conteneurs Docker liés à cet environnement
echo "Arrêt et suppression des services Docker..."
docker compose -f docker-compose.yml down -v

# 2. Supprime les fichiers de configuration spécifiques à l'environnement
if [ -f "$ENV_FILE" ]; then
    echo "Suppression du fichier d'environnement $ENV_FILE..."
    rm -f "$ENV_FILE"
else
    echo "Fichier d'environnement $ENV_FILE non trouvé, aucun fichier à supprimer."
fi


# 3. Supprime les volumes Docker spécifiques
echo "Suppression des volumes Docker associés..."
docker volume rm $(docker volume ls -q --filter "name=${CONTAINER_PREFIX}")

echo "Environnement $ENV supprimé avec succès."
