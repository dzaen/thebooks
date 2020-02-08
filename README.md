# thebooks
# Как поднять и настроить приложение.

### Для того, чтобы развернуть приложение thebooks, понадобятся:
- Ubuntu Server 18.04
- Nginx
- Php
- MySql
- Composer

### Перед установкой необходимых компонентов обновите вашу Ubuntu:

```
sudo apt update
sudo apt upgrade
```

### Установите компоненты.

Nginx:
```
sudo apt install nginx
```

MySql:
```
sudo apt install mysql-server
```

Php:
```
sudo apt install php php-fpm php-mysql php-xml
```

Запустите сервисы nginx, mysql и php-fpm:
```
sudo service nginx start
sudo service mysql start
sudo service php7.4-fpm start
```

Установите зависимости для composer:
```
sudo apt install curl php-cli php-mbstring git unzip
```

Затем установитe composer, следуя инструкциям на сайте https://getcomposer.org/download/.


### Создайте базу данных для приложения thebooks и пользователя этой базы данных:
```
mysql -u root -p
```

```
// Создание базы с именем "thebooks_db"
mysql> CREATE DATABASE thebooks_db;

// Создание пользователя с именем "thebooks" и паролем "thebooks"
mysql> CREATE USER  ‘thebooks’@‘localhost' IDENTIFIED BY ‘thebooks’;

// Предоставление привилегий новому пользователю
mysql> GRANT ALL PRIVILEGES ON thebooks_db.* TO ‘thebooks’@‘localhost';
mysql> FLUSH PRIVILEGES;
```

### Настройте приложение.
Создайте каталог для приложения:
```
sudo mkdir /var/www/thebooks
```

Измените владельца созданной директории, что можно было работать в ней без прав root:
```
// вместо "ubuntu" тут введите ваше имя пользователя
sudo chown ubuntu:ubuntu /var/www/thebooks
```

Теперь перейдите в каталог `/var/www` и клонируйте проект с гитхаба:
```
git clone https://github.com/dzaen/thebooks.git
```

Перейдите в каталог, в котором хранится клонированный проект, и создайте файл `.env`:
```
cd thebooks
vim .env
```
В созданный файл поместите следующее:
```
APP_ENV=PROD
APP_DEBUG=0
DATABASE_URL=mysql://thebooks:thebooks@localhost:3306/thebooks_db
```
> Тут thebooks:thebooks - это имя пользователя mysql и его пароль соответственно. 
> thebooks_db - название базы данных, которую вы создали ранее. 

Сохраните файл.

Установите зависимости проекта (находясь в корневой директории проекта `/var/www/thebooks`):
```
composer install
```

Обновите базу данных таблицами из проекта:
```
php bin/console doctrine:schema:update --force
```
После данных манипуляций в базе данных `thebooks` должна появиться одна таблица `book`.

### Настройте веб-сервер и запустите приложение.
Создайте новый файл конфигурации сервера:
```
sudo vim /etc/nginx/sites-available/thebooks
```
И поместите в него следующее:

```
server {
  listen 8000;
  listen [::]:8000;
  server_name thebooks 127.0.0.1;
  root /var/www/thebooks/public;
  index index.php;
  client_max_body_size 100m;
  location / {
    try_files $uri $uri/ /index.php$is_args$args;
  }
  location ~ \.php {
    try_files $uri /index.php =404;
    fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
    fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    fastcgi_param SCRIPT_NAME $fastcgi_script_name;
    fastcgi_split_path_info ^(.+\.php)(/.+)$;
    fastcgi_index index.php;
    include fastcgi_params;
  }
  location ~ /\.(?:ht|git|svn) {
  deny all;
  }
}
```
Тут вместо ip-адреса 127.0.0.1 можно указать адрес своего сервера. 
Сохраните файл.

Далее выполните команды:
```
sudo ln -s /etc/nginx/sites-available/thebooks /etc/nginx/sites-enabled/
sudo service nginx restart
```
Настройка завершена. Перейдите в браузере по адресу `http://127.0.0.1:8000/`, чтобы запустить приложение. 
