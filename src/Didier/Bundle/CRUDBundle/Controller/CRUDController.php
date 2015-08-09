<?php

namespace Didier\Bundle\CRUDBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class CRUDController extends Controller
{
    public function listAction(Request $request)
    {
        $config = $this->getObjectConfig($request);
        $class = $config->getClass();

        $objects = $this->get('didier_crud.connector.doctrine')->findAll($class);

        return $this->render('DidierCRUD:CRUD:list.html.twig', [
            'objects' => $objects,
        ]);
    }

    protected function getObjectConfig(Request $request)
    {
        $object = $request->attributes->get('_object');

        return $this->get('didier_crud.config')->getObject($object);
    }
}
