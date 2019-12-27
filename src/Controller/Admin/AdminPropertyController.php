<?php
namespace App\Controller\Admin;

use App\Entity\Property;
use App\Form\PropertyType;
use App\Repository\PropertyRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminPropertyController extends AbstractController {

    /**
     * @var PropertyRepository
     */
    private $repository;
    /**
     * @var ObjectManager
     */
    private $em;

    public function __construct(PropertyRepository $repository,ObjectManager $em)
    {
        $this->repository = $repository;
        $this->em = $em;
    }

    /**
     * @Route(path="/admin",name="admin.property.index")
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(PaginatorInterface $paginator,Request $request){

        $properties = $paginator->paginate(
            $this->repository->findAll(), /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );

       return $this->render("admin/property/index.html.twig",[
           'properties'=>$properties
       ]);
    }

    /**
     * @Route(path="/admin/{id}",name="admin.property.edit",methods="GET|POST")
     */
    public function edit(Property $property,Request $request){
        $form = $this->createForm(PropertyType::class,$property);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $this->em->flush();
            $this->addFlash('success','Bien modifie avec succes');
            return $this->redirectToRoute('admin.property.index');
        }
        return $this->render("admin/property/edit.html.twig",[
            'property' => $property,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route(path="/admin/property/create",name="admin.property.new")
     */
    public function new(Request $request){
        $property = new Property();
        $form = $this->createForm(PropertyType::class,$property);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $this->em->persist($property);
            $this->em->flush();
            $this->addFlash('success','Bien ajouter avec succes');
            return $this->redirectToRoute('admin.property.index');
        }
        return $this->render("admin/property/create.html.twig",[
            'property' => $property,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route(path="/admin/{id}",name="admin.property.delete",methods="DELETE")
     * @param Property $property
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete(Property $property,Request $request){
        if($this->isCsrfTokenValid('delete'.$property->getId(),$request->get('_token'))){
            $this->em->remove($property);
            $this->em->flush();
            $this->addFlash('success','Bien supprimer avec succes');
        }
        return $this->redirectToRoute("admin.property.index");
    }
}