<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\UserPhotoForm;
use Symfony\Component\HttpFoundation\Response;
class PhotoController extends AbstractController
{
    #[Route('/compte/photo', name: 'app_compte_photo', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function uploadPhoto(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $photoFile = $request->files->get('photo');

        if ($photoFile) {
            $newFilename = uniqid() . '.' . $photoFile->guessExtension();

            try {
                $photoFile->move(
                    $this->getParameter('photos_directory'), // Assure-toi que ce paramètre existe dans services.yaml
                    $newFilename
                );
            } catch (FileException $e) {
                $this->addFlash('danger', 'Erreur lors de l\'upload de la photo.');
                return $this->redirectToRoute('app_compte');
            }

            $user->setPhoto($newFilename);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_compte'); // ou une autre route si besoin
    }

}
