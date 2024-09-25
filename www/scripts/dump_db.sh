
# Variables de connexion MySQL
DB_HOST="localhost"
DB_USER="vide-grenier"
DB_PASS="vide-grenier"
DB_NAME="db-vide-grenier"
BACKUP_DIR="./backups"  # Dossier où les backups seront stockés
DATE=$(date +"%Y%m%d_%H%M%S")
DUMP_FILE="$BACKUP_DIR/${DB_NAME}_backup_$DATE.sql"

# Créer le dossier de backup s'il n'existe pas
mkdir -p $BACKUP_DIR

# Exécuter la commande mysqldump
mysqldump -h $DB_HOST -u $DB_USER -p$DB_PASS $DB_NAME > $DUMP_FILE

if [ $? -eq 0 ]; then
    echo "Sauvegarde de la base de données réussie : $DUMP_FILE"
else
    echo "Échec de la sauvegarde de la base de données."
fi
