<?php

namespace App\Controller;

use App\Entity\Color;
use App\Entity\Input;
use App\Entity\Product;
use App\Entity\Size;
use App\Form\InputType;
use App\Repository\InputRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/input")
 */
class InputController extends AbstractController
{
    /**
     * @Route("/", name="input_index", methods={"GET"})
     */
    public function index(InputRepository $inputRepository): Response
    {
        return $this->render('input/index.html.twig', [
            'inputs' => $inputRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="input_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $input = new Input();
        $input->setInputDate(new \DateTime('today'));
        $form = $this->createFormBuilder($input)
            ->add('product', EntityType::class, [
                'class' => Product::class,
                'choice_label' => 'name'
            ])
            ->add('quantity', IntegerType::class)
            ->add('amount', NumberType::class)
            ->add('input_date', DateType::class)
            ->add('save', SubmitType::class, ['label' => 'Save'])
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($input);
            $entityManager->flush();

            return $this->redirectToRoute('input_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('input/new.html.twig', [
            'input' => $input,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="input_show", methods={"GET"})
     */
    public function show(Input $input): Response
    {
        return $this->render('input/show.html.twig', [
            'input' => $input,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="input_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Input $input): Response
    {
        $form = $this->createFormBuilder($input)
            ->add('product', EntityType::class, [
                'class' => Product::class,
                'choice_label' => 'name'
            ])
            ->add('quantity', IntegerType::class)
            ->add('amount', NumberType::class)
            ->add('input_date', DateType::class)
            ->add('save', SubmitType::class, ['label' => 'Save'])
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('input_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('input/edit.html.twig', [
            'input' => $input,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="input_delete", methods={"POST"})
     */
    public function delete(Request $request, Input $input): Response
    {
        if ($this->isCsrfTokenValid('delete'.$input->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($input);
            $entityManager->flush();
        }

        return $this->redirectToRoute('input_index', [], Response::HTTP_SEE_OTHER);
    }
}
