<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\User;
use AppBundle\Entity\Gallery;
use AppBundle\Entity\Image;
use AppBundle\Entity\Role;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

#use Symfony\Component\Security\Core\Role\Role;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
        ]);
    }


    /**
     * @Route("/new/user")
     */

    public function addUser()
    {
        $role = new Role("ROLE_GALLERY_ADD");
        $user = new User();
        $user->setUsername('tru_gal_admin');
        $user->setPlainPassword('admin');
        $user->setEmail('tru_gal_admin@admin.com');
        $user->setEnabled(true);
        $user->addRole($role);

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        return new Response('Dodano nowego użytkownika o ID '.$user->getId());
    }

    /**
     * @Route("/show/users")
     */

    public function showUsers()
    {
       $repository = $this->getDoctrine()->getRepository('AppBundle:User');
       $users = $repository->findAll(); 
      
       return $this->render('show/users.html.twig', array(
           'users' => $users,
       ));
 
    }

    /**
     * @Route("/show/user/{user_id}", requirements={"page": "\d+"})
     */

    public function showUser(Request $request)
    {
       $repository = $this->getDoctrine()->getRepository('AppBundle:User');
       //$request = $this->getRequest();
       $user_id = $request->attributes->get('user_id');
       $user = $repository->findOneById($user_id);
      
       return $this->render('show/user.html.twig', array(
           'user' => $user,
       ));
 
    }

   /**
     * @Route("/add/gallery")
     */

    public function addGallery(Request $request)
    {
     
       $gallery = new Gallery();
       $form = $this->createFormBuilder($gallery)
            ->add('name', TextType::class)
            ->add('description', TextType::class)
            ->add('save', SubmitType::class, array('label' => 'Dodaj galerię'))
            ->getForm();
       
   
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
        // $form->getData() holds the submitted values
        // but, the original `$task` variable has also been updated
        $task = $form->getData();
        
        $em = $this->getDoctrine()->getManager();
        $em->persist($gallery);
        $em->flush();

        //return $this->redirectToRoute('show/galleries');
    }

        return $this->render('add/gallery.html.twig', array(
            'form' => $form->createView(),
        ));

    }

    /**
     * @Route("/show/galleries")
     */

    public function showGallery()
    {
     
       $repository = $this->getDoctrine()->getRepository('AppBundle:Gallery');
       $galleries = $repository->findAll(); 
    
       //print_r($galleries);

       return $this->render('show/galleries.html.twig', array(
           'galleries' => $galleries,
       ));
    }


    /**
     * @Route("/add/image")
     */

    public function addImage(Request $request)
    {
       $repository = $this->getDoctrine()->getRepository('AppBundle:Gallery');
       $galleries = $repository->findAll(); 

       $image = new Image();
       $form = $this->createFormBuilder($image)
            ->add('name', TextType::class)
            ->add('description', TextType::class)
            ->add('gallery', EntityType::class, array(
              'class' => 'AppBundle:Gallery',
              'choice_label' => 'name',
            ))
            ->add('full_size', FileType::class)
            ->add('save', SubmitType::class, array('label' => 'Dodaj obrazek'))
            ->getForm();
       
   
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
        // $form->getData() holds the submitted values
        // but, the original `$task` variable has also been updated
        $image = $form->getData();
       
        $file = $image->getFullSize();

        // Generate a unique name for the file before saving it
        $fileName = md5(uniqid()).'.'.$file->guessExtension();

        // Move the file to the directory where brochures are stored
        $file->move(
            $this->getParameter('images_directory'),
            $fileName
        );

        // Update the 'brochure' property to store the PDF file name
        // instead of its contents
        $image->setFullSize($fileName);        
        $image->setSmallSize($fileName);        
        $image->setAuthor($this->get('security.context')->getToken()->getUser());       

        $em = $this->getDoctrine()->getManager();
        $em->persist($image);
        $em->flush();

        //return $this->redirectToRoute('show/galleries');
    }

        return $this->render('add/image.html.twig', array(
            'form' => $form->createView(),
        ));

    }

    /**
     * @Route("/show/images")
     */

    public function showImages()
    {
     
       $repository = $this->getDoctrine()->getRepository('AppBundle:Image');
       $images = $repository->findAll(); 
    
       //print_r($galleries);

       return $this->render('show/images.html.twig', array(
           'images' => $images,
       ));
    }

}
