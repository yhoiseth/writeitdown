<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Post;
use AppBundle\Form\PostType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PostController extends Controller
{
    /**
     * @Route("/new", name="post_new")
     * @param Request $request
     * @return Response
     */
    public function newAction(Request $request)
    {
        $post = new Post();
        $form = $this->getForm($post);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($post);
            $entityManager->flush();
        }

        return $this->render('AppBundle:Post:new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/edit/{id}", name="post_edit")
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function editAction(Request $request, string $id)
    {
        $postRepository = $this->getDoctrine()->getRepository('AppBundle:Post');
        $post = $postRepository->find($id);

        $form = $this->getForm($post);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($post);
            $entityManager->flush();
        }

        return $this->render('AppBundle:Post:edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Post $post
     * @return Form
     */
    private function getForm($post): Form
    {
        $form = $this->createForm(PostType::class, $post);

        return $form;
    }
}
