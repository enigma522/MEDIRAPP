<?php

namespace App\Controller;

use App\Form\TodoType;
use App\Repository\TodoEventsRepository;
use App\Repository\TodoRepository;
use App\Repository\UsersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\TodoEvents;
use App\Entity\Todo;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Request;
use DateInterval;
use DatePeriod;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Twilio\Rest\Client;

class OchrebdwekController extends AbstractController
{

    public function __construct(private UsersRepository $usersRepository, private TodoEventsRepository $todoEventsRepository, private TodoRepository $todoRepository)
    {
    }
    #[Route('/ochrobdwek', name: 'app_ochrob_dwek')]
    public function index(Request $request, Security $security): Response
    {


        $event = new Todo();
        $form = $this->createForm(
            TodoType::class,
            $event
        );
        $form->handleRequest($request);


        if ($form->isSubmitted()) {
            $phoneNum = $this->usersRepository->findOneBy(['email' => $event->getPatientemail()])->getPhoneNumber();

            $sid = 0; // Your Account SID from https://console.twilio.com
            $token = 0; // Your Auth Token from https://console.twilio.com
            $client = new Client($sid, $token);
            $receiver = "+" . $phoneNum . "";
            $dateDebut = $event->getDateDebut()->format('Y-m-d');
            // Use the Client to make requests to the Twilio REST API
            $message = $client->messages->create(
                // The number you'd like to send the message to
                $receiver,
                [
                    // A Twilio phone number you purchased at https://console.twilio.com
                    'from' => '+15077283386',
                    // The body of the text message you'd like to send
                    // 'body' => "Hi " . $name . " your otp code is: " . $otp
                    'body' => "Dr " . $security->getUser()->getFirstName() . " " .
                        $security->getUser()->getLastName() . " vous a ajouté un nouveau evènement qui commence le  " .
                        $dateDebut . ".\n" . "MEDIRAPP"
                ]
            );
            if (isset($message)) {
            } else {
                echo "Code failed";
            }

            $this->todoRepository->save($event, true);
            $medecin = $this->usersRepository->findOneBy(['email' => $security->getUser()->getUserIdentifier()]);

            $email = $event->getPatientEmail();
            $patient = $this->usersRepository->findOneBy(['email' => $email]);

            $startDate = $event->getDateDebut();
            $endDate = $event->getDateFin();
            $endDate->modify('+1 day');

            // Loop through each day within the date range
            $interval = new DateInterval('P1D');
            $dateRange = new DatePeriod($startDate, $interval, $endDate);
            foreach ($dateRange as $date) {
                // Create a new sub-object for the current day
                $subObj = [
                    'name' => $event->getName(),
                    'date' => $date->format('Y-m-d'),
                    'description' => $event->getDescription(),
                    'type' => 'event',
                    'patient_email' => $event->getPatientEmail(),
                ];
                $todo = new TodoEvents();
                $date = new DateTime($subObj['date']);
                $todo->setName($subObj['name']);
                $todo->setDate($date);
                $todo->setDescription($subObj['description']);
                $todo->setType($subObj['type']);
                // $todo->setPatientEmail($subObj['patient_email']);
                $todo->setPatient($patient);
                $this->todoEventsRepository->save($todo, true);
            }

            $this->addFlash('success', $event->getName() . "a été ajouté avec succès");
        } else {
            $this->addFlash('error', "erreur");
        }
        return $this->render('ochrebdwek/index.html.twig', [
            'controller_name' => 'OchrobDwekController',
            'form' => $form->createView(),
            'login' => 'logout',
            'page' => 'Ochrob Dwek'
        ]);
    }
}
