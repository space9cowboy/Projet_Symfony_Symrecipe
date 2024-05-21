<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Form\IngredientType;
use App\Repository\IngredientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class IngredientController extends AbstractController
{
    /**
     * Cette fonction permet d'afficher tout les ingrédients
     *
     * @param IngredientRepository $repository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    #[Route('/ingredient', name: 'ingredient', methods: ['GET']) ]
    public function index(IngredientRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {
        $ingredients = $paginator->paginate(
            $repository->findAll(),
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );
        
        return $this->render('pages/ingredient/index.html.twig', [
            'ingredients' => $ingredients
        
        ]);
    }

    /**
     * Ce controlleur permet d'afficher un formulaire pour la création d'ingrédients
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/ingredient/new', name: 'ingredient.new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $manager) : Response 
    {
        $ingredient = new Ingredient();
        $form = $this->createForm(IngredientType::class, $ingredient);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $ingredient = $form->getData();
            
            //persist = commit
            $manager->persist($ingredient);
            //flush = push
            $manager->flush();

            //Ajout des messages flash
            $this->addFlash(
                'success',
                'Votre ingrédient a été crée avec succès :) '
            );

            //rediriger vers la page d'ingredient apres avoir crée unnouvel ingredient
            return $this->redirectToRoute('ingredient');

            
        }
        return $this->render('pages/ingredient/new.html.twig',[
            //Création du formulaire pour la vue 
            'form' => $form->createView()
        ]);
        
    }

    #[Route('/ingredient/edition/{id}', name: 'ingredient.edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Ingredient $ingredient, EntityManagerInterface $manager) : Response 
    {
        
        $form = $this->createForm(IngredientType::class, $ingredient);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $ingredient = $form->getData();
            
            //persist = commit
            $manager->persist($ingredient);
            //flush = push
            $manager->flush();

            //Ajout des messages flash
            $this->addFlash(
                'success',
                'Votre ingrédient a été modifié avec succès :) '
            );

            //rediriger vers la page d'ingredient apres avoir crée unnouvel ingredient
            return $this->redirectToRoute('ingredient');

            
        }
        

        return $this->render('pages/ingredient/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
