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

        $this->denyAccessUnlessGranted('new', $post);

        $form = $this->getForm($post);

        $this->handlePostFormRequest($request, $form);

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

        $this->denyAccessUnlessGranted('edit', $post);

        $form = $this->getForm($post);

        $this->handlePostFormRequest($request, $form);

        return $this->render('AppBundle:Post:edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="post_show")
     * @param Request $request
     * @param string $id
     * @return Response
     */
    public function showAction(Request $request, string $id)
    {
        $post = $this->getDoctrine()->getRepository('AppBundle:Post')->find($id);

        $this->denyAccessUnlessGranted('show', $post);

        return $this->render('AppBundle:Post:show.html.twig', [
            'post' => $post,
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

    /**
     * @param Request $request
     * @param Form $form
     */
    private function handlePostFormRequest(Request $request, Form $form): void
    {
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($post);
            $entityManager->flush();
        }
    }
}
