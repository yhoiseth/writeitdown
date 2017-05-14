<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\Post\SlugType;
use AppBundle\Repository\PostRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Stringy\Stringy;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
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

        $post->setTitle('Untitled');

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

    /**
     * @Route("/{username}", name="profile")
     * @param Request $request
     * @param string $username
     * @return Response
     */
    public function profileAction(Request $request, string $username): Response
    {
        /** @var PostRepository $postRepository */
        $postRepository = $this->getDoctrine()->getManager()->getRepository('AppBundle:Post');
        $userManager = $this->get('fos_user.user_manager');

        $owner = $userManager->findUserByUsername($username);

        if (!$owner) {
            throw $this->createNotFoundException('User not found');
        }

        $queryBuilder = $postRepository->createQueryBuilder('post')
            ->join('post.roles', 'role')
            ->where('role.user = :owner')
            ->andWhere('role.type = :roleType')
            ->setParameter('owner', $owner)
            ->setParameter('roleType', PostRole::TYPE_OWNER);

        if ($this->getUser() !== $owner) {
            $queryBuilder
                ->andWhere('post.publishedAt != :null')
                ->setParameter('null', serialize(null));
        }

        $posts = $queryBuilder
            ->getQuery()
            ->getResult();

        return $this->render('AppBundle:Profile:show.html.twig', [
            'posts' => $posts,
            'owner' => $owner,
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
        $post = $this->getPostBySlugAndOwner($username, $slug);

        $this->denyAccessUnlessGranted('edit', $post);

        $form = $this->getForm($post);

        $post = $this->handlePostFormRequest($request, $form, $post);

        return $this->render('AppBundle:Post:edit.html.twig', [
            'form' => $form->createView(),
            'username' => $username,
            'post' => $post,
        ]);
    }

    /**
     * @Route("/{username}/{slug}/publish", name="post_publish")
     * @param Request $request
     * @param string $username
     * @param string $slug
     * @return RedirectResponse
     */
    public function publishPostAction(Request $request, string $username, string $slug)
    {
        $post = $this->getPostBySlugAndOwner($username, $slug);
        
        $this->denyAccessUnlessGranted('edit', $post);

        $post->publish();

        $entityManager = $this->getDoctrine()->getManager();

        $entityManager->persist($post);
        $entityManager->flush();

        $this->addFlash('success', 'Post published');

        return $this->redirectToRoute(
            'post_show', [
                'username' => $username,
                'slug' => $slug,
        ]);
    }

    /**
     * @Route("/system/post/autosave/{id}", name="post_autosave")
     * @param Request $request
     * @param string $id
     * @return Response
     */
    public function autosavePostAction(Request $request, string $id)
    {
        $postRepository = $this->getDoctrine()->getRepository('AppBundle:Post');

        $post = $postRepository->find($id);

        $this->denyAccessUnlessGranted('edit', $post);

        $form = $this->getForm($post);

        $post = $this->handlePostFormRequest($request, $form, $post);

        return new JsonResponse(
            json_encode([
                'updatedAt' => $post->getUpdatedAt()
            ])
        );
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
        $post = $this->getPostBySlugAndOwner($username, $slug);

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
        if ($this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            /** @var User $user */
            $user = $this->getUser();

            return $this->redirectToRoute('profile', [
                'username' => $user->getUsernameCanonical()
            ]);
        }

        return $this->render('default/index.html.twig');
    }

    /**
     * @Route("/{username}/{slug}/slug/edit", name="post_slug_edit")
     * @param Request $request
     * @param string $username
     * @param string $slug
     * @return Response
     */
    public function editPostSlug(Request $request, string $username, string $slug)
    {
        $post = $this->getPostBySlugAndOwner(
            $username,
            $slug
        );

        $this->denyAccessUnlessGranted('edit', $post);

        $form = $this->createForm(SlugType::class, $post);

        $form->handleRequest($request);

        if ($form->isValid() && $form->isSubmitted()) {
            /** @var Post $post */
            $post = $form->getData();

            $post->setSlug(
                $this
                    ->get('slugify')
                    ->slugify(
                        $post->getSlug()
                    )
            );

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($post);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Slug updated'
            );

            return $this->redirectToRoute('post_edit', [
                'username' => $username,
                'slug' => $post->getSlug(),
            ]);
        }

        return $this->render('@App/Post/slug/edit.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
            'username' => $username,
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
     * @param Post $post
     * @return Post
     */
    private function handlePostFormRequest(Request $request, Form $form, Post $post)
    {
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Post $post */
            $post = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($post);
            $entityManager->flush();
        }

        return $post;
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

    /**
     * @param string $username
     * @param string $slug
     * @return Post|null
     */
    private function getPostBySlugAndOwner(string $username, string $slug)
    {
        $postRepository = $this->getDoctrine()->getRepository('AppBundle:Post');

        $owner = $this->get('fos_user.user_manager')->findUserByUsername($username);

        $post = $postRepository->getPostBySlugAndOwner($slug, $owner);

        return $post;
    }
}
