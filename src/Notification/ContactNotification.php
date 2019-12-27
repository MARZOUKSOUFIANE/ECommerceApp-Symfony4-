<?php

namespace App\Notification;

use App\Entity\Contact;
use Twig\Environment;

class ContactNotification{

    /**
     * @var \Swift_Mailer
     */
    private $mailer;
    /**
     * @var Environment
     */
    private $rendered;

    public function __construct(\Swift_Mailer $mailer, Environment $rendered)
    {
        $this->mailer = $mailer;
        $this->rendered = $rendered;
    }

    public function notify(Contact $contact){
        $message = (new \Swift_Message('Agence: '. $contact->getProperty()->getTitle()))
            ->setFrom($contact->getEmail())
            ->setTo('soufianemarzouk.2017@gmail.com')
            ->setReplyTo($contact->getEmail())
            ->setBody($this->rendered->render('emails/contact.html.twig',[
                'contact'=>$contact
            ]),'text/html');
        $this->mailer->send($message);
    }
}