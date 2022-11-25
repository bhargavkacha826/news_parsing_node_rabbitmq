<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Psr\Log\LoggerInterface;
use App\Scraper\Contracts\SourceInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Panther\Client;
use DOMDocument;
use DOMXPath;
use App\Entity\News;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\BrowserKit\Response;

class NewsParsing extends Command
{
    protected $source;
    protected $logger;

    protected static $defaultName = "app:news:parsing";

    public function __construct(
        SourceInterface $source,
        ManagerRegistry $doctrine,
        LoggerInterface $logger,
    ) {
        $this->source = $source;
        $this->doctrine = $doctrine;
        $this->logger = $logger;
        parent::__construct();
    }
    protected function configure()
    {
        $this->setDescription('News Parsing')->addOption('dry-run', null, InputOption::VALUE_NONE, 'Dry run');
    }
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->doctrine->getConnection()->beginTransaction(); // suspend auto-commit

        try {
            $this->logger->info('News Parsing Cron Start....');
            dump('News Parsing Cron Start....');
            $client = Client::createChromeClient(__DIR__ . '/../../drivers/chromedriver');
            $client->request('GET', $this->source->getUrl());
            $html = $client->getInternalResponse()->getContent();
            $crawler = new Crawler($html);
            // you can use following to get the whole HTML
            $crawler->outerHtml();
            $crawler->filter('div.lenta-item')->each(function (Crawler $c) {
                // Find and filter the title
                $title = $c->filter($this->source->getTitleSelector())->text();

                // Find and filter the deskc
                $desk = explode("</p>", $c->outerHtml(), -1);
                $f = explode(">", end($desk));
                $description = end($f);

                // Find and filter the image
                $image = $c->filter($this->source->getImageSelector());
                $doc = new DOMDocument();
                $doc->loadHTML($image->outerHtml());
                $xpath = new DOMXPath($doc);
                $src = $xpath->evaluate("string(//img/@src)");

                // Find and filter the date

                // Initialising the DateTime() object with a date
                $datetime = new \DateTime();
                // Calling the format() function with a 
                // specified format 'd-m-Y H:i:s'
                $date = $datetime->format('Y-m-d H:i:s');

                $query = $this->doctrine->getManager()->createQuery("SELECT n.title FROM App\Entity\News as n where n.title='" . trim($title) . "'");
                $data = $query->getOneOrNullResult();
                dump("News Title : " . $title);
                if (!is_null($data)) {
                    $queryBuilder = $this->doctrine->getManager()->createQueryBuilder('n');
                    $query = $queryBuilder->update(News::class, 'n')
                        ->set('n.created_at', ':created_at')
                        ->where('n.title = :title')
                        ->setParameter('created_at', $datetime)
                        ->setParameter('title', trim($title))
                        ->getQuery()->execute();
                } else {
                    $post = new News();
                    $title = $c->filter($this->source->getTitleSelector())->text();
                    $this->logger->info("titie :" . $title);
                    $post->setTitle($title);

                    $this->logger->info("image :" . $src);
                    $post->setImage($src);

                    $this->logger->info("description :" . $description);
                    $post->setDescription($description);



                    $post->setCreatedAt($datetime);
                    $this->logger->info("date :" . $date);

                    $this->doctrine->getManager()->persist($post);
                }
            });
            $this->doctrine->getManager()->flush();
            $this->doctrine->getConnection()->commit();
            $this->logger->info('News Parsing Cron End....');
            dump('News Parsing Cron End....');
        } catch (\Exception $e) {
            $this->doctrine->getConnection()->rollBack();
            dump($e->getMessage());
            $this->logger->error('An error occurred' . $e->getMessage());
        }
        return true;
    }
}
