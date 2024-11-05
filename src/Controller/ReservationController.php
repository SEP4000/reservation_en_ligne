<?php

namespace App\Controller;

use App\Entity\Reservations;
use App\Entity\User;
use App\Form\UserReservationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReservationController extends AbstractController
{

    #[Route('/reservation', name: 'app_reservation')]
    public function reservation(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Crée un nouvel utilisateur 
        $user = new User();

        // Crée le formulaire
        $form = $this->createForm(UserReservationFormType::class, $user);

        // Traite la requête
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

        $date = $form->get('date')->getData();
        $heureString = $form->get('heure')->getData();
        $heure = \DateTime::createFromFormat('H:i', $heureString);

        // Vérification des réservations existantes pour la même date et heure
        $existingReservation = $entityManager->getRepository(Reservations::class)
            ->findOneBy(['date' => $date, 'heure' => $heure]);

        if ($existingReservation) {
            // Message d'erreur si le créneau est déjà pris
            $this->addFlash('error', 'Ce créneau est déjà réservé. Veuillez choisir un autre créneau.');
            return $this->redirectToRoute('app_reservation');
        }

        // Sauvegarde de l'utilisateur et de la réservation
        $entityManager->persist($user);

        $reservation = new Reservations();
        $reservation->setDate($date);
        $reservation->setHeure($heure);
        $reservation->setUser($user);
        $reservation->setService($form->get('service')->getData());

        $entityManager->persist($reservation);
        $entityManager->flush();

        return $this->redirectToRoute('app_reservation_success');
        }

        // Affiche le formulaire
        return $this->render('reservation.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/reservation/success', name: 'app_reservation_success')]
    public function reservationSuccess(): Response
    {
        return $this->render('success.html.twig');
    }

    #[Route('/reservation/list', name: 'app_reservation_list')]
    public function listReservations(EntityManagerInterface $entityManager): Response
    {
        // Récupère toutes les réservations avec les informations associées
        $reservations = $entityManager->getRepository(Reservations::class)->findAll();

        return $this->render('list.html.twig', [
            'reservations' => $reservations,
        ]);
    }
}
