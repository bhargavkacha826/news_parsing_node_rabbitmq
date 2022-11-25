<?php

namespace App\Service;

use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Security;
use App\Entity\News;

class NewsParsingService
{
    protected $doctrine;
    protected $paginator;
    protected $serializer;
    protected $security;

    public function __construct(Security $security,  ManagerRegistry $doctrine, PaginatorInterface $paginator, SerializerInterface $serializer)
    {
        $this->doctrine = $doctrine;
        $this->security = $security;
        $this->paginator = $paginator;
        $this->serializer = $serializer;
    }
    public function get($request)
    {
        $qb = $this->doctrine->getRepository(News::class)->getNewsData();
        $pagination = $this->paginator->paginate(
            $qb,
            $request->query->getInt('page', 1),
            10
        );
        return $pagination;
    }
    public function makeAction($request)
    {
        if ($request->request->get('action') == 'delete_news') {
            $this->doctrine->getRepository(News::class)->deleteNews($request->request->get('id'));
        }
    }
}
