<?php


namespace App\Controller;

use App\Entity\Property;
use App\Entity\PropertySearch;
use App\Form\PropertySearchType;
use App\Repository\PropertyRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use PhpParser\Node\Scalar\String_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PropertyController extends AbstractController
{
    /***
     * @var PropertyRepository
     */
    private $repository;

    /***
     * @var ObjectManager
     */
    private $objectManager;

    public function __construct(PropertyRepository $propertyRepository, EntityManagerInterface $objectManager)
    {
        $this->repository = $propertyRepository ;
        $this->objectManager = $objectManager ;
    }

    /**
     * @Route("/biens" , name="property.index")
     * @return Response
     */
    public function index(PaginatorInterface $paginator, Request $request): Response
    {
        //Traiter le filtre
        $search = new PropertySearch();
        $form = $this->createForm(PropertySearchType::class,$search);
        $form->handleRequest($request);

        $properties = $paginator->paginate(
           $this->repository->findAllVisibleQuery($search),
           $request->query->getInt('page',1),
           9
       );
        return $this->Render("property/index.html.twig",
            [
                'current_menu' => 'properties',
                'properties' => $properties,
                'form' => $form->createView()
            ]);
    }

    /**
     * @Route("/biens/{slug}-{id}" , name="property.show", requirements={"slug" = "[a-z0-9\-]*"})
     * @return Response
     */
    public function show(Property $property, string $slug): Response
    {

        //Si ce n'est pas le bon slug
        if($property->getSlug() !== $slug)
        {
            $this->redirectToRoute("property.show", [
                'id' => $property->getId(),
                'slug'=> $property->getSlug()
                ],301);
        }
        return $this->render("property/show.html.twig",
        [
            "property"=> $property,
            'current_menu' => 'properties'
        ]);
    }
}