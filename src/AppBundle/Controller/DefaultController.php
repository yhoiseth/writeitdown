<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
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

    public function getPostsICanView()
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
}
