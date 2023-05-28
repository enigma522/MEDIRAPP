<?php

namespace App\Websocket;


use App\Entity\Message;
use App\Repository\ConversationRepository;
use App\Repository\MessageRepository;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use GuzzleHttp\Psr7\Request;
use phpDocumentor\Reflection\Types\Array_;
use App\Entity\Conversation;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use SplObjectStorage;
use Symfony\Bridge\Doctrine\ManagerRegistry;



class MessageHandler implements MessageComponentInterface
{

    private $clients;


    public function __construct(
        private UsersRepository $userRepository,
        private EntityManagerInterface $entityManager,
        private ConversationRepository $conversationRepository,
        private MessageRepository $messageRepository
    ) {
        $this->clients = new SplObjectStorage();
        //sort a table


    }



    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);
        echo "New connection! ({$conn->resourceId})\n";
        $querystring = $conn->httpRequest->getUri()->getQuery();
        parse_str($querystring, $queryarray);

        $user = $this->userRepository->findOneBy(['user_token' => $queryarray['token']]);
        $user->setUserConnectionId($conn->resourceId);
        $this->userRepository->save($user, true);
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        echo "Message from {$from->resourceId}: {$msg}\n";
        $message = json_decode($msg, true);

        $user = $this->userRepository->findOneBy(['id' => $message['to']]);
        $receiver_userid = $user->getUserConnectionId();
        $newmessage = new Message();

        $newmessage->setUser($this->userRepository->findOneBy(['id' => $message['from']]));
        $newmessage->setContent($message['message']);
        $connection = $this->entityManager->getConnection();
        $query = "SELECT c1.conversation_id FROM conversation_users c1 LEFT JOIN conversation_users c2 ON c1.conversation_id = c2.conversation_id WHERE c1.users_id =:val1 AND c2.users_id =:val2 LIMIT 1;";
        $params = [
            'val1' => $message['from'],
            'val2' => $message['to']
        ];
        $statement = $connection->prepare($query)->execute($params);
        $conversation = $statement->fetchAll();
        if (isset($conversation[0]['conversation_id'])) {
            $conv = $this->conversationRepository->findOneBy(['id' => $conversation[0]['conversation_id']]);
        } else {
            $conv = new Conversation();
            $user->addConversation($conv);
            $fromuser = $this->userRepository->findOneBy(['id' => $message['from']]);
            $fromuser->addConversation($conv);
        }
        $newmessage->setConversation($conv);

        $this->messageRepository->save($newmessage, true);

        foreach ($this->clients as $connection) {

            if ($connection === $from) {
                continue;
            } else {
                if ($connection->resourceId == $receiver_userid) {
                    $connection->send($msg);
                }
            }
        }
    }


    public function onClose(ConnectionInterface $conn)
    {

        $this->clients->detach($conn);
        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, Exception $e)
    {
    }
}
