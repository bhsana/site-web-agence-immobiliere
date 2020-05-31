<?php


namespace App\Controller\Admin;

use App\Entity\Property;
use App\Form\PropertyType;
use App\Repository\PropertyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminPropertyController extends AbstractController
{
    /**
     * @var PropertyRepository
     */
    private $repository;

    /**
     * @var ObjectManager
     */
    private $em;

    public function __construct(PropertyRepository $repository, EntityManagerInterface $em)
    {
        $this->repository = $repository;
        $this->em = $em;
    }

    /**
     * Interface principale de gestion des biens
     * @Route("/admin",name="admin.property.index")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index()
    {
        //Récupération de la liste des biens
        $properties = $this->repository->findLatest();
        //nbr des bien non vendus
        $nbrAvailable = $this->repository->getCount(0);
        //nbr des biens vendus
        $nbrnotAvailable = $this->repository->getCount(1);
        //nbr total des biens
        $nbrTotal = count($this->repository->findAll());
        return $this->render("admin/property/index.html.twig", [
            'properties' => $properties,
            'countTotal' => $nbrTotal,
            'countSold' => $nbrnotAvailable,
            'countNotSold' => $nbrAvailable
        ]);
    }

    /**
     * Interface de la liste des biens
     * @Route("/admin/list",name="admin.property.list")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function list()
    {
        //Récupération de la liste des biens
        $properties = $this->repository->findAll();
        return $this->render("admin/property/list.html.twig",
            ['properties' => $properties,
                'countTotal' => count($properties),
            ]);
    }

    /**
     * Ajout d'un nouveau bien
     * @param Request $request
     * @Route("/admin/property/create", name="admin.property.new")
     */
    public function new(Request $request)
    {
        $property = new Property();
        //Création du formulaire
        $form = $this->createForm(PropertyType::class, $property);
        //Sauvegarde des données du formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($property);
            //Créarion d'une instance du formulaire
            $this->em->flush();
            $this->addFlash('success', 'Bien ajouté avec succès');

            return $this->redirectToRoute("admin.property.list");
        }

        return $this->render("admin/property/new.html.twig", [
            'property' => $property,
            'form' => $form->createView(),
            'countTotal' => count($this->repository->findAll())

        ]);
    }


    /**
     * interface d'édition du bien
     * @Route("/admin/property/{id}",name="admin.property.edit", methods="GET|POST")
     * @param Property $property
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function edit(Property $property, Request $request)
    {
        //Création du formulaire
        $form = $this->createForm(PropertyType::class, $property);
        //Sauvegarde des données du formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            $this->addFlash('success', 'Bien modifié avec succès');
            return $this->redirectToRoute("admin.property.list");
        }

        return $this->render("admin/property/edit.html.twig", [
            'property' => $property,
            'form' => $form->createView(),
            'countTotal' => count($this->repository->findAll())
        ]);
    }

    /**
     * Suppression d'un bien
     * @param Property $property
     * @Route("/admin/property/{id}",name="admin.property.remove", methods="DELETE")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function delete(Property $property, Request $request)
    {
        if ($this->isCsrfTokenValid('DELETE' . $property->getId(), $request->get("_token"))) {

            $this->em->remove($property);
            $this->em->flush();
            $this->addFlash('success', 'Bien supprimé avec succès');

        }
        return $this->redirectToRoute("admin.property.list");

    }

}