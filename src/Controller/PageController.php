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
use App\Form\CommentType;
use PhpParser\Node\Stmt\Return_;

class PageController extends AbstractController
{


    /**
     * @Route("/p/{url}", name="page")
     */
    public function page($url)
    {
        $repo = $this->getDoctrine()->getRepository( Page::class );
        $page = $repo->findOneBy(['url' => $url]);
        return $this->render('index/page.html.twig',[
            'page'=> $page
        ]);
    }


    /**
     * @Route("/new", name="new")
     */
    public function new(Request $request): Response
    {
        return $this->render('index/create.html.twig');
    }

    /**
     * @Route("/save", name="save-page")
     */
    public function save(Request $request): Response
    {
        $user = $this->getUser();
        $page = new Page();
        $data = JSON_decode($request->getContent(), true);

        $repo = $this->getDoctrine()->getRepository( Tag::class );

        $title = $data['title'];
        $intro = $data['intro'];
        $content = $data['content'];
        $tags = $data['tags'];

        $url = $this->slugify(strip_tags($title));

        foreach($tags as $tagObject) {
            $tag = $repo->find($tagObject['id']);
            $page->addTag($tag);
        };

        $page->setTitle($title);
        $page->setIntro($intro);
        $page->setContent($content);
        $page->setStatus('INPROGRESS');
        $page->setDate( new \Datetime());
        $page->setUrl($url);

        $user->addPage($page);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($page);
        $entityManager->flush();

        return $this->redirectToRoute('user'); 
       
    }
    public function slugify($string) {
 
        // Replaces all spaces with hyphens
        $string = str_replace(' ', '-', $string);
     
        // Replace accent characters
        $fromto = array('à'=>'a', 'À'=>'a', 'â'=>'â', 'Â'=>'a', 'á'=>'a', 'Á'=>'a', 'å'=>'a', 'Å'=>'a', 'ä'=>'a', 'Ä'=>'a', 'è'=>'e', 'È'=>'e', 'é'=>'e', 'É'=>'e', 'ê'=>'e', 'Ê'=>'e', 'ì'=>'i', 'Ì'=>'i', 'í'=>'i', 'Í'=>'i', 'ï'=>'i', 'Ï'=>'i', 'î'=>'i', 'Î'=>'i', 'ò'=>'o', 'Ò'=>'o', 'ó'=>'o', 'Ó'=>'o', 'ö'=>'o', 'Ö'=>'o', 'ô'=>'o', 'Ô'=>'o', 'ù'=>'u', 'Ù'=>'u', 'ú'=>'u', 'Ú'=>'u', 'ü'=>'u', 'Ü'=>'u', 'û'=>'u', 'Û'=>'u', 'ñ'=>'n', 'ç'=>'c', '·'=>'-', '/'=>'-', '_'=>'-', ','=>'-', ':'=>'-', ';'=>'-');
        $string = strtr($string, $fromto);
     
        // Make lowercase
        $string = strtolower($string);
     
        // Remove special chars
        $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string);
     
        // Replaces multiple hyphens with single one
        return preg_replace('/-+/', '-', $string);
    }
    /**
     * @Route("/save-edit", name="save-edit")
     */
    public function saveEdit(Request $request): Response
    {
        $user = $this->getUser();

        $data = JSON_decode($request->getContent(), true);
        $id = $data['id'];
        $title = $data['title'];
        $intro = $data['intro'];
        $content = $data['content'];
        $tags = $data['tags'];
        $url = $this->slugify(strip_tags($title));

        $repoPage = $this->getDoctrine()->getRepository( Page::class );
        $page = $repoPage->find($id);

        $repo = $this->getDoctrine()->getRepository( Tag::class );

        

        foreach($tags as $tagObject) {
            $tag = $repo->find($tagObject['id']);
            $page->addTag($tag);
        };

        $page->setTitle($title);
        $page->setIntro($intro);
        $page->setContent($content);
        $page->setUrl($url);
        $page->setStatus('INPROGRESS');
      

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($page);
        $entityManager->flush();

        return $this->redirectToRoute('user'); 
       
    }

    /**
     * @Route("/upload-picture", name="upload")
     */
    public function uploadPicture(Request $requestr)
    {
        if(isset($_FILES['upload']['name']))
        {
            $file = $_FILES['upload']['tmp_name'];
            $file_name = $_FILES['upload']['name'];
            $file_name_array = explode(".", $file_name);
            $extension = end($file_name_array);
            $new_image_name = rand() . '.' . $extension;

            $allowed_extension = array("jpg", "gif", "png");
            if(in_array($extension, $allowed_extension))
            {
               
            move_uploaded_file($file, 'uploads/pictures/' . $new_image_name);
            
            $url = 'http://localhost:8000/uploads/pictures/' . $new_image_name;

            $res =json_encode(array(
                'url' => $url
             
                ));
            return new Response($res);
            }
        }
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
            $realEntities[$entity->getUrl()] = $entity->getTitle();
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


        return $this->render('index/edit.html.twig',[
            'page' => $page,
        ]);
    }


    /**
     * @Route("/pull/{id}", name="pull")
     */
    public function pull(Request $request, $id){
        $user = $this->getUser();

        $repo = $this->getDoctrine()->getRepository( Page::class );
        $pageEntity = $repo->find($id);


        return new Response('nop');
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
            if($status == 'PUBLIC' and count($page->getValidations()) > 2){
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
