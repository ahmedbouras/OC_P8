<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    private $em;
    private $passwordEncoder;

    public function __construct(EntityManagerInterface $em, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->em = $em;
        $this->passwordEncoder = $passwordEncoder;
    }
    
    /**
     * Liste tout les utilisateurs
     * 
     * @Route("/users", name="user_list")
     */
    public function listAction(UserRepository $userRepository)
    {
        return $this->render('user/list.html.twig', [
            'users' => $userRepository->findAll()
        ]);
    }

    /**
     * Permet de créer un utilisateur à l'aide d'un formulaire
     * 
     * @Route("/users/create", name="user_create")
     */
    public function createAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $this->passwordEncoder->encodePassword(
                    $user,
                    $form->get('password')->getData(),
                )
            );

            $this->em->persist($user);
            $this->em->flush();

            $this->addFlash('success', "L'utilisateur a bien été ajouté.");

            return $this->redirectToRoute('user_list');
        }

        return $this->render('user/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet de modifier un utilisateur à l'aide d'un formulaire
     * 
     * @Route("/users/{id}/edit", name="user_edit", requirements={"id"="\d+"})
     */
    public function editAction(User $user, Request $request)
    {
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $this->passwordEncoder->encodePassword(
                    $user,
                    $form->get('password')->getData(),
                )
            );

            $this->em->persist($user);
            $this->em->flush();

            $this->addFlash('success', "L'utilisateur a bien été modifié");

            return $this->redirectToRoute('user_list');
        }

        return $this->render('user/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }

    /**
     * Supprime un utilisateur
     * 
     * @Route("/users/{id}/delete", name="user_delete", requirements={"id"="\d+"})
     */
    public function deleteAction(User $user)
    {
        try {
            $this->em->remove($user);
            $this->em->flush();

            $this->addFlash('success', "L'utilisateur a bien été supprimée.");
        } // @codeCoverageIgnoreStart 
        catch (\Exception $e) {            
            $this->addFlash('error', 'Erreur lors de la suppression : ' . $e->getMessage());
        } // @codeCoverageIgnoreEnd

        return $this->redirectToRoute('user_list');
    }
}
