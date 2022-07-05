<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class PagesController extends AbstractController
{
    /**
     * It provides utilities to consume APIs
     * @var HttpClientInterface $client
     */
    protected $client;

    /**
     * Get the GOOGLE_SCRIPT_TRANSLATE  
     * @var string $url
     */
    protected $url;

    /**
     * Using it in translate
     */
    protected $source_lang = '';
    protected $target_lang = 'ar';
    protected $text;


    protected $translated_text;

    protected $pdf;
    protected $pdfName = 'Document.pdf';



    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;

        /**
         * I am using this package to parse pdf and get text.
         * https://github.com/smalot/pdfparser
         */
        $package  = new Package(new EmptyVersionStrategy());
        $path = $package->getUrl($this->pdfName); // sample-pdf-file.pdf
        // dump($path);
        $parser = new \Smalot\PdfParser\Parser();
        $this->pdf = $parser->parseFile($path); // Parse pdf

    }



    #[Route('/', name: 'app_home')]
    public function home(Request $request): Response
    {

        $this->url = $this->getParameter('app.google_script_translate');

        $this->text = nl2br($this->pdf->getText());

        //Http request
        $this->response =   $this->client->request('POST', $this->url, [
            'headers' => [
                'Content-Type: application/json',
                'Accept: application/json',
            ],
            'query' => [
                'source_lang' => $this->source_lang,
                'target_lang' => $this->target_lang,
                'text' => $this->text
            ]
        ]);

        $this->translated_text = json_decode($this->response->getContent(), true);

        return $this->render('pages/home.html.twig', [
            'current_page' => 'home',
            'translated_text' => $this->translated_text['translatedText'],
            'details' => $this->pdf->getDetails(),

            'target_lang' => $this->target_lang,
            'source_lang' => $this->source_lang,
            'text' => $this->text,

        ]);
    }
}