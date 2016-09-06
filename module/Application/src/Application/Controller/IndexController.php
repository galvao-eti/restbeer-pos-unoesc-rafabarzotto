<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        $beers = $this->getServiceLocator()
                      ->get('Application\Model\BeerTableGateway')
                      ->fetchAll();
        return new ViewModel(array('beers' => $beers));
    }

    public function insertAction()
    {
        $form = $this->getServiceLocator()->get('Application\Form\Beer');
        $form->setAttribute('action', '/insert');
        $tableGateway = $this->getServiceLocator()->get('Application\Model\BeerTableGateway');
        $beer = new \Application\Model\Beer;
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($beer->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                /* pega os dados validados e filtrados */
                $data = $form->getData();
                /* preenche os dados do objeto Post com os dados do formulário*/
                $beer->exchangeArray($data);
                /* salva o novo post*/
                $tableGateway->save($beer);
                /* redireciona para a página inicial que mostra todos os posts*/
                return $this->redirect()->toUrl('/');
            }
        }

        $id = (int) $this->params()->fromRoute('id', 0);
        if ($id > 0) { 
            //é uma atualização   
            /* busca a entidade no banco de dados*/
            $post = $tableGateway->get($id);
            //echo $post;
            /* preenche o formulário com os  dados do banco de dados*/
            $form->bind($post);
            /* muda o texto do botão submit*/
            $form->get('send')->setAttribute('value', 'Editar');
        }

        return new ViewModel(['beerForm' => $form]);
    }

    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if ($id == 0) {
            throw new \Exception("Código obrigatório");
        }

        $tableGateway = $this->getServiceLocator()->get('Application\Model\BeerTableGateway');
        /* remove o registro e redireciona para a página inicial*/
        $tableGateway->delete($id);
        
        return $this->redirect()->toUrl('/');
    }
}
