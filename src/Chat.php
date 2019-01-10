<?php
namespace MyApp;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
require("../db/user_trans.php");
require("../db/chatroom.php");

class Chat implements MessageComponentInterface {
    protected $clients;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
        echo "server started\n";
    }

    public function onOpen(ConnectionInterface $conn) {
        // Store the new connection to send messages to later
        $this->clients->attach($conn);

        echo "New connection! ({$conn->resourceId})\n";
        //$conn->send('Hello ' . $conn->Session->get('name'));
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $numRecv = count($this->clients) - 1;
        echo sprintf('Connection %d sending message "%s" to %d other connection%s' . "\n"
            , $from->resourceId, $msg, $numRecv, $numRecv == 1 ? '' : 's');
        
        $msg_cred = json_decode($msg, true);

        if ($msg_cred['meta'] == 'msg') {
            $objchat = new \chatroom;
            $objchat->setSenderid($msg_cred['userid']);
            $objchat->setMsg($msg_cred['usermsg']);
            $objchat->setDatetime(date("Y-m-d h:i:s"));

        if ($objchat->savechats()) {
            $objuser = new \Usertrans;
            $userdata = $objuser->getuserbyid($msg_cred['userid']);
            $data['userid'] = $msg_cred['userid'];
            $data['from'] = $userdata['name'];
            $data['msg'] = $msg_cred['usermsg'];
            $data['dt'] = date("Y-m-d h:i:s");
            $data['meta'] = $msg_cred['meta'];
         }

        } elseif ($msg_cred['meta'] == 'offline') {
            
            $data['id'] = $msg_cred['id'];
            $data['dt'] = date("Y-m-d h:i:s");
            $data['meta'] = $msg_cred['meta'];

        } elseif ($msg_cred['meta'] == 'online') {
            
            $data['id'] = $msg_cred['id'];
            $data['dt'] = date("Y-m-d h:i:s");
            $data['meta'] = $msg_cred['meta'];
        }
        

        foreach ($this->clients as $client) {
          
          if ($msg_cred['meta'] == 'msg') {
              if ($from == $client) {
                // The sender is not the receiver, send to each client connected
                $data['from'] = "Me";
                $client->send(json_encode($data));
            } else {
                $data['from'] = $userdata['name'];
                $client->send(json_encode($data));
            }
          } else {
            if ($msg_cred['meta'] == 'online' || $msg_cred['meta'] == 'offline') {

              if ($from !== $client) {
                // The sender is not the receiver, send to each client connected
                $client->send(json_encode($data));

          }
            }
            
        }
    }
}

    public function onClose(ConnectionInterface $conn) {
        // The connection is closed, remove it, as we can no longer send it messages
        $this->clients->detach($conn);

        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }
}