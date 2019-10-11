<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Validation;
use App\Entity\User;
use App\Entity\Tag;
use App\Entity\Page;
use App\Repository\PageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Form\PageType;
use App\Form\TagType;
use App\Form\CommentType;

class PageController extends AbstractController
{
    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request): Response
    {
        $user = $this->getUser();

        $repo = $this->getDoctrine()->getRepository( Tag::class );
        $tags = $repo->findAll();
        
        $page = new Page();

        $form = $this->createForm(PageType::class, $page);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $page->setStatus('INPROGRESS');
            $page->setDate( new \Datetime());
            $user->addPage($page);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($page);
            $entityManager->flush();

            return $this->redirectToRoute('user');
        }

        return $this->render('index/create.html.twig', [
            'PageForm' => $form->createView(),
            'tags'=> $tags
        ]);
    }

    /**
     * @Route("/search", name="search")
     */
    public function search(Request $request)
    {
        
        $requestString = $request->get('q');
        $repo = $this->getDoctrine()->getRepository( Page::class );
        $entities = $repo->findEntitiesByString($requestString);
     
        if(!$entities) {
            $result['entities']['error'] = "No results";
        } else {
            $result['entities'] = $this->getRealEntities($entities);
        }
        
        return new Response(json_encode($result));

        
    }
    public function getRealEntities($entities){
        foreach ($entities as $entity){
            $realEntities[$entity->getId()] = $entity->getTitle();
        }
        return $realEntities;
    }

    /**
     * @Route("/user", name="user")
     */
    public function user()
    {
        $user = $this->getUser();
        $pages = $user->getPages();

        return $this->render('index/user.html.twig', [
            'controller_name' => 'IndexController',
            'pages' => $pages,
        ]);
    }

    /**
     * @Route("/remove/{id}", name="remove")
     */
    public function remove($id): Response
    {
        $user = $this->getUser();
        $repo = $this->getDoctrine()->getRepository( Page::class );
        $page = $repo->find($id);
       
        $user->removePage($page);

        if($page->getUser() == null){
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($page);
            $entityManager->flush();

            return $this->redirectToRoute('user');
        }

        return $this->redirectToRoute('index');
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(Request $request, $id)
    {
        $user = $this->getUser();

        $repo = $this->getDoctrine()->getRepository( Page::class );
        $page = $repo->find($id);
        
        if($page->getUser() == $user) {

            $form = $this->createForm(PageType::class, $page);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                
                $page->setStatus('INPROGRESS');
                $page->setDate( new \Datetime());

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($page);
                $entityManager->flush();

                return $this->redirectToRoute('user');
            }
        }

        return $this->render('index/edit.html.twig', [
            'PageForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/status/{id}/{status}", name="status")
     */
    public function status(Request $request, $id, $status)
    {
        $user = $this->getUser();

        $repo = $this->getDoctrine()->getRepository( Page::class );
        $page = $repo->find($id);
        
        if($page->getUser() == $user) {

            $entityManager = $this->getDoctrine()->getManager();

            if ($status == 'INPROGRESS' or $status == 'INREVIEW'){
                $page->setStatus($status);
                $entityManager->persist($page);
                $entityManager->flush();
            }
            if($status == 'PUBLIC' and count($page->getValidations()) > 3){
                $page->setStatus($status);
                $entityManager->persist($page);
                $entityManager->flush();
            }
            return $this->redirectToRoute('user');
            
        }
    }

    /**
     * @Route("/review", name="list_review")
     */
    public function listReview()
    {
        $user = $this->getUser();
        $repo = $this->getDoctrine()->getRepository( Page::class );
        $pages = $repo->findBy(['status'=>'INREVIEW']);
        
        return $this->render('page/list_review.html.twig', [
            'controller_name' => 'IndexController',
            'pages' => $pages,
        ]);
    }

    /**
     * @Route("/review/{id}", name="review")
     */
    public function review($id,Request $request): Response
    {
        $user = $this->getUser();
        $repo = $this->getDoctrine()->getRepository( Page::class );
        $page = $repo->find($id);

        $comments = $page->getComments();

        $comment = new Comment;
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $page->addComment($comment);
            $user->addComment($comment);
            $comment->setDate( new \Datetime());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();
        }
        

        return $this->render('page/review.html.twig', [
            'controller_name' => 'IndexController',
            'page' => $page,
            'comments' => $comments,
            'CommentForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/approve/{id}", name="approve")
     */
    public function approve($id)
    {
        $user = $this->getUser();
        $repo = $this->getDoctrine()->getRepository( Page::class );
        $page = $repo->find($id);

        $validations = $page->getValidations();

        $x = false;
        foreach($validations as $validation){
            if($validation->getUser() == $user){
                $x= true;
            }
        }
        if ($x == false){
            $validation = new Validation;
            $page->addValidation($validation);
            $user->addValidation($validation);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($validation);
            $entityManager->flush();
        }  
        
        return $this->redirectToRoute('list_review');
    }
    
}
