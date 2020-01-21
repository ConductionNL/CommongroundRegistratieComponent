<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * A Component file.
 *
 * @category   	Entity
 *
 * @author     	Ruben van der Linde <ruben@conduction.nl>
 * @license    	EUPL 1.2 https://opensource.org/licenses/EUPL-1.2
 * @version    	1.0
 *
 * @link   		http//:www.conduction.nl
 * @package		Common Ground Component
 * @subpackage  Commonground Registratie Component (CGRC)
 *
 * @ApiResource(
 *  normalizationContext={"groups"={"read"}, "enable_max_depth"=true},
 *  denormalizationContext={"groups"={"write"}, "enable_max_depth"=true}
 * )
 * @ORM\Entity(repositoryClass="App\Repository\ComponentFileRepository")
 */
class ComponentFile
{
    /**
     * @var UuidInterface The UUID identifier of this object
     *
     * @example e2984465-190a-4562-829e-a8cca81aa35d
     *
     * @Assert\Uuid
     * @Groups({"read"})
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Component", inversedBy="files")
     * @ORM\JoinColumn(nullable=false)
     */
    private $component;

    /**
     * @var string The name of this resource
     * @example My component file
     *
     * @Groups({"read", "write"})
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @var string The type of this resource
     * @example My type
     *
     * @Groups({"read", "write"})
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    /**
     * @var string The location of this resource
     * @example https://github.com/repos/ConductionNL/Commongrounregistratiecomponent/contents/README.md?ref=master
     *
     * @Groups({"read", "write"})
     * @ORM\Column(type="string", length=255)
     */
    private $location;

    /**
     * @var string The sha of this resource
     *
     * @Groups({"read", "write"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $sha;

    /**
     * @var string The extension of this resource
     * @example md
     *
     * @Groups({"read", "write"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $extention;

    /**
     * @var string The raw content of this resource
     *
     * @Groups({"read", "write"})
     * @ORM\Column(type="text", nullable=true)
     */
    private $content;

    /**
     * @var string The html content of this resource
     *
     * @Groups({"read", "write"})
     * @ORM\Column(type="text", nullable=true)
     */
    private $html;

    /**
     * @var DateTime The moment the content of this file was last updated by te crawler
     *
     * @Groups({"read"})
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $contentUpdated;

    /**
     * @var DateTime The moment the html of this file was last updated by te crawler
     *
     * @Groups({"read"})
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $htmlUpdated;

    /**
     * @var DateTime The moment this component was found by the crawler
     *
     * @Groups({"read"})
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @var DateTime The last time this component was changed
     *
     * @Groups({"read"})
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    public function getId()
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
