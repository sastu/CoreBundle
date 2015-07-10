<?php

namespace Core\Bundle\CoreBundle\Service;

use Core\Bundle\CoreBundle\Entity\Actor;
use Symfony\Bridge\Twig\Extension;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Core\Bundle\EcommerceBundle\Entity\Invoice;
use Core\Bundle\EcommerceBundle\Entity\Transaction;
use Core\Bundle\CoreBundle\Entity\Optic;

/**
 * Class Mailer
 */
class Mailer
{
    private $mailer;
    private $twig;
    private $parameters;
    private $router;
    private $templating;
    private $html2pdf_factory;
    private $kernel;

    /**
     * @param \Swift_Mailer     $mailer
     * @param \Twig_Environment $twig
     * @param array             $parameters
     */
    public function __construct(
            \Swift_Mailer $mailer, 
            \Twig_Environment $twig, 
            array $parameters, 
            $router,
            $templating,
            $html2pdf_factory,
            $kernel
            )
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->parameters = $parameters['parameters'];
        $this->router = $router;
        $this->templating = $templating;
        $this->html2pdf_factory = $html2pdf_factory;
        $this->kernel = $kernel;
    }

    /**
     * Send an email to a user to confirm the account creation
     *
     * @param UserInterface $user
     *
     * @return void
     */
    public function sendRegisteredEmailMessage(Actor $user)
    {
        $templateName = 'FrontBundle:Registration:email.html.twig';

        $context = array(
            'user' => $user
        );

        $this->sendMessage(
                $templateName, 
                $context,  
                $this->parameters['company']['email'], 
                $user->getEmail()
                );
    }
    
    public function sendOpticRegisteredEmailMessage(Optic $optic) 
    {
        
        //envio el email con la clave
        $message = \Swift_Message::newInstance()
            ->setSubject('Bienvenido a optisoop')
            ->setFrom(array('optisoop@optisoop.com' => 'Core avisos'))
            ->setTo($optic->getEmail())
            ->setBody(
                $this->templating->render(
                    'FrontBundle:Registration:register.email.optic.html.twig',
                    array(
                        'nombre' => $optic->getName(),
                        'email' => $optic->getEmail(),
                        'hss_validacion' => md5($optic->getEmail().'optisoopkey'.$optic->getId()),
                    )
                )
            , 'text/html')
        ;
       
        //Presencia en la plataforma
        if($optic->getPack()->getId() == 2){
            $html = $this->templating->render(
                'FrontBundle:Registration/Contract:plataforma.html.twig', 
                array(
                    'optic' => $optic,
                ));

            $html2pdf = $this->html2pdf_factory->create();
            $html2pdf->WriteHTML($html);

            $filename = 'contrato_presencia-plataforma_'.$optic->getId().'-'.$optic->getPack()->getId().'.pdf';
            $fileDir = $this->kernel->getRootDir().'/../web/uploads/documents/'.$filename;
            $html2pdf->Output($fileDir, 'F');
            $message->attach(\Swift_Attachment::fromPath($fileDir));
        }
        //Pack Internet Plus
        if($optic->getPack()->getId() == 3){
            $html = $this->templating->render(
                'FrontBundle:Registration/Contract:plus.html.twig', 
                array(
                    'optic' => $optic,
                ));

            $html2pdf = $this->html2pdf_factory->create();
            $html2pdf->WriteHTML($html);

            $filename = 'contrato_internet-plus_'.$optic->getId().'-'.$optic->getPack()->getId().'.pdf';
            $fileDir = $this->kernel->getRootDir().'/../web/uploads/documents/'.$filename;
            $html2pdf->Output($fileDir, 'F');
            $message->attach(\Swift_Attachment::fromPath($fileDir));

        }
        //Pack Internet Total
        if($optic->getPack()->getId() == 4){
            $html = $this->templating->render(
                'FrontBundle:Registration/Contract:total.html.twig', 
                array(
                    'optic' => $optic,
                ));

            $html2pdf = $this->html2pdf_factory->create();
            $html2pdf->WriteHTML($html);

            $filename = 'contrato_internet-total_'.$optic->getId().'-'.$optic->getPack()->getId().'.pdf';
            $fileDir = $this->kernel->getRootDir().'/../web/uploads/documents/'.$filename;
            $html2pdf->Output($fileDir, 'F');
            $message->attach(\Swift_Attachment::fromPath($fileDir));
        }

        $this->mailer->send($message);
    
    }
    
    public function sendValidateEmailMessage(Actor $user)
    {
        $fromEmail =  $this->parameters['company']['email'];
        $toEmail = $user->getEmail();
        $templateName = 'CoreBundle:Registration:validate.email.html.twig';
        
        $context = array(
            'name' => $user->getUsername(),
        );
        
        $this->sendMessage($templateName, $context, $fromEmail, $toEmail);
            
    }
    
    /**
     * Send an email to a user and admin
     *
     * @param array $params (name, email, message)
     *
     * @return void
     */
    public function sendContactMessage(array $params)
    {
        $templateName = 'CoreBundle:Email:base.email.html.twig';

        $context = array(
            'params' => $params
        );
        
        $this->sendMessage(
                $templateName, 
                $context,  
                $this->parameters['company']['email'], 
                $params['email']
                );
        
        $this->sendMessage(
                $templateName, 
                $context,  
                $params['email'], 
                $this->parameters['company']['email']
                );

    }

    /**
     * Send invest confirmation
     *
     * @param Invoice $invoice
     * @param float   $amount
     */
    public function sendPurchaseConfirmationMessage(Invoice $invoice, $amount)
    {
        $templateName = 'CoreBundle:Email:invest_confirmation.html.twig';
        $toEmail = $invoice->getTransaction()->getActor()->getEmail();

        switch ($invoice->getTransaction()->getPaymentMethod()) {
            case Transaction::PAYMENT_METHOD_BANK_TRANSFER:
                $paymentType = 'invoice.payment.by.bank.transfer';
                break;
            case Transaction::PAYMENT_METHOD_CREDIT_CARD:
                $paymentType = 'invoice.payment.by.credit.card';
                break;
            case Transaction::PAYMENT_METHOD_PAYPAL:
                $paymentType = 'invoice.payment.by.paypal';
        }

        $orderUrl = $this->router->generate('optisoop_front_profile_showinvoice', array('number' => $invoice->getInvoiceNumber()), UrlGeneratorInterface::ABSOLUTE_URL);

        $context = array(
            'order_number' => $invoice->getTransaction()->getTransactionKey(),
            'amount'         => $amount,
            'payment_type'   => $paymentType,
            'order_url'      => $orderUrl
        );

        $this->sendMessage($templateName, $context, $this->parameters['company']['sales_email'] , $toEmail);
    }

    /**
     * Notify invest to the site admin
     *
     * @param Invoice $invoice
     */
    public function sendPurchaseNotification(Invoice $invoice)
    {
        $templateName = 'CoreBundle:Email:invest_notification.html.twig';

        $context = array(
            'order_number'    => $invoice->getTransaction()->getTransactionKey(),
            'invoice_date'      => $invoice->getCreated(),
            'user_email'        => $invoice->getTransaction()->getActor()->getEmail(),
            'order_details_url' => $this->router->generate('optisoop_front_profile_showinvoice', array('number' => $invoice->getInvoiceNumber()), UrlGeneratorInterface::ABSOLUTE_URL),
        );

        $this->sendMessage($templateName, $context, $this->parameters['company']['sales_email'], $this->parameters['company']['sales_email']);
    }

    /**
     * Send the tracking code
     *
     * @param Order $order
     */
    public function sendTrackingCodeEmailMessage(Transaction $transaction)
    {
        $templateName = 'EcommerceBundle:Email:trackingCode.html.twig';
        $toEmail = $transaction->getActor()->getEmail();

        $context = array(
            'order_number'  => $transaction->getTransactionKey(),
            'tracking_code' => $transaction->getDelivery()->getTrackingCode(),
            'carrier_name'  => 'Transporte'//$transaction->getDelivery()->getCarrier()->getName()
        );

        $this->sendMessage($templateName, $context, $this->parameters['company']['sales_email'], $toEmail);
    }

    /**
     * Send bank transfer confirmation
     *
     * @param Transaction $transaction
     */
    public function sendBankTransferConfirmation(Transaction $transaction)
    {
        $templateName = 'CoreBundle:Email:bankTransferConfirmation.html.twig';
        $toEmail = $transaction->getActor()->getEmail();

        $context = array(
            'user_name'     => $transaction->getActor()->getName(),
            'order_number'  => $transaction->getTransactionKey()
        );

        $this->sendMessage($templateName, $context, $this->parameters['company']['sales_email'] , $toEmail);
    }
    
    /**
     * Send bank newsletter email
     * 
     * @param Transaction $transaction
     */
    public function sendShipping($emails, $type, $body)
    {
        $templateName = 'CoreBundle:Email:newsletter.html.twig';
  
        $values = array(
            'type'     => $type,
            'body' => $body
        );
        
        $context    = $this->twig->mergeGlobals($values);
        $template   = $this->twig->loadTemplate($templateName);
        $subject    = $template->renderBlock('subject', $context);
        $textBody   = $template->renderBlock('body_text', $context);
        $htmlBody   = $template->renderBlock('body_html', $context);

        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom(array($this->parameters['company']['email'] =>  $this->parameters['company']['name']))
            ->setTo($emails);

        if (!empty($htmlBody)) {
            $message->setBody($htmlBody, 'text/html')
                ->addPart($textBody, 'text/plain');
        } else {
            $message->setBody($textBody);
        }
        $this->mailer->send($message);
        
    }
    
    /**
     * @param string $templateName
     * @param array  $context
     * @param string $fromEmail
     * @param string $toEmail
     */
    private function sendMessage($templateName, $context, $fromEmail, $toEmail)
    {
        $context    = $this->twig->mergeGlobals($context);
        $template   = $this->twig->loadTemplate($templateName);
        $subject    = $template->renderBlock('subject', $context);
        $textBody   = $template->renderBlock('body_text', $context);
        $htmlBody   = $template->renderBlock('body_html', $context);

        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom(array($fromEmail =>  $this->parameters['company']['name']))
            ->setTo($toEmail);

        if (!empty($htmlBody)) {
            $message->setBody($htmlBody, 'text/html')
                ->addPart($textBody, 'text/plain');
        } else {
            $message->setBody($textBody);
        }
        $this->mailer->send($message);

    }
}
