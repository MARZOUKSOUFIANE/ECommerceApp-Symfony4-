<?php

namespace App\Controller\Admin;

use App\Entity\Criteria;
use App\Form\CriteriaType;
use App\Repository\CriteriaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/criteria")
 */
class AdminCriteriaController extends AbstractController
{
    /**
     * @Route("/", name="admin.criteria.index", methods={"GET"})
     */
    public function index(CriteriaRepository $criteriaRepository): Response
    {
        return $this->render('admin/criteria/index.html.twig', [
            'criterias' => $criteriaRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="admin.criteria.new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $criterion = new Criteria();
        $form = $this->createForm(CriteriaType::class, $criterion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($criterion);
            $entityManager->flush();
            $this->addFlash('success','Option ajoutee avec succe');

            return $this->redirectToRoute('admin.criteria.index');
        }

        return $this->render('admin/criteria/create.html.twig', [
            'criterias' => $criterion,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/{id}/edit", name="admin.criteria.edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Criteria $criterion): Response
    {
        $form = $this->createForm(CriteriaType::class, $criterion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success','Option modifiee avec succe');


            return $this->redirectToRoute('admin.criteria.index', [
                'id' => $criterion->getId(),
            ]);
        }

        return $this->render('admin/criteria/edit.html.twig', [
            'criterias' => $criterion,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="admin.criteria.delete", methods={"DELETE"})
     */
    public function delete(Request $request, Criteria $criterion): Response
    {
        if ($this->isCsrfTokenValid('delete'.$criterion->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($criterion);
            $entityManager->flush();
            $this->addFlash('success','Option supprimee avec succe');

        }

        return $this->redirectToRoute('admin.criteria.index');
    }
}
