<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Tag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TagController extends AbstractController
{
    /**
     * @Route("/tag/add", name="add_tag")
     */
    public function addTag(Request $request)
    {
        $requestString = $request->get('q');
        $tag = new Tag();
        $tag->setName($requestString);
        if($requestString !== null){
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($tag);
            $entityManager->flush();
        }
        return new Response('ok');
    }
    /**
     * @Route("/search-tag", name="search-tag")
     */
    public function searchTag(Request $request)
    {
        
        $requestString = $request->get('q');
        $repo = $this->getDoctrine()->getRepository( Tag::class );
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
            $realEntities[$entity->getId()] = $entity->getName();
        }
        return $realEntities;
    }
}
