<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Service\NewsParsingService;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="app_admin", methods={"GET","POST"})
     */
    public function index(NewsParsingService $newsParsingService, Request $request): Response
    {
        $pagination = $newsParsingService->get($request);
        return $this->render('admin/index.html.twig', ['pagination' => $pagination, 'totalCount' => $pagination->getTotalItemCount()]);
    }
    /**
     * @Route("/admin/makeAction", name="makeAction", methods={"GET","POST"})
     */
    public function makeAction(Request $request, NewsParsingService $newsParsingService): Response
    {
        $newsParsingService->makeAction($request);
        return new Response(true);
    }
}
