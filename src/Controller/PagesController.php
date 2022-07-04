<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Google\Cloud\Translate\V2\TranslateClient;

class PagesController extends AbstractController
{
    public const GOOLGE_API_KEY = 'AIzaSyBiBd0Cg2umJavucDlEBg3nljlXUkBwBrQ';

    #[Route('/', name: 'app_home')]
    public function home(): Response
    {
        /**
         * I am using this package to parse pdf and get text.
         * https://github.com/smalot/pdfparser
         */

        $package  = new Package(new EmptyVersionStrategy());
        $path = $package->getUrl('sample.pdf');
        // dump($path);
        $parser = new \Smalot\PdfParser\Parser();
        $pdf = $parser->parseFile($path);
        $text = $pdf->getText();
        dump($text);
        return $this->render('pages/home.html.twig', [
            'current_page' => 'home',
            'text' => $text,
        ]);
    }
}