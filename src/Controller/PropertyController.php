<?php
namespace App\Controller;

use App\Entity\Contact;
use App\Entity\Property;
use App\Entity\PropertySearch;
use App\Form\ContactType;
use App\Form\PropertySearchType;
use App\Notification\ContactNotification;
use App\Repository\PropertyRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PropertyController extends AbstractController {

    /**
     * @var PropertyRepository
     */
    private $repository;
    /**
     * @var ObjectManager
     */
    private $em;

    public function __construct(PropertyRepository $repository, ObjectManager $em)
    {
        $this->repository=$repository;
        $this->em=$em;
    }

    /**
     * @Route("biens",name="property.index")
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function index(PaginatorInterface $paginator,Request $request):Response{

        $search=new PropertySearch();
        $form=$this->createForm(PropertySearchType::class,$search);
        $form->handleRequest($request);

        $properties = $paginator->paginate(
            $this->repository->findAllVisibleQuery($search,false), /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            12 /*limit per page*/
        );
        return $this->render('property/index.html.twig',[
            'current_menu'=>'biens',
            'properties'=>$properties,
            'form'=>$form->createView()
        ]);
    }


    /**
     * @Route("biens-location",name="property.index.location")
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function indexLocation(PaginatorInterface $paginator,Request $request):Response{

        $search=new PropertySearch();
        $form=$this->createForm(PropertySearchType::class,$search);
        $form->handleRequest($request);

        $properties = $paginator->paginate(
            $this->repository->findAllVisibleQuery($search,true), /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            12 /*limit per page*/
        );
        return $this->render('property/index.html.twig',[
            'current_menu'=>'biens-location',
            'properties'=>$properties,
            'form'=>$form->createView()
        ]);
    }


    /**
     * @Route("biens/{slug}-{id}",name="property.show",requirements={"slug":"[a-z0-9\-]*"})
     */
    public function show(Property $property,$slug,Request $request,ContactNotification $contactNotification):Response{

       if($property->getSlug() !== $slug){
           return $this->redirectToRoute('property.show',[
               'id'=> $property->getId(),
               'slug'=>$property->getSlug()
           ],301);
       }

        $contact=new Contact();
        $contact->setProperty($property);
        $form=$this->createForm(ContactType::class,$contact);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $contactNotification->notify($contact);
            $this->addFlash('success','Votre message a bien été envoyé');

            return $this->redirectToRoute('property.show',[
                'id'=> $property->getId(),
                'slug'=>$property->getSlug()
            ]);


        }

        return $this->render("property/show.html.twig",[
            'property' => $property,
            'current_menu'=>'biens',
            'form'=> $form->createView()
        ]);
    }

    /**
     * @Route(path="/properties/{id}/mail",name="save.mail",methods={"POST"})
     * @param $id
     * @param Request $request
     * @param ContactNotification $contactNotification
     * @return Response
     */
    public function sendMail(Property $property,Request $request,ContactNotification $contactNotification){
        $data=json_decode($request->getContent());
        //$property=$this->repository->find($id);
        $contact=new Contact();
        $contact->setProperty($property);
        $contact->setEmail($data->email);
        $contact->setFirstname($data->firstname);
        $contact->setLastname($data->lastname);
        $contact->setPhone($data->phone);
        $contact->setMessage($data->message);
        $contactNotification->notify($contact);

        $response=new Response();
        $response->setStatusCode(Response::HTTP_OK);
        return $response;
    }

    /**
     * @Route(path="/api/uploadPhoto/{id}",name="upload.photo",methods={"POST"})
     * @param $id
     * @param Request $request
     * @return Response
     */
    public function uploadPhoto($id,Request $request){
        $file=$request->files->get('file');
        echo $file;
        $destination = $this->getParameter('kernel.project_dir'). '/public/images/properties/';
        move_uploaded_file($file,$destination.$id.".png");

        $property=$this->repository->find($id);
/*        $property->setImageFile($file);*/
        $property->setImageName($id.".png");
       $this->em->flush();

        $response=new Response();
        $response->setStatusCode(Response::HTTP_OK);
        return $response;

    }

    /**
     * @Route(path="/api/photoProperty/{id}",name="show.property.image")
     * @param Property $property
     * @return BinaryFileResponse
     */
    public function getProductImage($id){
        $property=$this->repository->find($id);
        $publicResourcesFolderPath = $this->getParameter('kernel.project_dir'). '/public/images/properties/';
        $imageName=$property->getImageName();
        if($imageName!=null) {
            return new BinaryFileResponse($publicResourcesFolderPath . $imageName);
        }
        else
            return new BinaryFileResponse($publicResourcesFolderPath . 'empty.jpg');
    }

}