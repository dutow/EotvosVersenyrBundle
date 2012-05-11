<?php

namespace Eotvos\VersenyrBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\ExecutionContext;
use Eotvos\VersenyrBundle\Validator as EcAssert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;

/**
 * Minimal user entity used for contensants login.
 *
 * This class contains only minimal information required for symfony's user interface.
 *
 * Actual contest data is accessed via the Registrant object, which is mapped to a single contents event.
 *
 * To require more information, sites should extend the Registrant object and set the eotvos.versenyr.registranter
 * service, NOT this!
 *
 * For more information about this, see Doc/UserInformation.md
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Eotvos\VersenyrBundle\Entity\UserRepository")
 * @ORM\HasLifecycleCallbacks
 * @DoctrineAssert\UniqueEntity( fields = {"email"}, message = "Ez az e-mail cím már foglalt!" )
 * 
 * @author    Zsolt Parragi <zsolt.parragi@cancellar.hu> 
 * @copyright 2012 Cancellar
 * @license   MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @version   Release: v0.1
 *
 * @see       Registrant
 */
class User implements UserInterface, \Serializable
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string $email
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=false)
     * @Assert\NotBlank(message="A mező kitöltése kötelező!")
     * @Assert\Email( checkMX = false, message = "Hibás e-mail cím!" )
     */
    private $email;

    /**
     * @var string $password
     *
     * @ORM\Column(name="password", type="string", length=255, nullable=false)
     */
    private $password;

    /**
     * @var string $salt
     *
     * @ORM\Column(name="salt", type="string", length=32, nullable=false)
     */
    private $salt;

    /**
     * @var boolean $admin
     *
     * @ORM\Column(name="admin", type="boolean", nullable=false)
     */
    private $admin;

    /**
     * @var boolean $tester
     *
     * @ORM\Column(name="tester", type="boolean", nullable=false)
     */
    private $tester;

    /**
     * @ORM\OneToMany(targetEntity="Registration", mappedBy="user")
     */
    private $registrations;

    /**
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     * @Gedmo\Timestampable(on="create")
     */
    private $createdAt;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->sections = new \Doctrine\Common\Collections\ArrayCollection();
        $this->tester = false;
        $this->admin = false;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set password
     *
     * @param string $password new password of the user
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Add a section
     *
     * @param Eotvos\VersenyrBundle\Entity\Section $section new section to register to
     */
    public function addSection(\Eotvos\VersenyrBundle\Entity\Section $section)
    {
        $this->sections[] = $section;
    }

    /**
     * Get sections
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getSections()
    {
        return $this->sections;
    }

    /**
     * Get username
     *
     * @return string 
     */
    public function getUsername()
    {
        return $this->email;
    }

    /**
     * Set email
     *
     * @param string $email new email address
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set salt
     *
     * @param string $salt new salt
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;
    }

    /**
     * Get salt
     *
     * @return string 
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * Add a submission
     *
     * @param Eotvos\VersenyrBundle\Entity\Submission $submission user's submission
     */
    public function addSubmission(\Eotvos\VersenyrBundle\Entity\Submission $submission)
    {
        $this->submissions[] = $submission;
    }

    /**
     * Get submissions
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getSubmissions()
    {
        return $this->submissions;
    }

    /**
     * Gets the roles of the user
     *
     * @return array`
     */
    public function getRoles()
    {
        $roles = array('ROLE_USER');
        if ($this->getAdmin()) {
            $roles[] = 'ROLE_ADMIN';
        }
        if ($this->getTester()) {
            $roles[] = 'ROLE_TESTER';
        }

        return $roles;
    }

    /**
     * Erases the user's sensitive data - so nothing.
     *
     */
    public function eraseCredentials()
    {
    }

    /**
     * Equality operator
     *
     * @param UserInterface $oth other user
     *
     * @return bool if the users equals
     */
    public function equals(UserInterface $oth)
    {
        return ($oth instanceof User) && ($oth->getEmail() == $this->getEmail());
    }

    /**
     * Serializes the object.
     *
     * @return string serialized data
     */
    public function serialize()
    {
        return serialize(array(
            'id' => $this->id,
            'email' => $this->email,
            'password' => $this->password,
            'salt' => $this->salt,
        ));
    }

    /**
     * Unserializes the object.
     *
     * @param string $str serialized string
     *
     * @return User self
     */
    public function unserialize($str)
    {
        $tmp = unserialize($str);

        if (!is_array($tmp)) {
            throw new \Exception("Bad serialized data!");
        }

        foreach ($tmp as $key => $v) {
            $this->$key = $v;
        }

        return $this;
    }

    /**
     * May the user join the given section?
     *
     * @param Section $section question subject
     *
     * @return bool true if possible
     */
    public function mayJoin(\Eotvos\VersenyrBundle\Entity\Section $section)
    {
        $now = new \DateTime();
        $now->sub(new \DateInterval('P1D'));

        return ($section->getRegistrationUntil() > $now);
    }

    /**
     * Returns the user's unique identifier required for his anonymous submissions.
     *
     * @return string
     */
    public function getUniqueIdentifier()
    {
        $id = $this->getId();
        $betu = array(
            'L',
            'H',
            'A',
            'B',
            'G',
            'C',
            'J',
            'P',
            'E',
            'K',
            'O',
            'R',
            'I',
            'M',
            'D',
            'F',
            'N'
        );
        $sum = 0;
        $idn = $id;
        while ($idn) {
            $sum = $sum + $idn % 37;
            $idn /= 37;
        }
        $betuid = $sum % 17;

        return $id.$betu[$betuid];
    }

    public function __toString()
    {
        return $this->getUniqueIdentifier();
    }

    /**
     * Add registrations
     *
     * @param Eotvos\VersenyrBundle\Entity\Registration $registrations
     */
    public function addRegistration(\Eotvos\VersenyrBundle\Entity\Registration $registrations)
    {
        $this->registrations[] = $registrations;
    }

    /**
     * Get registrations
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getRegistrations()
    {
        return $this->registrations;
    }

    /**
     * Set admin
     *
     * @param boolean $admin
     */
    public function setAdmin($admin)
    {
        $this->admin = $admin;
    }

    /**
     * Get admin
     *
     * @return boolean 
     */
    public function getAdmin()
    {
        return $this->admin;
    }

    /**
     * Set tester
     *
     * @param boolean $tester
     */
    public function setTester($tester)
    {
        $this->tester = $tester;
    }

    /**
     * Get tester
     *
     * @return boolean 
     */
    public function getTester()
    {
        return $this->tester;
    }

    /**
     * Set createdAt
     *
     * @param datetime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * Get createdAt
     *
     * @return datetime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function getRegistrationForTerm($term)
    {
        foreach ($this->getRegistrations() as $reg) {
            if ($reg->getTerm()->getId()==$term->getId()) {
                return $reg;
            }
        }

        return null;
    }
}
