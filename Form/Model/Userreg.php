<?php

namespace Eotvos\VersenyrBundle\Form\Model;

use Eotvos\Versenybundle\Entity\User;
use Eotvos\Versenybundle\Entity\Registration;

class Regform
{
    protected $user;

    protected $registration;

    public function setUser(User $user)
    {
        $this->user = $user;
    }

    public function getUser()
    {
        return $user;
    }

    public function setRegistration(Registration $registration)
    {
        $this->registration = $registration;
    }

    public function getRegistration()
    {
        return $this->registration;
    }
}

