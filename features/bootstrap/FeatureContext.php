<?php

use Behat\Behat\Hook\Scope\AfterStepScope;
use Behat\Behat\Context\Context;
use Behat\MinkExtension\Context\RawMinkContext;

class FeatureContext extends RawMinkContext implements Context
{
	public function __construct()
    {
        
    }
}