# Variables de connexion MySQL
DB_HOST="localhost"
DB_USER="vide-grenier"
DB_PASS="vide-grenier"
DB_NAME="db-vide-grenier"
BACKUP_FILE=$1  # Le fichier SQL de backup à restaurer

# Vérification si le fichier est fourni
if [ -z "$BACKUP_FILE" ]; then
    echo "Veuillez spécifier un fichier SQL à restaurer."
    echo "Usage: ./restore_db.sh /path/to/backup.sql"
    exit 1
fi

# Exécuter la commande mysql pour restaurer la base
mysql -h $DB_HOST -u $DB_USER -p$DB_PASS $DB_NAME < $BACKUP_FILE

if [ $? -eq 0 ]; then
    echo "Restauration de la base de données réussie depuis $BACKUP_FILE"
else
    echo "Échec de la restauration de la base de données."
fi
