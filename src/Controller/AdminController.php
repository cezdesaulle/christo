<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\Produit;
use App\Form\CategorieType;
use App\Form\ProduitType;
use App\Repository\CategorieRepository;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{

    /**
     * @Route("/listProduit", name="listProduit")
     */
    public function listproduit(ProduitRepository $produitRepository)
    {
        $produits = $produitRepository->findAll();

        return $this->render('admin/listProduit.html.twig', [
            'produits' => $produits
        ]);
    }


    /**
     * @Route ("/addProduit", name="addProduit")
     */
    public function addProduit(Request $request, EntityManagerInterface $manager)
    {

        // La classe Request de symfony httpfoundation est la classe qui récupére les données de toutes nos super_globales  ($_POST, $_GET, $_COOKIE).
        // L'interface EntityManagerInterface est obligatoire afin d'éxecuter une requete d'insert ou de modification ou de suppression.


        $produit = new Produit(); //ici on instancie un nouvel objet de la classe ou entité Produit qui est vide pour le moment

        $form = $this->createForm(ProduitType::class, $produit, array('add' => true));
        // ici on créé un objet formulaire en liens avec un formulaire de symfony (type) et qui va controller la correspondance des champs add() du formulaire avec la presence de ces propriétés dans l'entité Produit. createForm() attend 2 arguments minimum: le premier le nom du formulaire (du type auquel il fait référence et l'objet instancié correspondant à l'entité.)

        dump($request->request); // equivalent d'un var_dump($_POST), on pourra le consulter dans la barre de debug de symfony
        dump($request->cookies);// equivalent d'un var_dump($_COOKIES), on pourra le consulter dans la barre de debug de symfony
        dump($request->query);// equivalent d'un var_dump($_GET), on pourra le consulter dans la barre de debug de symfony
        $form->handleRequest($request); //  ici on utilise la méthode handleRequest() afin de recuperer les données soumises

        //a partir d'ici, si le formulaire a été soumis $produit qui était vide avant , est rempli des données du formulaire

        dump($form->getErrors());
        if ($form->isSubmitted() && $form->isValid()):

            $picture = $form->get('picture')->getData(); // on récupère notre input type file ($_FILES)
            //dd($picture);
            if ($picture):

                $pictureName = date('YmdHis') . "-" . uniqid() . "-" . $picture->getClientOriginalName();
                $picture->move($this->getParameter('pictures_directory'), $pictureName);

                $produit->setPicture($pictureName);
                $manager->persist($produit);// persist() consiste à la préparation de la requête qu'il conserve en mémoire tampon.
                $manager->flush(); // flush execute la requête et vide le tampon

                $this->addFlash("success", "Produit ajouté");

                return $this->redirectToRoute('listProduit');


            endif;


        endif;

        return $this->render('admin/addProduit.html.twig', [
            'form' => $form->createView()
        ]);

    }

    /**
     * @Route("/updateProduit/{id}", name="updateProduit")
     */
    public function updateProduit(Request $request, EntityManagerInterface $manager, Produit $produit)
    {


        $form = $this->createForm(ProduitType::class, $produit, array('update' => true));

        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()):



            $picture = $form->get('updatePicture')->getData(); // on récupère notre input type file ($_FILES)
            //dd($picture);
            if ($picture):

                $pictureName = date('YmdHis') . "-" . uniqid() . "-" . $picture->getClientOriginalName();
                $picture->move($this->getParameter('pictures_directory'), $pictureName);

                unlink($this->getParameter('pictures_directory').'/'.$produit->getPicture());
                $produit->setPicture($pictureName);
            endif;

            $manager->persist($produit);
            $manager->flush();


            $this->addFlash("success", "Produit modifié");
            return $this->redirectToRoute('listProduit');
        endif;


        return $this->render('admin/updateProduit.html.twig', [
            'form' => $form->createView(),
            'produit' => $produit
        ]);
    }


    /**
     * @Route("/deleteProduit/{id}", name="deleteProduit")  // ici on passe en get le parametre id du produit correspondant
     */
    public function deleteProduit(EntityManagerInterface $manager, Produit $produit)
    {  // lorsqu'on passe un id en parametre dans l'url, si on injecte en dépendance l'entité concerné la requete de find($id) est automatiquement effectuée et $produit est retourné chargé de ses informations provenant de la BDD


        // $produit=$produitRepository->find($id);  si on par le ProduitRepository

        $manager->remove($produit);  // la requete est préparée
        $manager->flush(); // la requete est executé
        $this->addFlash('success', 'Produit supprimé');


        return $this->redirectToRoute('listProduit');
    }





    /**
     * @Route("/addCategory", name="addCategory")
     * @Route ("/updateCategory/{id}", name="updateCategory")
     */
    public function addCategory(Request $request, EntityManagerInterface $manager, CategorieRepository $repository, $id = null)
    {

        if ($id == null):
            $categorie = new Categorie();
        else:
            $categorie = $repository->find($id);
        endif;

        $form = $this->createForm(CategorieType::class, $categorie);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()):


            $manager->persist($categorie);
            $manager->flush();

            if ($id == null):
                $this->addFlash('success', 'Catégorie ajoutée');
            else:
                $this->addFlash('success', 'Catégorie modifiée');
            endif;
            return $this->redirectToRoute('listCategory');


        endif;

        return $this->render('admin/category.html.twig', [
            'form' => $form->createView()

        ]);


    }

    /**
     * @Route("/listCategory", name="listCategory")
     */
    public function listCategory(CategorieRepository $repository)
    {
        $categories = $repository->findAll();

        return $this->render('admin/listCategory.html.twig', [
            'categories' => $categories

        ]);

    }

    /**
     * @Route("/deleteCategory/{id}", name="deleteCategory")
     */
    public function deleteCategory(Categorie $categorie, EntityManagerInterface $manager)
    {
        $manager->remove($categorie);
        $manager->flush();
        $this->addFlash('success', 'Catégorie supprimée');
        return $this->redirectToRoute('listCategory');


    }


}
