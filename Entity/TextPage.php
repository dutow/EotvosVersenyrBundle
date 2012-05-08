<?php

namespace Eotvos\VersenyrBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Eotvos\VersenyrBundle\Entity\TextPage
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Eotvos\VersenyrBundle\Entity\TextPageRepository")
 * @Gedmo\Tree(type="nested")
 * @ORM\HasLifecycleCallbacks
 */
class TextPage
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Gedmo\TreeLeft
     * @ORM\Column(name="lft", type="integer")
     */
    private $lft;

    /**
     * @Gedmo\TreeLevel
     * @ORM\Column(name="lvl", type="integer")
     */
    private $lvl;

    /**
     * @Gedmo\TreeRight
     * @ORM\Column(name="rgt", type="integer")
     */
    private $rgt;

    /**
     * @Gedmo\TreeRoot
     * @ORM\Column(name="root", type="integer", nullable=true)
     */
    private $root;

    /**
     * @Gedmo\TreeParent
     * @ORM\ManyToOne(targetEntity="TextPage", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity="TextPage", mappedBy="parent")
     * @ORM\OrderBy({"lft" = "ASC"})
     */
    private $children;

    /**
     * @var string $title
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=false)
     */
    private $title;

    /**
     * @var string $redirects_to
     *
     * @ORM\Column(name="redirects_to", type="string", length=255, nullable=true)
     */
    private $redirects_to;

    /**
     * @var text $body
     *
     * @ORM\Column(name="body", type="text", nullable=true)
     */
    private $body;

    /**
     * @var string $slug
     *
     * @ORM\Column(name="slug", type="string", length=255, nullable=false)
     * @Gedmo\Slug(fields={"title"})
     */
    private $slug;

    /**
     * @var string $special
     *
     * @ORM\Column(name="special", type="string", length=255, nullable=true)
     */
    private $special;

    /**
     * @var string $fbbox
     *
     * @ORM\Column(name="fbbox", type="boolean")
     */
    private $fbbox;

    /**
     * @var object $in_menu
     *
     * @ORM\Column(name="in_menu", type="boolean")
     */
    private $in_menu;

    /**
     * @var object $section
     *
     * @ORM\OneToOne(targetEntity="Section", mappedBy="page")
     */
    private $section;

    /**
     * @var object $round
     *
     * @ORM\OneToOne(targetEntity="Round", mappedBy="page")
     */
    private $round;


    /**
     * @ORM\PrePersist
     */
    public function __construct()
    {
        $this->fbbox = false;
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
     * Set title
     *
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set body
     *
     * @param text $body
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * Get body
     *
     * @return text
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Set parent
     *
     * @param integer $parent
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    /**
     * Get parent
     *
     * @return integer
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set slug
     *
     * @param string $slug
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set lft
     *
     * @param integer $lft
     */
    public function setLft($lft)
    {
        $this->lft = $lft;
    }

    /**
     * Get lft
     *
     * @return integer
     */
    public function getLft()
    {
        return $this->lft;
    }

    /**
     * Set lvl
     *
     * @param integer $lvl
     */
    public function setLvl($lvl)
    {
        $this->lvl = $lvl;
    }

    /**
     * Get lvl
     *
     * @return integer
     */
    public function getLvl()
    {
        return $this->lvl;
    }

    /**
     * Set rgt
     *
     * @param integer $rgt
     */
    public function setRgt($rgt)
    {
        $this->rgt = $rgt;
    }

    /**
     * Get rgt
     *
     * @return integer
     */
    public function getRgt()
    {
        return $this->rgt;
    }

    /**
     * Set root
     *
     * @param integer $root
     */
    public function setRoot($root)
    {
        $this->root = $root;
    }

    /**
     * Get root
     *
     * @return integer
     */
    public function getRoot()
    {
        return $this->root;
    }

    public function getRootNode()
    {
      // TODO: better!
      $p = $this;
      while($p->getParent()!=NULL){
        $p = $p->getParent();
      }
      return $p;
    }

    /**
     * Set special
     *
     * @param string $special
     */
    public function setSpecial($special)
    {
        $this->special = $special;
    }

    /**
     * Get special
     *
     * @return string
     */
    public function getSpecial()
    {
        return $this->special;
    }

    /**
     * Add children
     *
     * @param Eotvos\VersenyrBundle\Entity\TextPage $children
     */
    public function addTextPage(\Eotvos\VersenyrBundle\Entity\TextPage $children)
    {
        $this->children[] = $children;
    }

    /**
     * Get children
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Set section
     *
     * @param Eotvos\VersenyrBundle\Entity\Section $section
     */
    public function setSection(\Eotvos\VersenyrBundle\Entity\Section $section)
    {
        $this->section = $section;
    }

    /**
     * Get section
     *
     * @return Eotvos\VersenyrBundle\Entity\Section
     */
    public function getSection()
    {
        return $this->section;
    }

    public function getParentList(){
      $p = $this->getParent();
      $list = array();
      $list []= $this;
      while($p!=null){
        $list []= $p;
        $p = $p->getParent();
      }
      return array_reverse($list);
    }

    /**
     * Set fbbox
     *
     * @param boolean $fbbox
     */
    public function setFbbox($fbbox)
    {
        $this->fbbox = $fbbox;
    }

    /**
     * Get fbbox
     *
     * @return boolean
     */
    public function getFbbox()
    {
        return $this->fbbox;
    }

    /**
     * Set round
     *
     * @param Eotvos\VersenyrBundle\Entity\Round $round
     */
    public function setRound(\Eotvos\VersenyrBundle\Entity\Round $round)
    {
        $this->round = $round;
    }

    /**
     * Get round
     *
     * @return Eotvos\VersenyrBundle\Entity\Round
     */
    public function getRound()
    {
        return $this->round;
    }

    /**
     * Set in_menu
     *
     * @param boolean $inMenu
     */
    public function setInMenu($inMenu)
    {
        $this->in_menu = $inMenu;
    }

    /**
     * Get in_menu
     *
     * @return boolean
     */
    public function getInMenu()
    {
        return $this->in_menu;
    }

    /**
     * Set redirects_to
     *
     * @param string $redirectsTo
     */
    public function setRedirectsTo($redirectsTo)
    {
        $this->redirects_to = $redirectsTo;
    }

    /**
     * Get redirects_to
     *
     * @return string
     */
    public function getRedirectsTo()
    {
        return $this->redirects_to;
    }
}
