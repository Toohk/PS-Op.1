<?php

namespace App\Controller;

use App\Entity\Page;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        $repo = $this->getDoctrine()->getRepository( Page::class );
        $pages = $repo->findBy(['status'=>'PUBLIC']);
        return $this->render('index/index.html.twig', [
            'controller_name' => 'IndexController',
            'pages' => $pages
        ]);
    }

}
