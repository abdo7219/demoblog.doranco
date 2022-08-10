<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BlogController extends AbstractController
{
    #[Route('/blog', name: 'app_blog')]
    // une route est définie par son chemin(/blog)
    public function index(ArticleRepository $repo): Response

    {
        $articles = $repo->findAll();
//j'utilise la méthode findAll()pour récup tous les article de la BBD
        return $this->render('blog/index.html.twig', [
            'tabArticles' => $articles,
        ]);
    }


    #[Route('/', name: 'home')]
    public function home(): Response
    {
        return $this->render('blog/home.html.twig', [
            'title' => 'Bienvenue sur le blog',
            'age' => 36
        ]);
    }

    #[Route('/blog/show/{id}', name: 'blog_show')]
    public function show($id, ArticleRepository $repo): Response

    //ArticleRepository c'est une classe,qui contient des metohode, et $repo represente l'objet
    //pour récupérer le repository, je le passe en paramètre index()
    //cela, s'appelle une injection de dépendance
    {
        $article =  $repo->find($id);

        return $this->render('blog/show.html.twig', [
            'article' => $article
        ]);
    }
    #[Route('/blog/new', name: 'blog_create')]
    #[Route('/blog/edit/{id}', name: 'blog_edit')]

    public function form(Request $superglobals, EntityManagerInterface $manager, Article $article = null)
    {
        // la classe Request contient les données véhiculées par les superglobales ($_POST, $_GET...)

        //dump($superglobals);
        // si symfony ne récuper pas d'objet Article, nous en créons un cvide

        if($article == null)    // équivalent à if(!$article)
        {
        
             $article = new Article;// je crée un objet Article vide prêt à être rempli
             $article->setCreatedAt(new \DateTime()); // ajouter la date seulement à l'nsertion d'un article
        }

       $form = $this->createForm(ArticleType::class, $article);// lier le formulaire à l objet
        //createForme()permet de récuperer tous les formulaire existant

        $form->handleRequest($superglobals);


        // handleRequest() permet d'insérer les données du formulaire dans l'objet $article
        // elle permet aussi de faire des vérifs sur formulaires(quelle est la méthode? est ce que les champs sont tous rempli ? etc)
        
        //dump($article);

if($form->isSubmitted() && $form->isValid())
{
    
    $manager->persist($article); //prépare la future requête
    $manager->flush(); // exécute la requête(insertion)
return $this->redirectToRoute('blog_show', [
    'id' => $article->getId()
]);
//cette méthode permet de redireger vers la page de notre article nouvellement crée
}

        return $this->renderForm("blog/form.html.twig",[
            'formArticle' => $form,
            'editMode' => $article->getId() !== NULL
        ]);

        // si nous sommes sur la route/new $article n'a pas encore id donc editMode = 0
        //sinon , editMode = 1
        //2eme méthode 
        //return $this->render("blog/form.html.twig",[
            //'formArticle' => $form->createView()
       // ]);
    }
    #[Route('/blog/delete/{id}', name:'blog_delete')]
// prépare la rout de suppression
    public function delete(EntityManagerInterface $manager, $id, ArticleRepository $repo)
    {
$article = $repo->find($id);

$manager->remove($article);
//remove prépare la supression d'un article

$manager->flush();
//exécute la requête préparée(suppression)

$this->addFlash('success', "l'article a bien été suprimé !");

//addFlash() permet de créer un msg de notification
//Il prend 2 argument  le 1er type de message(ce que l'un veut, pas de type prédéfini)
// le 2 eme arg est le message

return $this->redirectToRoute(("app_blog"));
//redirection vers la liste des articles après la suppression
//nous afficherons le message Flash sur le template affiché sur la route app_blog(index.html.twig)
    }
}
