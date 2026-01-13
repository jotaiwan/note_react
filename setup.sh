#!/bin/bash

# Notify the user that the script requires sudo privileges
echo "This script will require root privileges (sudo) to install packages and modify system configurations."
echo "Do you want to continue? (y/n)"
read -r CONTINUE

# check and create docker group
getent group docker || sudo groupadd docker

# add user to docker group
sudo usermod -aG docker $USER

# remove composer.lock if exists
if [ -f "composer.lock" ]; then
    echo "Removing existing composer.lock file..."
    rm composer.lock
fi

# If user chooses 'n', exit the script
if [[ "$CONTINUE" != "y" && "$CONTINUE" != "Y" ]]; then
    echo "Exiting script."
    exit 0
fi

# Ensure the script uses sudo privileges throughout
if [[ $EUID -ne 0 ]]; then
    echo "This script requires root privileges. Please enter your sudo password."
    sudo -v
fi

# Check if <host> parameter is provided
if [ -z "$1" ]; then
    echo "Usage: $0 <host>"
    exit 1
fi
# set to environment variable
export SITE="$1"
export USER="$2"

# environment variables setup
PHP_VERSION_REQUIRED="8.2"  # Set required PHP version
HOST="$1"
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
DOCUMENT_ROOT="$SCRIPT_DIR/public"
SITES_AVAILABLE_DIR="/etc/apache2/sites-available"
USER_HOME="$HOME"
# What is USER_BASE_NAME? For example, if the path is /home/matt/cook, "cook" is the base name.
# You can leave it empty, and it will be set up under the user's home directory.
USER_BASE_NAME="/$BASE_NAME"

# Set default paths based on host
NOTE_FOLDER="${USER_HOME}${USER_BASE_NAME}/notes/${HOST}"
NOTE_FILE="${NOTE_FOLDER}/note.txt"

CREDENTIALS_FOLDER="${USER_HOME}${USER_BASE_NAME}/configs/sites/${HOST}"
CREDENTIAL_FILE="${CREDENTIALS_FOLDER}/credential.json"

# 1. Check if apache2 is already installed
if ! command -v apache2 &> /dev/null; then
    echo "Apache2 is not installed. Installing apache2..."
    sudo apt update
    sudo apt install -y apache2
else
    echo "Apache2 is already installed."
fi

