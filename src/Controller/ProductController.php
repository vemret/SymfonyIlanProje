<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\Product1Type;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/user/product")
 */
class ProductController extends AbstractController
{
    /**
     * @Route("/", name="user_product_index", methods={"GET"})
     */
    public function index(ProductRepository $productRepository): Response
    {
        $user = $this->getUser();
        return $this->render('product/index.html.twig', [
            'products' => $productRepository->findBy(['userid'=>$user->getId()]),
        ]);
    }

    /**
     * @Route("/new", name="user_product_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $product = new Product();
        $form = $this->createForm(Product1Type::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();

            /** @var file $file */
            $file = $form['image']->getData();
            if ($file) {
                $fileName = $this->generateUniqueFileName().'.'.$file->guessExtension();
                try{
                    $file->move(
                        $this->getParameter('images_directory'),//in services.yaml
                        $fileName
                    );
                } catch (FileException $e){

                }
                $product->setImage($fileName);//upload file
            }

            $user = $this->getUser();
            $product->setUserid($user->getId());
            $product->setStatus("New");
            $entityManager->persist($product);
            $entityManager->flush();

            return $this->redirectToRoute('user_product_index');
        }

        return $this->render('product/new.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_product_show", methods={"GET"})
     */
    public function show(Product $product): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="user_product_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Product $product): Response
    {
        $form = $this->createForm(Product1Type::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var file $file */
            $file = $form['image']->getData();
            if ($file) {
                $fileName = $this->generateUniqueFileName().'.'.$file->guessExtension();
                try{
                    $file->move(
                        $this->getParameter('images_directory'),//in services.yaml
                        $fileName
                    );
                } catch (FileException $e){

                }
                $product->setImage($fileName);//upload file
            }

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_product_index');
        }

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @return string
     */
    private function generateUniqueFileName(){
        //md5 reduces the similarity file names
        //uniqid whisc is based on timestamp
        return md5(uniqid());
    }

    /**
     * @Route("/{id}", name="user_product_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Product $product): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($product);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_product_index');
    }
}
