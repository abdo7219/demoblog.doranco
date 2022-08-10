<?php

namespace App\DataFixtures;

use App\Entity\Article;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class ArticleFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        for ($i = 1; $i <= 10; $i++)
        {
            $article = new Article;

            $article->setTitle("Titre de l'article n°$i")->setContent("<p>Contenu de l'article n°$i<br>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Earum placeat excepturi sed aspernatur! Harum molestias, qui quo nesciunt a voluptates eum. Corrupti neque aliquam inventore beatae. Consequatur saepe non excepturi.</p>")->setImage("http://picsum.photos/250/150")->setCreatedAt(new \DateTime());

            $manager->persist($article);
        }

        $manager->flush();
    }
}




