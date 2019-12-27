<?php
namespace App\Controller;

use App\Repository\PropertyRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class HomeController extends AbstractController {



    /**
     * @Route("/",name="home")
     * @return Response
     */
    public function index(PropertyRepository $property):Response{
        $properties=$property->findLatest();
        return $this->render("pages/home.html.twig",[
            'properties'=>$properties
        ]);
    }
}