<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TaskController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Liste les tâches terminées ou non terminées en fonction du paramètres $done
     * @param true|null $done
     * 
     * @Route("/tasks/{done?}", name="task_list", requirements={"done"="\d+"})
     */
    public function listAction(TaskRepository $taskRepository, $done)
    {
        $tasks = ($done) ? $taskRepository->findBy(['isDone' => true]) : $taskRepository->findBy(['isDone' => false]);

        return $this->render('task/list.html.twig', [
            'tasks' => $tasks
        ]);
    }

    /**
     * Permet de créer une tâche à l'aide d'un formulaire
     *  
     * @Route("/tasks/create", name="task_create")
     * @IsGranted("ROLE_USER")
     */
    public function createAction(Request $request, UserRepository $userRepository)
    {
        $task = new Task();
        $user = $this->getUser() ?? $userRepository->findOneBy(['username' => 'anonym']);
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $task->setUser($user);
            $this->em->persist($task);
            $this->em->flush();

            $this->addFlash('success', 'La tâche a été bien été ajoutée.');

            return $this->redirectToRoute('task_list', [
                'done' => false,
            ]);
        }

        return $this->render('task/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet de modifier une tâche à l'aide d'un formulaire
     * 
     * @Route("/tasks/{id}/edit", name="task_edit", requirements={"id"="\d+"})
     * @IsGranted("ROLE_USER")
     */
    public function editAction(Task $task, Request $request)
    {
        $isAdmin = in_array('ROLE_ADMIN', $this->getUser()->getRoles()) ?? false;

        if ($task->getUser() !== $this->getUser() && !$isAdmin) {
            $this->addFlash('error', 'Vous ne pouvez pas modifier cette tâche.');

            return $this->redirectToRoute('task_list', [
                'done' => $task->getIsDone(),
            ]);
        }

        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($task);
            $this->em->flush();

            $this->addFlash('success', 'La tâche a bien été modifiée.');

            return $this->redirectToRoute('task_list', [
                'done' => $task->getIsDone(),
            ]);
        }

        return $this->render('task/edit.html.twig', [
            'form' => $form->createView(),
            'task' => $task,
        ]);
    }

    /**
     * Permet de changer le statut du tâche (à faire ou terminées) 
     * 
     * @Route("/tasks/{id}/toggle", name="task_toggle", requirements={"id"="\d+"})
     * @IsGranted("ROLE_USER")
     */
    public function toggleTaskAction(Task $task)
    {
        $task->toggle(!$task->getIsDone());
        $this->em->persist($task);
        $this->em->flush();

        $state = ($task->getIsDone()) ? 'faite' : 'non terminée';

        $this->addFlash('success', sprintf(
            'La tâche \' %s \' a bien été marquée comme %s.', 
            $task->getTitle(),
            $state
        ));

        return $this->redirectToRoute('task_list', [
            'done' => false,
        ]);
    }

    /**
     * Supprime une tâche à condition d'être le propriétaire de celle-ci ou d'être administrateur
     * 
     * @Route("/tasks/{id}/delete", name="task_delete", requirements={"id"="\d+"})
     * @IsGranted("ROLE_USER")
     */
    public function deleteTaskAction(Task $task)
    {
        $isAdmin = in_array('ROLE_ADMIN', $this->getUser()->getRoles()) ?? false;

        if ($task->getUser() === $this->getUser() || $isAdmin) {
            $this->em->remove($task);
            $this->em->flush();

            $this->addFlash('success', 'La tâche a bien été supprimée.');
        } else {
            $this->addFlash('error', 'Vous ne pouvez pas supprimer cette tâche.');
        }

        return $this->redirectToRoute('task_list');
    }
}
