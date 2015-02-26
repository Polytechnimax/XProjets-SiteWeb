<?php
namespace Junior\SiteinterneBundle\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Surveilledates extends Constraint
{
  public $message = "Il y a un problème dans les dates du document. %suite_msg%";

  public function validatedBy()
  {
    return 'junior_siteinternebundle_surveilledates';
  }
  
  public function getTargets()
	{
		return self::CLASS_CONSTRAINT;
	}
}