# 2. Check if the ondrej/php PPA is already added
if ! grep -q "ondrej/php" /etc/apt/sources.list.d/*; then
    echo "Adding PHP PPA (ondrej/php) to system..."
    sudo add-apt-repository ppa:ondrej/php -y
    sudo apt update
else
    echo "PHP PPA already added."
fi

# 3. Install php-curl if not already installed
if ! php -m | grep -q '^curl$'; then
    echo "php-curl is not installed for PHP $PHP_VERSION_REQUIRED. Installing..."
    sudo apt update
    sudo apt install -y php${PHP_VERSION_REQUIRED}-curl
else
    echo "php-curl is already installed for PHP $PHP_VERSION_REQUIRED."
fi

# 4. Install php-curl if not already installed
if php -m | grep -q '^xml$'; then
    echo "PHP XML extension is already installed."
else
    echo "PHP XML extension not found. Installing..."
    sudo apt update
    sudo apt install -y "php${PHP_VERSION_REQUIRED}-xml"
fi

# 5. Check PHP version
PHP_VERSION_INSTALLED=$(php -v | head -n 1 | awk '{print $2}')
if [[ "$(printf '%s\n' "$PHP_VERSION_REQUIRED" "$PHP_VERSION_INSTALLED" | sort -V | head -n1)" != "$PHP_VERSION_REQUIRED" ]]; then
    echo "Current PHP version is $PHP_VERSION_INSTALLED. Updating to PHP $PHP_VERSION_REQUIRED..."
    sudo apt update
    sudo apt install -y "php$PHP_VERSION_REQUIRED" "php$PHP_VERSION_REQUIRED"-cli "php$PHP_VERSION_REQUIRED"-common "php$PHP_VERSION_REQUIRED"-mysql "php$PHP_VERSION_REQUIRED"-xml "php$PHP_VERSION_REQUIRED"-mbstring "php$PHP_VERSION_REQUIRED"-curl "php$PHP_VERSION_REQUIRED"-zip
    sudo update-alternatives --set php /usr/bin/php$PHP_VERSION_REQUIRED
    sudo update-alternatives --set phpize /usr/bin/phpize$PHP_VERSION_REQUIRED
    sudo update-alternatives --set php-config /usr/bin/php-config$PHP_VERSION_REQUIRED
else
    echo "PHP version $PHP_VERSION_INSTALLED is already installed and meets the requirement."
fi

# 6. Check if Composer is installed
if ! command -v composer &> /dev/null; then
    echo "Composer is not installed. Installing Composer..."
    curl -sS https://getcomposer.org/installer | php
    sudo mv composer.phar /usr/local/bin/composer
else
    echo "Composer is already installed."
fi

# 7. Check and create directories and files
check_and_create_dir() {
    local dir="$1"

    if [ -d "$dir" ]; then
        echo "Directory already exists: $dir"
    else
        echo "Creating directory: $dir"
        mkdir -p "$dir" || {
            echo "Error: Failed to create directory $dir"
            exit 1
        }
    fi

    if [ ! -w "$dir" ]; then
        echo "Error: Directory $dir is not writable. Please check the permissions."
        exit 1
    fi

    # important!! Linux 支援 ACL（Access Control List），可以給特定使用者或群組額外權限：
    # 這個就是 "設定 ACL 給 www-data"
    # 設置 www-data 的讀寫權限，遞迴到所有子資料夾和檔案
    sudo setfacl -R -m u:www-data:rwX "$dir"

    # 設置預設 ACL，方便未來新檔案/資料夾自動繼承權限
    sudo setfacl -d -m u:www-data:rwX "$dir"

}

check_and_create_file() {
    local file="$1"

    if [ -f "$file" ]; then
        echo "File already exists: $file"
    else
        echo "Creating file: $file"
        touch "$file" || {
            echo "Error: Failed to create file $file"
            exit 1
        }
    fi

    if [ ! -w "$file" ]; then
        echo "Error: File $file is not writable. Please check the permissions."
        exit 1
    fi

    # important!! Linux 支援 ACL（Access Control List），可以給特定使用者或群組額外權限：
    # 這個就是 "設定 ACL 給 www-data"
    sudo setfacl -m u:www-data:rw "$file"
}

check_and_create_dir "$NOTE_FOLDER"
check_and_create_file "$NOTE_FILE"
check_and_create_dir "$CREDENTIALS_FOLDER"
check_and_create_file "$CREDENTIAL_FILE"

# 8.Ensure public folder exists
if [ ! -d "$DOCUMENT_ROOT" ]; then
    echo "Creating document root: $DOCUMENT_ROOT"
    mkdir -p "$DOCUMENT_ROOT"
fi

# 9. Ensure vhost config directory exists
if [ ! -d "$SITES_AVAILABLE_DIR" ]; then
    echo "Creating Apache vhost configuration directory: $SITES_AVAILABLE_DIR"
    sudo mkdir -p "$SITES_AVAILABLE_DIR"
fi

# 10. Update local.conf with VirtualHost configuration (non-SSL)
echo "Updating $HOST.local.conf with the custom VirtualHost configuration (non-SSL)"
sudo bash -c "cat > $SITES_AVAILABLE_DIR/$HOST.local.conf << EOF
<VirtualHost *:80>
    ServerName $HOST.local
    DocumentRoot $DOCUMENT_ROOT

    <Directory $DOCUMENT_ROOT>
        AllowOverride All
        Require all granted
        DirectoryIndex index.php index.html
        <IfModule mod_rewrite.c>
            RewriteEngine On
            RewriteCond %{REQUEST_FILENAME} !-f
            RewriteCond %{REQUEST_FILENAME} !-d
            RewriteRule ^ /index.php [QSA,L]
        </IfModule>
    </Directory>

    ErrorLog \${APACHE_LOG_DIR}/$HOST.local-error.log
    CustomLog \${APACHE_LOG_DIR}/$HOST.local-access.log combined
</VirtualHost>
EOF"

# 11. Enable mod_rewrite for Apache (necessary for .htaccess)
echo "Enabling mod_rewrite for Apache..."
sudo a2enmod rewrite

# 12. Disable SSL Module (not needed for local development)
echo "Disabling SSL module..."
sudo a2dismod ssl

# 13. Remove SSL-related configuration files
sudo rm -f /etc/apache2/sites-available/default-ssl.conf
sudo rm -f /etc/apache2/sites-enabled/default-ssl.conf

# 14. Enable the new non-SSL vhost configuration
echo "Enabling $HOST.local.conf vhost..."
sudo a2ensite $HOST.local.conf

# 15. Add $HOST.local to /etc/hosts if not already present
grep -q "$HOST.local" /etc/hosts || echo "127.0.0.1 $HOST.local" | sudo tee -a /etc/hosts

# 16. Restart Apache to apply changes
echo "Checking Apache configuration for errors before restarting..."
sudo apache2ctl configtest

if [ $? -eq 0 ]; then
    echo "Apache configuration is OK. Restarting Apache..."
    sudo systemctl restart apache2
else
    echo "Apache configuration has errors. Fix the issues and try again."
    exit 1
fi

# 17. Composer install if needed
if [ ! -d "$DOCUMENT_ROOT/vendor" ]; then
    echo "Vendor directory does not exist. Running composer install..."
    cd "$DOCUMENT_ROOT" && composer install
else
    echo "Vendor directory already exists. Skipping composer install."
fi

# 18. Fix permissions for Apache access (prevents 403)
echo "Fixing permissions for Apache access..."
CURRENT="$DOCUMENT_ROOT"
while [ "$CURRENT" != "/" ]; do
    sudo chmod o+x "$CURRENT"
    CURRENT=$(dirname "$CURRENT")
done

# 19. Keep user as owner, Apache as group
sudo chown -R "$USER":www-data "$DOCUMENT_ROOT"
sudo chmod -R 755 "$DOCUMENT_ROOT"

# 20. Set proper permissions for index.php if exists
if [ -f "$DOCUMENT_ROOT/index.php" ]; then
    sudo chmod 755 "$DOCUMENT_ROOT/index.php"
    sudo chown "$USER":www-data "$DOCUMENT_ROOT/index.php"
fi

# 21. Restart Apache to apply all changes
echo "Restarting Apache..."
sudo systemctl restart apache2

# 22. It's optional, set data folder permission (for stocking result)
#     TODO: change <username> to your login user name
sudo setfacl -R -m u:"$USER":rwX data
sudo setfacl -R -m d:u:"$USER":rwX data   # 默认 ACL

#23. 安裝 Symfony CLI
curl -sS https://get.symfony.com/cli/installer | bash
export PATH="$HOME/.symfony/bin:$PATH"


echo "Setup complete! Visit http://$HOST.local in your browser."
