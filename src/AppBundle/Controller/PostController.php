<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Post;
use AppBundle\Form\PostType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class PostController extends Controller
{
    /**
     * @Route("/new")
     */
    public function newAction()
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);

        return $this->render('AppBundle:Post:new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}
