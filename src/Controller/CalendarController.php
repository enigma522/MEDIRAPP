<?php

namespace App\Controller;

use App\Entity\TodoEvents;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ObjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class CalendarController extends AbstractController
{
    private ObjectManager $manager;
    private ObjectRepository $repo;
    private Security $security;
    public function __construct(private EntityManagerInterface $entityManager, private ManagerRegistry $doctrine, Security $security)
    {
        $this->manager = $this->doctrine->getManager();
        $this->repo = $this->doctrine->getRepository(TodoEvents::class);
        $this->security = $security;
    }
    #[Route('/calendar', name: 'app_calendar')]
    public function index(Request $request): Response
    {
        $request->getSession()->set('id', $this->security->getUser()->getId());
        return $this->render('calendar/index.html.twig', [
            'controller_name' => 'Calendrier',
            'login' => 'Logout',
            'page' => 'Calendrier'
        ]);
    }

    /**
     * @Route("/calendar/getAll/{id}", name="list", methods={"GET"})
     */
    public function list($id): Response
    {
        $connection = $this->entityManager->getConnection();

        
        $query = "SELECT * FROM todo_events where patient_id=:val";
        $params = ['val' => intval($id)];
        $statement = $connection->prepare($query)->execute($params);
        $todos = $statement->fetchAll();

        $data = [];
        foreach ($todos as $todo) {

            $data[] = [
                'id' => $todo['id'],
                'title' => $todo['name'],
                'description' => $todo['description'],
                'dueDate' => $todo['date'],
                'type' => $todo['type'],
                //'patient'=>
                // add more fields as needed
            ];
        }
        return $this->json($data);
    }

    /**
     * @Route("/calendar/find/{id}", name="findById", methods={"GET"})
     */
    public function findBy($id): Response
    {

        $todoEvent = $this->repo->find($id);

        $data = [

            'id' => $todoEvent->getId(),
            'title' => $todoEvent->getName(),
            'description' => $todoEvent->getDescription(),
            'dueDate' => $todoEvent->getDate(),
            'type' => $todoEvent->getType(),
            // add more fields as needed
        ];



        return $this->json($data);
    }

    /**
     * @Route("/calendar/{id}/edit", name="edit_todo",methods={"POST"})
     */
    public function edit(Request $request, int $id): Response
    {
        $todo = $this->repo->find($id);


        if (!$todo) {
            throw $this->createNotFoundException(
                'No todo found for id ' . $id
            );
        }
        $data = $request->toArray();
        $todo->setName($data['title']);
        $todo->setDescription($data['description']);
        $todo->setDate(new \DateTime($data['dueDate']));
        $todo->setType($data['type']);

        $this->manager->flush();

        return $this->json([
            'message' => 'Todo edited successfully',
            'data' => [
                'id' => $todo->getId(),
                'title' => $todo->getName(),
                'description' => $todo->getDescription(),
                'dueDate' => $todo->getDate(),
                'type' => $todo->getType(),
            ],
        ]);
    }
}