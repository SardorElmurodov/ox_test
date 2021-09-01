<?php

namespace App\Controller;

use App\Entity\Size;
use App\Repository\ColorRepository;
use App\Repository\InputRepository;
use App\Repository\OutputRepository;
use App\Repository\ProductRepository;
use App\Repository\SizeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class RestController extends AbstractController
{
    private $sizeRepository;
    private $colorRepository;
    private $productRepository;
    private $inputRepository;
    private $outputRepository;

    public function __construct(SizeRepository $sizeRepository, ColorRepository $colorRepository, ProductRepository $productRepository, InputRepository $inputRepository, OutputRepository $outputRepository)
    {
        $this->sizeRepository = $sizeRepository;
        $this->colorRepository = $colorRepository;
        $this->productRepository = $productRepository;
        $this->inputRepository = $inputRepository;
        $this->outputRepository = $outputRepository;
    }
    /**
     * @Route("/rest/sizes", name="rest_size", methods={"POST"})
     */
    public function sizes(Request $request): JsonResponse
    {
        $sizes = $this->sizeRepository->findAll();
        $data = [];

        foreach ($sizes as $size) {
            $data[] = [
                'id' => $size->getId(),
                'name' => $size->getName(),
            ];
        }

        return new JsonResponse($data, Response::HTTP_OK);
    }

    /**
     * @Route("/rest/colors", name="rest_color", methods={"POST"})
     */
    public function colors(Request $request): JsonResponse
    {
        $colors = $this->colorRepository->findAll();
        $data = [];

        foreach ($colors as $color) {
            $data[] = [
                'id' => $color->getId(),
                'name' => $color->getName(),
            ];
        }

        return new JsonResponse($data, Response::HTTP_OK);
    }

    /**
     * @Route("/rest/products", name="rest_product", methods={"POST"})
     */
    public function products(Request $request): JsonResponse
    {
        $products = $this->productRepository->findAll();
        $data = [];

        foreach ($products as $product) {
            $data[] = [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'color' => $product->getColor()->getName(),
                'size' => $product->getSize()->getName(),
            ];
        }

        return new JsonResponse($data, Response::HTTP_OK);
    }

    /**
     * @Route("/rest/input", name="rest_input", methods={"POST"})
     */
    public function input(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $inputs = $this->inputRepository->createQueryBuilder('i')
            ->andWhere('i.product = :val')
            ->setParameter('val', $data['product_id'])
            ->orderBy('i.id', 'ASC')
            ->getQuery()
            ->getResult();
        $data = [];

        foreach ($inputs as $i) {
            $data[] = [
                'id' => $i->getId(),
                'product' => $i->getProduct()->getName(),
                'quantity' => $i->getQuantity(),
                'amount' => $i->getAmount(),
                'date' => $i->getInputDate()->format('d.m.Y'),
            ];
        }

        return new JsonResponse($data, Response::HTTP_OK);
    }

    /**
     * @Route("/rest/add-input", name="add_input", methods={"POST"})
     */
    public function addInput(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $product_id = $data['product_id'];
        $quantity = $data['quantity'];
        $amount = $data['amount'];
        $date = $data['date'];

        if (empty($product_id) || empty($quantity) || empty($amount) || empty($date)) {
            throw new NotFoundHttpException('Expecting mandatory parameters!');
        }

        $product = $this->productRepository->findOneBy(['id' => $product_id]);

        if(!$product) {
            throw new NotFoundHttpException('Product not found!');
        }

        $this->inputRepository->saveInput($product, $quantity, $amount, $date);

        return new JsonResponse(['status' => 'Input created!'], Response::HTTP_CREATED);
    }

    /**
     * @Route("/rest/output", name="rest_output", methods={"POST"})
     */
    public function output(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $outputs = $this->outputRepository->createQueryBuilder('o')
            ->andWhere('o.product = :val')
            ->setParameter('val', $data['product_id'])
            ->orderBy('o.id', 'ASC')
            ->getQuery()
            ->getResult();
        $data = [];

        foreach ($outputs as $o) {
            $data[] = [
                'id' => $o->getId(),
                'product' => $o->getProduct()->getName(),
                'quantity' => $o->getQuantity(),
                'amount' => $o->getAmount(),
                'date' => $o->getOutputDate()->format('d.m.Y'),
            ];
        }

        return new JsonResponse($data, Response::HTTP_OK);
    }

    /**
     * @Route("/rest/add-output", name="add_output", methods={"POST"})
     */
    public function addOutput(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $product_id = $data['product_id'];
        $quantity = $data['quantity'];
        $amount = $data['amount'];
        $date = $data['date'];

        if (empty($product_id) || empty($quantity) || empty($amount) || empty($date)) {
            throw new NotFoundHttpException('Expecting mandatory parameters!');
        }

        $product = $this->productRepository->findOneBy(['id' => $product_id]);

        if(!$product) {
            throw new NotFoundHttpException('Product not found!');
        }

        $this->outputRepository->saveOutput($product, $quantity, $amount, $date);

        return new JsonResponse(['status' => 'Output created!'], Response::HTTP_CREATED);
    }
}
