<?php

namespace App\Controller;


use App\Entity\User;
use App\Repository\PropertyRepository;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Twig\Environment;
use App\Repository\UserRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class SecurityController extends AbstractController {


    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var ObjectManager
     */
    private $em;
    /**
     * @var \Swift_Mailer
     */
    private $mailer;
    /**
     * @var Environment
     */
    private $rendered;

    /**
     * SecurityController constructor.
     * @param UserRepository $userRepository
     * @param ObjectManager $em
     * @param \Swift_Mailer $mailer
     * @param Environment $rendered
     */
    public function __construct(UserRepository $userRepository, ObjectManager $em,\Swift_Mailer $mailer, Environment $rendered)
    {
        $this->userRepository = $userRepository;
        $this->em = $em;
        $this->mailer = $mailer;
        $this->rendered = $rendered;
    }


    /**
     * @Route(path="login" , name="login")
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils){
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('security/login.html.twig',[
            'lastUsername'=> $lastUsername,
            'error'=> $error
        ]);
    }

    /**
     * @Route(path="forgot-password" , name="forgotPassword")
     * @return Response
     */
    public function forgotPassword(){

        return $this->render('security/forgotPassword.html.twig',[
            'error'=>null
        ]);
    }

    /**
     * @Route(path="change-password" , name="changePassword")
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param UserRepository $userRepository
     * @param ObjectManager $em
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function changePassword(Request $request, UserPasswordEncoderInterface $passwordEncoder,UserRepository $userRepository,ObjectManager $em){

        $mail=$request->get('mail');
        $password=$request->get('password');
        $confirmPassword=$request->get('confirmPassword');
        if($password!=$confirmPassword){
            return $this->render('security/forgotPassword.html.twig',[
                'error'=> 'Confirm password'
            ]);
        }
        else if($password==$confirmPassword && $mail!=null){
            $user=$userRepository->findUserByEmail($mail);
            if($user==null) {
                return $this->render('security/forgotPassword.html.twig', [
                    'error' => 'this user doesn\'t exist'
                ]);
            }
            else{
                $user[0]->setPassword(
                    $passwordEncoder->encodePassword($user[0],$password)
                );
                $this->em->flush();
                $message = (new \Swift_Message('Changing password'))
                    ->setFrom('soufianemarzouk.2017@gmail.com')
                    ->setTo($mail)
                    ->setReplyTo($mail)
                    ->setBody($this->rendered->render('emails/passwordChange.html.twig',[
                        'user'=>$user[0]
                    ]),'text/html');
                $this->mailer->send($message);

                $this->addFlash('success','Your password has been changed successfuly, check you mail.');
            }
        }

        return $this->render('security/forgotPassword.html.twig',[
            'mail'=> $mail,
            'password'=> $password,
            'confirmPassword'=> $confirmPassword,
            'error'=> null
        ]);
    }
}