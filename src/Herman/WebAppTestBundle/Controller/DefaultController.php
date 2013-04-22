<?php

namespace Herman\WebAppTestBundle\Controller;

use Herman\WebAppTestBundle\Entity\UserStatus;

use Herman\WebAppTestBundle\Entity\User;

use Herman\WebAppTestBundle\Form\UserType;

use Herman\WebAppTestBundle\Entity\EmailSubscriber;

use Herman\WebAppTestBundle\Form\EmailSubscriberType;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('WebAppTestBundle:Default:index.html.twig');
    }
    
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
    
    
    /**
     * Save the email subsriber
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function saveEmailAction(Request $request)
    {
        $form = $this->createForm(new EmailSubscriberType(), new EmailSubscriber());
        $form->bind($request);
        if ($form->isValid()) {
            
            // persist data
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($form->getData());
            $em->flush();
            
            // flash message
            $request->getSession()->setFlash('success', 'Email successfully subscribed');
            
            return $this->redirect($this->generateUrl('web_app_test_subscribe_email'));
        }
        else {
            return $this->render('WebAppTestBundle:Default:subscribeEmail.html.twig', array('form' => $form->createView()));
        }
    }
    
    /**
     * Registration action
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function registrationAction()
    {
        $form = $this->createForm(new UserType(), new User());
        
        return $this->render('WebAppTestBundle:Default:registration.html.twig', array('form' => $form->createView()));
    }
    
    /**
     * Save user registration
     * 
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function saveRegistrationAction(Request $request)
    {
        $form = $this->createForm(new UserType(), new User());
        $form->bind($request);
        
        if ($form->isValid()) {
            
            $user = $form->getData();
            
            // generate registration token
            $token = md5(time(). $this->container->getParameter('secret'));
            
            // set user status to inactive
            $user->setStatus(UserStatus::INACTIVE);
            
            // set registration token
            $user->setRegistrationToken($token);
            
            // persist data
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($user);
            $em->flush();
            
            // generate activation link
            $activationLink = $this->generateUrl(
                'web_app_test_user_activation',
                array('id' => $user->getId(), 'token' => $user->getRegistrationToken()),
                true
            );
            
            // send mail
            $body = 'This is a test activation email. To activate please click on the link: '.$activationLink;
            $message = \Swift_Message::newInstance()
                ->setSubject('Web app test application')
                ->setFrom('kiamdeveloper@gmail.com', 'Kikiam')
                ->setTo($user->getEmail(), $user->getFullName())
                ->setBody($body);
            
            $this->get('mailer')->send($message);
            
            // set flash
            $request->getSession()->setFlash('success', 'An activation link was sent to your email. Just for demo if email was not sent thru local mail: this is the activation link: <a href="'.$activationLink.'">click here</a>');
            
            return $this->redirect($this->generateUrl('web_app_test_registration'));
        }
    
        return $this->render('WebAppTestBundle:Default:registration.html.twig', array('form' => $form->createView()));
    }
    
    /**
     * 
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function activateUserAction(Request $request)
    {
        $user = $this->getDoctrine()->getRepository('WebAppTestBundle:User')->find($request->get('id', 0));
        if (!$user) {
            throw $this->createNotFoundException('Invalid user');
        }
        
        // check if token is equal to user's token
        // additional token check can be implemented here, or in a separate authentication service
        $token = $request->get('token', null);
        if ($token && $token == $user->getRegistrationToken()){
            
            // set registration token to null so it won't be accessible again
            $user->setRegistrationToken(null);
            // activate status
            $user->setStatus(UserStatus::ACTIVE);
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($user);
            $em->flush();
            
            $request->getSession()->setFlash('success', 'Welcome '.$user->getFullName().'! Your account is now fully activated.');
            
            return $this->redirect($this->generateUrl('web_app_test_homepage'));
        }
        else {
            throw $this->createNotFoundException('Token is not valid');
        }
    }
    
}
