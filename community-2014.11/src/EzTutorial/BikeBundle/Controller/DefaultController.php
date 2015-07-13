<?php

namespace EzTutorial\BikeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('EzTutorialBikeBundle:Default:index.html.twig', array('name' => $name));
    }
}
