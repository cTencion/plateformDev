<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use Doctrine\Persistence\ManagerRegistry;

use App\Entity\Folder;
use App\Form\FolderType;


/**
 * @Route("/dossier")
 */
class FolderController extends AbstractController
{
    /**
     * @Route("/", name="folder_index")
     */
    public function index(ManagerRegistry $doctrine, Request $request): Response
    {
        $repository = $doctrine->getRepository(Folder::class);
        $em = $doctrine->getManager();

        $folders = $repository->findBy([], ['name' => 'ASC']);

        $folder = new Folder();
        $form = $this->createForm(FolderType::class, $folder);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $em->persist($folder);
            $em->flush();
            return $this->redirectToRoute('folder_index');
        }

        return $this->render('folder/index.html.twig', [
            'folders' => $folders,
            'form' => $form->createview()
        ]);
    }

    /**
     * @Route("/{id}/editer", name="folder_edit", requirements = { "id" = "\d+" })
     */
    public function edit($id, ManagerRegistry $doctrine, Request $request): Response
    {
        $em = $doctrine->getManager();
        $repository = $doctrine->getRepository(Folder::class);
        $folder = $repository->find($id);
        $form = $this->createForm(FolderType::class, $folder);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em->flush();
            return $this->redirectToRoute('folder_index');
        }
        return $this->render('folder/edit.html.twig', [
            'form' => $form->createView(),
            'folder' => $folder
        ]);
    }

    /**
     * @Route("/{id}/supprimer", name="folder_delete", requirements = { "id" = "\d+" })
     */
    public function delete($id, ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $repository = $doctrine->getRepository(Folder::class);
        $folder = $repository->find($id);
        $em->remove($folder);
        $em->flush();
        return $this->redirectToRoute('folder_index');
    }

    /**
     * @Route("/{id}", name="folder_show", requirements = { "id" = "\d+" })
     */
    public function show($id, ManagerRegistry $doctrine): Response
    {
        $repository = $doctrine->getRepository(Folder::class);
        $folder = $repository->find($id);
        return $this->render('folder/show.html.twig', [
            'folder' => $folder
        ]);
    }
}
