<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\Post;
use AppBundle\Entity\PostRole;
use AppBundle\Form\PostType;
use Symfony\Component\Form\Form;

class DefaultController extends Controller
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

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post = $form->getData();

            $doctrine = $this->getDoctrine();
            $entityManager = $doctrine->getManager();
            $entityManager->persist($post);
            $entityManager->flush();

            $postRepository = $doctrine->getRepository('AppBundle:Post');
            $postRepository->addRole(PostRole::TYPE_OWNER, $post, $this->getUser());

            return $this->redirectToRoute('post_edit', ['id' => $post->getId()]);
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
     * @Route("/", name="homepage")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        return $this->render('default/index.html.twig', [
            'posts' => $this->getPostsICanView(),
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
        ]);
    }

    /**
     * @return array
     */
    private function getPostsICanView(): array
    {
        $postsICanView = [];

        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $postsICanView;
        }

        $postRepository = $this->getDoctrine()->getRepository('AppBundle:Post');

        $allPosts = $postRepository->findAll();

        foreach ($allPosts as $post) {
            if ($this->isGranted('show', $post)) {
                $postsICanView[] = $post;
            }
        }

        return $postsICanView;
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
     * @return void
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
