<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Article;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Form\ArticleType;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;




class BlogController extends AbstractController
{
    /**
     * @Route("/blog", name="blog")
     */
    public function index()
    {
        $repo = $this->getDoctrine()->getRepository(Article::class);
        $articles = $repo->findAll();
        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController',
            'articles' => $articles
        ]);
    }
    /**
     * @Route("/",name="home")
     */
    public function home(){
        return $this->render('blog/home.html.twig',[
            'title' =>'Bienvenue sur mon blog'
            
        ]);
    }
    /**
     * @Route("/blog/new",name="blog_create")
     * @Route("blog/{id}/edit", name="blog_edit")
     */
    public function form(Article $article = NULL,Request $request, ObjectManager $manager){
       if(!$article){
        $article = new Article();
       }

       //possibilité de faire le form comme ca

        //  $form = $this->createFormBuilder($article)
        
        //champ titre
        //    ->add('title', TextType::class,[
        //      'attr' =>[
        //        'placeholder' => "titre de l'article",
        //      'class' => 'form-control'
        //    ]
        //   ])
        
        //champ contenu
        // ->add('content', TextareaType::class,[
        //   'attr' =>[
        //     'placeholder' => "contenu de l'article",
        //   'class' => 'form-control'
        //]
        // ])
        
        //champ image
        // ->add('image', TextType::class,[
        //   'attr' =>[
        //     'placeholder' => "image de l'article",
        //   'class' => 'form-control'
        //]
        //])
           
        //btn valider(possibilité de faire comme ca)
            // ->add('save', SubmitType::class,[
            //   'label' => 'enregistrer'
            //])
           // ->getForm();

        // appel ArticleType crée par ligne de commande
           $form = $this->createForm(ArticleType::class, $article);

            //verification des champs
            $form->handleRequest($request);

            //si le formulaire est ok enregistrer dans la bdd
            if($form->isSubmitted() && $form->isValid()){
                //date de l'article
                if(!$article->getId()){
                $article->setCreatedAt(new \DateTime());
                }
                $manager->persist($article);
                //tout est ok envoi des données
                $manager->flush();

                return $this->redirectToRoute('blog_show',['id'=>$article->getId() ]);
            }

        return $this->render('blog/create.html.twig',[
            'formArticle' => $form->createView(),

            //boleen pour savoir si article est true ou false(btn)
            'editMode' => $article->getId() !== null
        ]);
    }
    
    /**
     * @route("/blog/article/{id}",name="blog_show")
     */
    public function show(Article $article, Request $request, ObjectManager $manager){
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        
        $form->handleRequest($request);
        //si le form est valid tu le flush dans la bdd
        if($form->isSubmitted() && $form->isValid()){

        //avant de poster un commentaire expliquer à ce commentaire  qui vient d'être créé et a quel article il est relié
       $comment->setCreatedAt(new \DateTime())
               ->setArticle($article);
       
       
        $manager->persist($comment);
        $manager->flush();

          return $this->redirectToRoute('blog_show', ['id' =>$article->getId()]);        
        
        }

        return $this->render('blog/show.html.twig',[
            'article' => $article,
            'commentForm' => $form->createView()
        ]);
       
    }
    
}
