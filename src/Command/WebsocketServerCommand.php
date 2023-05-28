<?php
    namespace App\Command;
     
use App\Repository\ConversationRepository;
use App\Repository\MessageRepository;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use App\Websocket\MessageHandler;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
     
    class WebsocketServerCommand extends Command
    {
        protected static $defaultName = "run:websocket-server";
        private $userRepository;
        private $entityManager;
        private $conversationRepository;
        private $messageRepository;

        public function __construct(UsersRepository $userRepository,EntityManagerInterface $entityManager,ConversationRepository $conversationRepository,MessageRepository $messageRepository)
        {
            $this->userRepository = $userRepository;
            $this->entityManager = $entityManager;
            $this->conversationRepository = $conversationRepository;
            $this->messageRepository = $messageRepository;
            parent::__construct();
        }
        protected function execute(InputInterface $input, OutputInterface $output)
        {
            $port = 3001;
            $output->writeln("Starting server on port " . $port);
            $server = IoServer::factory(
                new HttpServer(
                    new WsServer(
                        new MessageHandler($this->userRepository,$this->entityManager,$this->conversationRepository,$this->messageRepository)
                    )
                ),
                $port
            );
            $server->run();
            return 0;
        }
    }