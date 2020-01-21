<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\Add;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass="App\Repository\ComponentFileRepository")
 */
class ComponentFile
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Component", inversedBy="files")
     * @ORM\JoinColumn(nullable=false)
     */
    private $component;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $type;
    
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $location;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $sha;    
        
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $extention;
    
    
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $content;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $html;
    
    
    /**
     * @var Datetime $contentUpdated The moment the content of this file was last updated by te crawler
     *
     * @Groups({"read"})
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $contentUpdated;
    
    /**
     * @var Datetime $htmlUpdated The moment the html of this file was last updated by te crawler
     *
     * @Groups({"read"})
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $htmlUpdated;
    
    /**
     * @var Datetime $createdAt The moment this component was found by the crawler
     *
     * @Groups({"read"})
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $createdAt;
    
    /**
     * @var Datetime $updateAt The last time this component was changed
     *
     * @Groups({"read"})
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getComponent(): ?Component
    {
        return $this->component;
    }

    public function setComponent(?Component $component): self
    {
        $this->component = $component;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }
    
    public function getLocation(): ?string
    {
    	return $this->location;
    }
    
    public function setLocation(string $location): self
    {
    	$this->location = $location;
    	
    	return $this;
    }
    
    public function getExtention(): ?string
    {
    	return $this->extention;
    }
    
    public function setExtention(string $extention): self
    {
    	$this->extention = $extention;
    	
    	return $this;
    }
    
    public function getSha(): ?string
    {
    	return $this->sha;
    }
    
    public function setSha(string $sha): self
    {
    	$this->sha = $sha;
    	
    	return $this;
    }   

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getHtml(): ?string
    {
        return $this->html;
    }

    public function setHtml(?string $html): self
    {
        $this->html = $html;

        return $this;
    }
       
    public function getContentUpdated(): ?\DateTimeInterface
    {
    	return $this->contentUpdated;
    }
    
    public function setContentUpdated(?\DateTimeInterface $contentUpdated): self
    {
    	$this->contentUpdated = $contentUpdated;
    	
    	return $this;
    }
    
    public function getHtmlUpdated(): ?\DateTimeInterface
    {
    	return $this->htmlUpdated;
    }
    
    public function setHtmlUpdated(?\DateTimeInterface $htmlUpdated): self
    {
    	$this->htmlUpdated = $htmlUpdated;
    	
    	return $this;
    }
    
    public function getCreatedAt(): ?\DateTimeInterface
    {
    	return $this->createdAt;
    }
    
    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
    	$this->createdAt = $createdAt;
    	return $this;
    }
    
    public function getUpdatedAt(): ?\DateTimeInterface
    {
    	return $this->updatedAt;
    }
    
    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
    	$this->updatedAt = $updatedAt;
    	return $this;
    }
}
