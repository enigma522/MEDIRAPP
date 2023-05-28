<?php

namespace App\Controller;


use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Request;



class MesPatientsController extends AbstractController
{

    public function __construct(private UsersRepository $usersRepository, private EntityManagerInterface $entityManager)
    {
    }

    #[Route('/mespatients', name: 'app_mespat')]
    public function index(Request $request, Security $security): Response
    {
        $session = $request->getSession();
        $session->set('id',$security->getUser()->getId());

        return $this->render('mes_patients/index.html.twig', [
            'controller_name' => 'MesPatientsController',
            'page' => 'Mes patients',
            'login' => 'Logout'
        ]);
    }
    #[Route('/mespatients/{id}', name: 'app_mes_patients', methods:'GET')]
    public function index1($id): Response
    {
        $connection = $this->entityManager->getConnection();
        $query = "SELECT u.* FROM  users u LEFT JOIN users_users uu on uu.users_source=u.id where uu.users_target = :id;";
        $params = ['id' => intval($id)];
        $statement = $connection->prepare($query)->execute($params);
        $conversation = $statement->fetchAll();

        return new JsonResponse($conversation);
    }

   
}
