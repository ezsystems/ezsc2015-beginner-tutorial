<?php

namespace EzSummer\BikeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('EzSummerBikeBundle:Default:index.html.twig', array('name' => $name));
    }
}
