<?php

namespace Herman\WebAppTestBundle\Controller;

use Herman\WebAppTestBundle\Entity\EmailSubscriber;

use Herman\WebAppTestBundle\Form\EmailSubscriberType;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    /**
     * 
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function subscribeEmailAction(Request $request)
    {
        $form = $this->createForm(new EmailSubscriberType(), new EmailSubscriber());
        
        return $this->render('WebAppTestBundle:Default:subscribeEmail.html.twig', array('form' => $form->createView()));
    }
    
    
    public function saveEmailAction(Request $request)
    {
        $form = $this->createForm(new EmailSubscriberType(), new EmailSubscriber());
        $form->bind($request);
        if ($form->isValid()) {
            
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($form->getData());
            $em->flush();
            
            $request->getSession()->setFlash('success', 'Email successfully subscribed');
            
            return $this->redirect($this->generateUrl('web_app_test_subscribe_email'));
        }
        else {
            return $this->render('WebAppTestBundle:Default:subscribeEmail.html.twig', array('form' => $form->createView()));
        }
    }
}
