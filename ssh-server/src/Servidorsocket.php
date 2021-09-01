<?php
/*
** PHP SSH2 Web Client
** Autor: Jose Joaquin Anton
** Email: roke@roke.es
**
** License: The MIT License -> https://opensource.org/licenses/mit-license.php
**  Copyright (c) 2018 Jose Joaquin Anton
**
**  Se concede permiso, libre de cargos, a cualquier persona que obtenga una copia de este software y de los archivos de documentación asociados
**  (el "Software"), para utilizar el Software sin restricción, incluyendo sin limitación los derechos a usar, copiar, modificar, fusionar, publicar,
**  distribuir, sublicenciar, y/o vender copias del Software, y a permitir a las personas a las que se les proporcione el Software a hacer lo mismo,
**  sujeto a las siguientes condiciones:
**
**  El aviso de copyright anterior y este aviso de permiso se incluirán en todas las copias o partes sustanciales del Software.
**
**  EL SOFTWARE SE PROPORCIONA "TAL CUAL", SIN GARANTÍA DE NINGÚN TIPO, EXPRESA O IMPLÍCITA, INCLUYENDO PERO NO LIMITADA A GARANTÍAS DE
**  COMERCIALIZACIÓN, IDONEIDAD PARA UN PROPÓSITO PARTICULAR Y NO INFRACCIÓN. EN NINGÚN CASO LOS AUTORES O PROPIETARIOS DE LOS DERECHOS DE
**  AUTOR SERÁN RESPONSABLES DE NINGUNA RECLAMACIÓN, DAÑOS U OTRAS RESPONSABILIDADES, YA SEA EN UNA ACCIÓN DE CONTRATO, AGRAVIO O CUALQUIER OTRO
**  MOTIVO, DERIVADAS DE, FUERA DE O EN CONEXIÓN CON EL SOFTWARE O SU USO U OTRO TIPO DE ACCIONES EN EL SOFTWARE.
*/

namespace MyApp;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class Servidorsocket implements MessageComponentInterface
{
    protected $clients;
    protected $connection = [];
    protected $shell = [];
    protected $conectado = [];
    protected $idConexion = [];

    const COLS = 80;
    const ROWS = 24;

    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn)
    {
        // Store the new connection to send messages to later
        $this->clients->attach($conn);
        $this->connection[$conn->resourceId] = null;
        $this->shell[$conn->resourceId] = null;
        $this->conectado[$conn->resourceId] = null;
        $this->idConexion[$conn->resourceId] = null;
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        $data = json_decode($msg, true);
        switch (key($data)) {
        case 'data':
            fwrite($this->shell[$from->resourceId], $data['data']['data']);
            usleep(800);
            while($line = fgets($this->shell[$from->resourceId])) {
                $from->send(mb_convert_encoding($line, "UTF-8"));
                $this->resend($line, $from);
            }
            break;
        case 'auth':
            $from->send(mb_convert_encoding("Connecting to ".$data['auth']['server']."....\r\n", "UTF-8"));
            if ($this->connectSSH($data['auth']['idconnection'], $data['auth']['server'], $data['auth']['port'], $data['auth']['user'], $data['auth']['password'], $from)) {
                $from->send(mb_convert_encoding("Connected....", "UTF-8"));
                while($line = fgets($this->shell[$from->resourceId])) {
                    $from->send(mb_convert_encoding($line, "UTF-8"));
                }
            }else{
                $from->send(mb_convert_encoding("Error, can not connect to the server. Check the credentials\r\n", "UTF-8"));
                $from->close();
            }
            break;
        case 'sharessh':
            //Only root user connection read the connection
            $this->conectado[$from->resourceId]=false;
            $this->idConexion[$from->resourceId]=$data['sharessh']['idconnection'];
            $from->send("You are now viewing the ssh connection id ".$data['sharessh']['idconnection']."\r\n", "UTF-8");
            break;
        default:
            if ($this->conectado[$from->resourceId]) {
                while($line = fgets($this->shell[$from->resourceId])) {
                      $from->send(mb_convert_encoding($line, "UTF-8"));
                      $this->resend($line, $from);
                }
            }
            break;
        }
    }

    protected function resend($line, $from)
    {
        foreach ($this->clients as $client) {
            if ($client->resourceId == $from->resourceId) {
                continue;
            }

            if ($this->idConexion[$client->resourceId] == $this->idConexion[$from->resourceId]) {
                $client->send(mb_convert_encoding($line, "UTF-8"));
            }
        }
    }

    public function connectSSH($idConnection, $server, $port, $user, $password, $from)
    {
        $this->connection[$from->resourceId] = ssh2_connect($server, $port);

        if ($this->connection[$from->resourceId] === false) {
            $from->send("Error during connection to ".$server." at port ".$port."\r\n", "UTF-8");
            return false;
        }

        if (ssh2_auth_password($this->connection[$from->resourceId], $user, $password)) {
            $from->send("Authentication Successful for server ".$server." at port ".$port."!\r\n", "UTF-8");
            $from->send("Your id connection is ".$idConnection."\r\n", "UTF-8");
            $this->shell[$from->resourceId]=ssh2_shell($this->connection[$from->resourceId], 'xterm', null, self::COLS, self::ROWS, SSH2_TERM_UNIT_CHARS);
            sleep(1);
            $this->conectado[$from->resourceId]=true;
            $this->idConexion[$from->resourceId]=$idConnection;
            return true;
        } else {
            $from->send("Wrong username ".$user." and password for server ".$server." at port ".$port."\r\n", "UTF-8");
            return false;
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        // The connection is closed, remove it, as we can no longer send it messages
        $this->conectado[$conn->resourceId]=false;
        $this->clients->detach($conn);

        // Gracefully closes terminal, if it exists
        if(isset($this->shell[$conn->resourceId]) && is_resource($this->shell[$conn->resourceId])) {
            fclose($this->shell[$conn->resourceId]);
            $this->shell[$conn->resourceId] = null;
        }
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        $conn->close();
    }
}

?>
