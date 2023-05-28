<?php

namespace App\Controller;

use App\Entity\Conversation;
use App\Entity\Message;
use App\Repository\ConversationRepository;
use App\Repository\MessageRepository;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
//use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Core\Security;

use Doctrine\DBAL\Connection;



class ChatController extends AbstractController
{

    public function __construct(private EntityManagerInterface $entityManager,private MessageRepository $messageRepository,private ConversationRepository $conversationRepository)

    {
    }


    private $emailUser;
    private $id;
    #[Route('/{id}', name: 'user_app_chat', requirements: ['id' => '\d+'])]
    public function index(Request $request, Security $security, $id): Response
    {
        $users = [];
        if ($security->getUser() != null) {
            $session = $request->getSession();

            $users = $session->get('users');
        }
        $connection = $this->entityManager->getConnection();
        $query = "SELECT c1.conversation_id FROM conversation_users c1 LEFT JOIN conversation_users c2 ON c1.conversation_id = c2.conversation_id WHERE c1.users_id =:val1 AND c2.users_id =:val2 LIMIT 1;";
        $params = [
            'val1' => intval($id),
            'val2' => $security->getUser()->getId()
        ];
        $statement = $connection->prepare($query)->execute($params);
        $conversation = $statement->fetchAll();

        $messages = $this->messageRepository->findByExampleconv($conversation);

        $data = [];
        foreach ($messages as $msg) {
            $data[] = [
                'content' => $msg->getContent(),
                'id' => $msg->getUser()->getId(),
            ];
        }

        return new JsonResponse($data);
    }


    #[Route('/chat', name: 'app_chat')]
    public function chathome(Security $security, Request $request): Response
    {
        $connection = $this->entityManager->getConnection();
        $id = $security->getUser()->getId();


        $query = "SELECT conversation_users.conversation_id FROM conversation_users WHERE users_id = :val;";
        $params = ['val' => $id];

        $statement = $connection->prepare($query)->execute($params);
        $conversations = $statement->fetchAll();

        $query = "SELECT users.firstname,users.lastname,users.id from users where id in (select conversation_users.users_id FROM conversation_users WHERE conversation_id in (:conversations) and users_id != :id);";
        $conn = [];
        foreach ($conversations as $value) {
            $conn[] = $value['conversation_id'];
        }

        $params = [
            'conversations' => $conn,
            'id' => $id
        ];


        $types = [
            'conversations' => Connection::PARAM_INT_ARRAY,
            'id' => \PDO::PARAM_INT
        ];

        $statement = $connection->executeQuery($query, $params, $types);
        $users = $statement->fetchAll();

        if ($security->getUser() != null) {
            $session = $request->getSession();
            $session->set('users', $users);
        }



        return $this->render('chat/index.html.twig', [
            'controller_name' => 'HomepageController',
            'login' => 'Logout',
            'page' => 'Chat',
            'friends' => $users,

        ]);
    }


    #[Route('/chatwith/{id}', name: 'user_app_chat_with',requirements: ['id' => '\d+'])]
    public function chatwith(Request $request,Security $security,$id): Response
    {
        
        
        $connection = $this->entityManager->getConnection();
        $query = "SELECT c1.conversation_id FROM conversation_users c1 LEFT JOIN conversation_users c2 ON c1.conversation_id = c2.conversation_id WHERE c1.users_id =:val1 AND c2.users_id =:val2 LIMIT 1;";
        $params = ['val1' => intval($id),
                    'val2' => $security->getUser()->getId()];
        $statement = $connection->prepare($query)->execute($params);
        $conversation = $statement->fetchAll();
        
        if ($conversation == null){
            $newconversation = new Conversation();
            $this->conversationRepository->save($newconversation,true);

            $query = "INSERT INTO conversation_users (conversation_id,users_id) VALUES (:val1,:val2);";
            $params = ['val1' => intval($newconversation->getId()),
                        'val2' => $security->getUser()->getId()];
            $statement = $connection->prepare($query)->execute($params);
            $query = "INSERT INTO conversation_users (conversation_id,users_id) VALUES (:val1,:val2);";
            $params = ['val1' => intval($newconversation->getId()),
                        'val2' => intval($id)];
            $statement = $connection->prepare($query)->execute($params);
        }
        
        return $this->redirectToRoute('app_chat');
        
    }

    


    


    

}