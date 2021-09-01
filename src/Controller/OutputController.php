<?php

namespace App\Controller;

use App\Entity\Output;
use App\Entity\Product;
use App\Form\OutputType;
use App\Repository\OutputRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/output")
 */
class OutputController extends AbstractController
{
    /**
     * @Route("/", name="output_index", methods={"GET"})
     */
    public function index(OutputRepository $outputRepository): Response
    {
        return $this->render('output/index.html.twig', [
            'outputs' => $outputRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="output_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $output = new Output();
        $output->setOutputDate(new \DateTime('today'));
        $form = $this->createFormBuilder($output)
            ->add('product', EntityType::class, [
                'class' => Product::class,
                'choice_label' => 'name'
            ])
            ->add('quantity', IntegerType::class)
            ->add('amount', NumberType::class)
            ->add('output_date', DateType::class)
            ->add('save', SubmitType::class, ['label' => 'Save'])
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($output);
            $entityManager->flush();

            return $this->redirectToRoute('output_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('output/new.html.twig', [
            'output' => $output,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="output_show", methods={"GET"})
     */
    public function show(Output $output): Response
    {
        return $this->render('output/show.html.twig', [
            'output' => $output,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="output_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Output $output): Response
    {
        $form = $this->createFormBuilder($output)
            ->add('product', EntityType::class, [
                'class' => Product::class,
                'choice_label' => 'name'
            ])
            ->add('quantity', IntegerType::class)
            ->add('amount', NumberType::class)
            ->add('output_date', DateType::class)
            ->add('save', SubmitType::class, ['label' => 'Save'])
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('output_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('output/edit.html.twig', [
            'output' => $output,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="output_delete", methods={"POST"})
     */
    public function delete(Request $request, Output $output): Response
    {
        if ($this->isCsrfTokenValid('delete'.$output->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($output);
            $entityManager->flush();
        }

        return $this->redirectToRoute('output_index', [], Response::HTTP_SEE_OTHER);
    }
    /**
     * @Route("/output/test", name="test_output", methods={"POST"})
     */
    public function test(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        return new JsonResponse($data, Response::HTTP_CREATED);
    }
}
