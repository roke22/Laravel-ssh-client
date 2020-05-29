![](https://raw.githubusercontent.com/roke22/Laravel-ssh-client/master/LaravelSshCient.gif)

## Laravel Ssh Web Client

Cliente Web SSH2 en Laravel que usa websockets para conectar a otros servidores por SSH con el cliente web.

Necesitas tener activadas libssh2 instalado en el servidor y hospedarlo en un servidor linux, puedes comprobar libssh2 con "phpinfo()" mas informacion en http://php.net/manual/en/book.ssh2.php

## INSTALACION

1. Instala libssh2. Si tienes plesk puedes seguir este manual https://support.plesk.com/hc/en-us/articles/213930085-How-to-install-SSH2-extension-for-PHP-
2. Crea el fichero .env desde el fichero .env-example, se ha añaddo la variable WEBSOCKET_URL para configurar la URL del websocket
3. Ejecuta en el directorio raiz de la aplicación composer install --optimize-autoloader --no-dev
4. Ejecuta en el directorio raiz de la aplicación npm install
5. Ejecuta en el directorio raiz de la aplicación php artisan migrate:refresh
6. En el directorio "ssh-server" instala ratchet (http://socketo.me/) con composer, ejecuta en el directorio "ssh-server" el comando "composer install"
7. Arranca el websocket que esta en la carpeta "ssh-server/bin", puedes hacerlo con el comando "php ssh-server/bin/websocket.php 2>&1 >/dev/null &" desde el directorio principal
8.  Ahora puedes cargar la web

NOTA: Puedes cambiar el tamaño de la consola modificando las constantes ROWS y COLS en Servidorsocket.php pero tambien debes modificarlos en el index.html, en caso de ser diferente no dibujara correctamente la informacion en la terminal web.

## LICENCIA

Cliente Web SSH2 esta bajo la licencia MIT, mas informacion en https://opensource.org/licenses/mit-license.php


## Laravel Ssh Web Client

Ssh Web Client that use Laravel and websockets to connect to a SSH server by a webclient.

You need to have libssh2 installed in your server and host the project on a linux server, you can check libssh2 with a "phpinfo()" more info at http://php.net/manual/en/book.ssh2.php

## INSTALLATION

1. Install libssh2. If you have Plesk Panel follow this manual https://support.plesk.com/hc/en-us/articles/213930085-How-to-install-SSH2-extension-for-PHP-
2. Create the .env file from .env-example file, we add the WEBSOCKET_URL variable to define the URL of the websocket
3. From root directory of the app execute composer install --optimize-autoloader --no-dev
4. From root directory of the app execute npm install
5. From root directory of the app execute php artisan migrate:refresh
6. On the "ssh-server" folder install ratchet (http://socketo.me/) with composer, execute on the "ssh-server" folder "composer install" 
7. Run the websocket that is in the "ssh-server/bin" folder, you can do it with the commando "php ssh-server/bin/websocket.php 2>&1 >/dev/null &" from the root folder.
8.  Now you can load the url of the domain.

NOTE: You can change the size of the terminal modifying the constants ROWS and COLS in Servidorsocket.php but you have to do in index.html too. Must be the same size in both files or the web terminal will draw the information in a bad way.

## LICENSE

SSH2 Web Client is under MIT license, more info at https://opensource.org/licenses/mit-license.php