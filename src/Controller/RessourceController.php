<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use Doctrine\Persistence\ManagerRegistry;

use App\Entity\Ressource;
use App\Form\RessourceType;


class RessourceController extends AbstractController
{
    /**
     * @Route("/", name="ressource_index")
     */
    public function index(ManagerRegistry $doctrine): Response
    {
        $repository = $doctrine->getRepository(Ressource::class);
        $ressources = $repository->findBy([], ['name' => 'ASC']);
        return $this->render('ressource/index.html.twig',[
            'ressources' => $ressources
        ]);
    }

    /**
     * @Route("/ajouter", name="ressource_add")
     */
    public function add(ManagerRegistry $doctrine, Request $request): Response
    {
        $em = $doctrine->getManager();
        $ressource = new Ressource();
        $form = $this->createForm(RessourceType::class, $ressource);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($ressource);
            $em->flush();
            return $this->redirectToRoute('ressource_index');
        }
        return $this->render('ressource/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/editer", name="ressource_edit", requirements = { "id" = "\d+" })
     */
    public function edit($id, Request $request, ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $repository = $doctrine->getRepository(Ressource::class);
        $ressource = $repository->find($id);
        $form = $this->createForm(RessourceType::class, $ressource);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('ressource_index', [
                'id' => $id
            ]);
        }
        return $this->render('ressource/edit.html.twig', [
            'form' => $form->createView(),
            'ressource' => $ressource
        ]);
    }

    /**
     * @Route("/{id}/supprimer", name="ressource_delete", requirements = { "id" = "\d+" })
     */
    public function delete($id, ManagerRegistry $doctrine)
    {
        $em = $doctrine->getManager();
        $repository = $doctrine->getRepository(Ressource::class);
        $ressource = $repository->find($id);
        $em->remove($ressource);
        $em->flush();
        return $this->redirectToRoute('ressource_index');
    }
}
