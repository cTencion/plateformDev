<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use Doctrine\Persistence\ManagerRegistry;

use App\Entity\Format;
use App\Form\FormatType;

/**
 * @Route("/format")
 */
class FormatController extends AbstractController
{
    /**
     * @Route("/", name="format_index")
     */
    public function index(ManagerRegistry $doctrine, Request $request): Response
    {
        $repository = $doctrine->getRepository(Format::class);
        $em = $doctrine->getManager();

        $formats = $repository->findBy([], ['name' => 'ASC']);

        $format = new Format();
        $form = $this->createForm(FormatType::class, $format);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $em->persist($format);
            $em->flush();
            return $this->redirectToRoute('format_index');
        }

        return $this->render('format/index.html.twig', [
            'formats' => $formats,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/ajouter", name="format_add")
     */
    public function add(ManagerRegistry $doctrine, Request $request): Response
    {
        $em = $doctrine->getManager();
        $format = new Format();
        $form = $this->createForm(FormatType::class, $format);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($format);
            $em->flush();
            return $this->redirectToRoute('format_index');
        }
        return $this->render('format/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/editer", name="format_edit", requirements = { "id" = "\d+" })
     */
    public function edit($id, ManagerRegistry $doctrine, Request $request): Response
    {
        $em = $doctrine->getManager();
        $repository = $doctrine->getRepository(Format::class);
        $format = $repository->find($id);
        $form = $this->createForm(FormatType::class, $format);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em->flush();
            return $this->redirectToRoute('format_index');
        }
        return $this->render('format/edit.html.twig', [
            'form' => $form->createView(),
            'format' => $format
        ]);
    }

     /**
     * @Route("/{id}/supprimer", name="format_delete", requirements = { "id" = "\d+" })
     */
    public function delete($id, ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $repository = $doctrine->getRepository(Format::class);
        $format = $repository->find($id);
        $em->remove($format);
        $em->flush();
        return $this->redirectToRoute('format_index');
    }
}
