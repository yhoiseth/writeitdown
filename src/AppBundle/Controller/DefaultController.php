<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Stringy\Stringy;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\Post;
use AppBundle\Entity\PostRole;
use AppBundle\Form\PostType;
use Symfony\Component\Form\Form;
use function Stringy\create as s;


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
            /** @var Post $post */
            $post = $form->getData();

            $slug = $this->get('slugify')->slugify($post->getTitle());

            $doctrine = $this->getDoctrine();
            $entityManager = $doctrine->getManager();

            while ($this->userOwnsPostWithSameSlug($slug)) {
                $slug = $this->incrementSlug($slug);
            }


            $post->setSlug($slug);

            $entityManager->persist($post);
            $entityManager->flush();

            $postRepository = $doctrine->getRepository('AppBundle:Post');
            $postRepository->addRole(PostRole::TYPE_OWNER, $post, $this->getUser());

            return $this->redirectToRoute('post_edit', [
                'username' => $this->getUser()->getUsername(),
                'slug' => $post->getSlug(),
            ]);
        }

        return $this->render('AppBundle:Post:new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{username}", name="profile")
     * @param Request $request
     * @param string $username
     * @return Response
     */
    public function profileAction(Request $request, string $username): Response
    {
        if ($this->getUser()->getUsername() !== $username) {
            return new Response('', 403);
        }

        $postRepository = $this->getDoctrine()->getManager()->getRepository('AppBundle:Post');
        $userManager = $this->get('fos_user.user_manager');

        $posts = $postRepository->createQueryBuilder('post')
            ->join('post.roles', 'role')
            ->where('role.user = :owner')
            ->andWhere('role.type = :roleType')
            ->setParameter('owner', $userManager->findUserByUsername($username))
            ->setParameter('roleType', PostRole::TYPE_OWNER)
            ->getQuery()
            ->getResult()
        ;

        return $this->render('AppBundle:Profile:show.html.twig', [
            'posts' => $posts,
            'username' => $username,
        ]);
    }

    /**
     * @Route("/{username}/{slug}/edit", name="post_edit")
     * @param Request $request
     * @param string $username
     * @param string $slug
     * @return Response
     */
    public function editAction(Request $request, string $username, string $slug)
    {
        $postRepository = $this->getDoctrine()->getRepository('AppBundle:Post');
        $post = $postRepository->findOneBy(['slug' => $slug]);

        $this->denyAccessUnlessGranted('edit', $post);

        $form = $this->getForm($post);

        $this->handlePostFormRequest($request, $form);

        return $this->render('AppBundle:Post:edit.html.twig', [
            'form' => $form->createView(),
            'username' => $username,
            'post' => $post,
        ]);
    }

    /**
     * @Route("/{username}/{slug}", name="post_show")
     * @param Request $request
     * @param string $slug
     * @param string $username
     * @return Response
     */
    public function showAction(Request $request, string $slug, string $username): Response
    {
        $post = $this->getDoctrine()->getRepository('AppBundle:Post')->findOneBy([
            'slug' => $slug,
        ]);

        $this->denyAccessUnlessGranted('show', $post);

        return $this->render('AppBundle:Post:show.html.twig', [
            'post' => $post,
            'username' => $username,
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
     * @param string $slug
     * @return bool
     */
    private function userOwnsPostWithSameSlug(string $slug): bool
    {
        $entityManager = $this->getDoctrine()->getManager();

        $queryForPostsOwnedByUserWithSameSlug = $entityManager->getRepository('AppBundle:Post')
            ->createQueryBuilder('post')
            ->join('post.roles', 'role')
            ->where('post.slug = :slug')
            ->andWhere('role.user = :user')
            ->andWhere('role.type = :roleType')
            ->setParameter('slug', $slug)
            ->setParameter('user', $this->getUser())
            ->setParameter('roleType', PostRole::TYPE_OWNER)
            ->getQuery()
        ;

        $postsOwnedByUserWithSameSlug = $queryForPostsOwnedByUserWithSameSlug->getResult();

        if (count($postsOwnedByUserWithSameSlug) > 0) {
            return true;
        }

        return false;
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

    /**
     * @param string $slug
     * @return string $slug
     */
    public function incrementSlug(string $slug): string
    {
        /** @var Stringy $parts */
        $parts = s($slug)->split('-');

        $numberOfParts = count($parts);

        $lastPartIndex = $numberOfParts - 1;

        $lastPart = $parts[$lastPartIndex];

        if (ctype_digit((string) $lastPart)) {
            $oldVersionNumber = (integer) (string) $lastPart;

            $incrementedVersionNumber = $oldVersionNumber + 1;

            $newLastPart = s((string) $incrementedVersionNumber);

            $parts[$lastPartIndex] = $newLastPart;

            $slug = implode('-', $parts);

            return $slug;
        }

        return $slug . '-2';
    }
}
