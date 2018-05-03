<?php

namespace App\Controller;

use App\Entity\Todo;
use App\Form\TodoType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class TodoController extends Controller
{
    /**
     * @Route("/todo", name="todo")
     */
    public function index()
    {
        $todos = $this->getDoctrine()
            ->getRepository(Todo::class)
            ->findAll();

        return $this->render('todo/index.html.twig', [
            'todos' => $todos
        ]);
    }

    /**
     * @Route("/todo/view/{id}", name="todo_view")
     */
    public function view($id)
    {
        $todo = $this->getDoctrine()
            ->getRepository(Todo::class)
            ->find($id);
        dump($todo);

        return $this->render('todo/view.html.twig', [
            'todo' => $todo
        ]);
    }

    /**
     * @Route("/todo/edit/{id}", name="todo_edit")
     */
    public function edit($id, Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $todo = $entityManager
            ->getRepository(Todo::class)
            ->find($id);

        $form = $this->createForm(TodoType::class, $todo);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $todo->setName($data->getName());
            $todo->setCategory($data->getCategory());
            $todo->setDescription($data->getDescription());
            $todo->setTodoDate($data->getTodoDate());
            $todo->setCreateDate($data->getCreateDate());

            $entityManager->persist($todo);
            $entityManager->flush();

            return $this->redirectToRoute('todo_view', [
                'id' => $todo->getId()
            ]);
        }

        return $this->render('todo/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/todo/delete/{id}", name="todo_delete")
     */
    public function delete($id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $todo = $entityManager->getRepository(Todo::class)
            ->find($id);

        $entityManager->remove($todo);
        $entityManager->flush();

        $todos = $entityManager->getRepository(Todo::class)
            ->findAll();

        return $this->redirectToRoute('todo', [
            'todos' => $todos
        ]);
    }

    /**
     * @Route("/todo/create", name="todo_create")
     */
    public function create(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $form = $this->createForm(TodoType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $todo = new Todo();
            $todo->setName($data->getName());
            $todo->setCategory($data->getCategory());
            $todo->setDescription($data->getDescription());
            $todo->setTodoDate($data->getTodoDate());
//            $todo->setCreateDate($data->getCreateDate()); // would be better to use date function to get current date
            $currnetDate = new \DateTime();
            $todo->setCreateDate($currnetDate);

            $entityManager->persist($todo);
            $entityManager->flush();

            $todos = $entityManager->getRepository(Todo::class)
                ->findAll();

            return $this->redirectToRoute('todo', [
                'todos' => $todos
            ]);
        }

        return $this->render('todo/create.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
