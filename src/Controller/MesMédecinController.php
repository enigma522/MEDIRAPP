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

class MesMédecinController extends AbstractController
{
    public function __construct(private UsersRepository $usersRepository,private EntityManagerInterface $entityManager)
    {

    }


    #[Route('mesMedecins', name: 'app_mesMed')]
    public function index(Request $request, Security $security): Response


    {
        $session = $request->getSession();
        $session->set('id',$security->getUser()->getId());


        return $this->render('mes_médecin/index.html.twig', [
            'controller_name' => 'MesMédecinController',
            'login' => "Logout",
            'page' => 'Mes médecins'
        ]);
    }
  


    #[Route('mesMedecins/{id}', name: 'app_mes_m_decin')]
    public function index1($id): Response
    {
        $connection = $this->entityManager->getConnection();
        $query = "SELECT u.* FROM  users u LEFT JOIN users_users uu on uu.users_target=u.id where uu.users_source = :id;";
        $params = ['id' => intval($id)];
        $statement = $connection->prepare($query)->execute($params);
        $conversation = $statement->fetchAll();

        return new JsonResponse($conversation);
        
    }



    #[Route('add_patient/{email}', name: 'app_add_patient')]
    public function add_patient(Security $security,$email): Response{


        $thisuser = $security->getUser();
        $patient = $this->usersRepository->findOneBy(['email' => $email]);

        if ($patient != null){
            $thisuser->addPatient($patient);
            $this->usersRepository->save($thisuser,true);
            return new JsonResponse("ok");
        }else{
            return new JsonResponse("can't find patient");
        }

        
        

    }


    

  
}
