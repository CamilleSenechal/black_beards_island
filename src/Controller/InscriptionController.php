<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use function Sodium\add;

class InscriptionController extends AbstractController
{
    /**
     * @Route("/inscription", name="inscription")
     */
    public function inscription(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $form = $this->createFormBuilder()
            ->add('pseudo')
            ->add('email', EmailType::class)
            ->add('password', RepeatedType::class,[
                'type'=>PasswordType::class,
                'required' => true,
                'first_options' => ['label' => 'Password'],
                'second_options' => ['label' => 'Confirm Password']
            ])


            ->add('inscription', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-success float-right'
                ]
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted()){
            $data = $form->getData();
            $user = new User();
            $user->setPseudo($data['pseudo']);

            $user->setRoles(["ROLE_JOUEUR"]);

            $user->setEmail($data['email']);

            $user->setPassword(
                $passwordEncoder->encodePassword($user, $data['password'])
            );
            dump($user);


            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirect($this->generateUrl('app_login'));
        }

        return $this->render('inscription/index.html.twig', [
            'form' =>$form->createView()
        ]);
    }
}
