<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Tag;

class TagController extends AbstractController
{
    /**
     * @Route("/tag/add/{name}", name="add_tag")
     */
    public function addTag($name)
    {
        $tag = new Tag();
        $tag->setName($name);

        if($name !== null){
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($tag);
            $entityManager->flush();

            return $this->redirectToRoute('create');
        }

        return $this->redirectToRoute('index');
    }
}
