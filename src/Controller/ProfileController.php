<?php

namespace App\Controller;

use App\Form\UsersType;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ObjectRepository;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class ProfileController extends AbstractController
{
    
    public function __construct(private UsersRepository $usersRepository)
    {
        
        
    }

    #[Route('/profilem', name: 'app_medecin')]
    public function index(Security $security): Response
    {
        $id = $security->getUser()->getId();
        $user = $this->usersRepository->findOneBy(['id'=> $id]);

        $form=$this->createForm(UsersType::class,$user);
        
        if($form->isSubmitted()&& $form->isValid())
        {
            $this->usersRepository->save($user,true);

            $this->addFlash('succes',$user->getFirstname()."a été edite avec succès");
        }
        return $this->render('profile/index.html.twig', [
            'controller_name' => 'MedecinController',
            'form'=>$form->createView(),
            'login'=>'logout',
            'page'=>'Profile'
        ]);
    }
}
