<?php
namespace Junior\SiteinterneBundle\Validator;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class SurveilledatesValidator extends ConstraintValidator
{
  private $requestStack;
  private $em;

  public function __construct(RequestStack $requestStack, EntityManagerInterface $em)
  {
    $this->requestStack = $requestStack;
    $this->em           = $em;
  }

  public function validate($protocol, Constraint $constraint)
  {
    $request = $this->requestStack->getCurrentRequest();

    // On vérifie si la date de signature du doc est correcte
    $msg = $this->em
      ->getRepository('JuniorSiteinterneBundle:Document')
      ->dateCorrecte($protocol->getMission()->getId(), $protocol->getSigneLe(),$protocol->getTypeDeDocument())
    ;

    if ($msg != 'ok') {
      $this->context->addViolationAt('signeLe',$constraint->message, array('%suite_msg%' => $msg));
    }
  }
}