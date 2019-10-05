<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        return $this->render('index/index.html.twig', [
            'controller_name' => 'IndexController',
        ]);
    }

    /**
     * @Route("/signup", name="signup")
     */
    public function signup()
    {
        return $this->render('index/signup.html.twig', [
            'controller_name' => 'IndexController',
        ]);
    }

    /**
     * @Route("/login9", name="login")
     */
    public function login()
    {
        return $this->render('index/login.html.twig', [
            'controller_name' => 'IndexController',
        ]);
    }

    /**
     * @Route("/user", name="user")
     */
    public function user()
    {
        return $this->render('index/user.html.twig', [
            'controller_name' => 'IndexController',
        ]);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create()
    {
        return $this->render('index/create.html.twig', [
            'controller_name' => 'IndexController',
        ]);
    }

    /**
     * @Route("/edit", name="edit")
     */
    public function edit()
    {
        return $this->render('index/edit.html.twig', [
            'controller_name' => 'IndexController',
        ]);
    }
}
