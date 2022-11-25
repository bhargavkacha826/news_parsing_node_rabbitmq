<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class HomeController extends AbstractController
{
    /**
     * creates a customer object and initializes 
     * @Route("/", name="create_order", methods={"GET","POST"})
     */
    public function index(Request $request, Session $session): Response
    {
        return $this->renderForm('home/index.html.twig');
    }
}
