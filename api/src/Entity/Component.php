<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use App\Controller\Add;
use DateTime;
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
 * A Component.
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
 *  denormalizationContext={"groups"={"write"}, "enable_max_depth"=true},
 *  collectionOperations={
 *  	"get",
 *      "add" ={
 *         "method"="POST",
 *         "path"="/add",
 *         "controller"=Add::class,
 *         "read"=false,
 *         "output"=false
 *     }
 *  },
 * 	itemOperations={
 *     "refresh" ={
 *         "method"="POST",
 *         "path"="/components/{id}/refresh",
 *         "controller"=ComponentRefresh::class
 *     },
 *     "get"
 *  }
 * )
 * @ORM\Entity(repositoryClass="App\Repository\ComponentRepository")
 * @ApiFilter(SearchFilter::class, properties={"name": "exact","description": "partial"})
 * @ApiFilter(BooleanFilter::class, properties={"commonground"})
 */
class Component
{
    /**
     * @var UuidInterface $id The UUID identifier of this object
     * @example e2984465-190a-4562-829e-a8cca81aa35d
     *
     * @ApiProperty(
     * 	   identifier=true,
     *     attributes={
     *         "swagger_context"={
     *         	   "description" = "The UUID identifier of this object",
     *             "type"="string",
     *             "format"="uuid",
     *             "example"="e2984465-190a-4562-829e-a8cca81aa35d"
     *         }
     *     }
     * )
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
     * @var string The name of this component
     *
     * @example My component
     *
     * @ApiProperty(
     * 	   iri="http://schema.org/name",
	 *     attributes={
	 *         "swagger_context"={
	 *         	   "description" = "The name of this component",
	 *             "type"="string",
	 *             "example"="My component",
	 *             "maxLength"=255,
	 *             "required" = true
	 *         }
	 *     }
	 * )
	 *
     * @Assert\NotNull
     * @Assert\Length(
     *      max = 255
     * )
     * @Groups({"read"})
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @var string An short description of this component
     *
     * @example This is the best component ever
     *
     * @ApiProperty(
     * 	   iri="https://schema.org/description",
	 *     attributes={
	 *         "swagger_context"={
	 *         	   "description" = "An short description of this component",
	 *             "type"="string",
	 *             "example"="This is the best component ever",
	 *             "maxLength"=2550
	 *         }
	 *     }
	 * )
	 *
     * @Assert\Length(
     *      max = 2550
     * )
     * @Groups({"read"})
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @var string The logo for this component
     *
     * @example https://www.my-organisation.com/logo.png
     *
     * @ApiProperty(
     * 	   iri="https://schema.org/logo",
     *     attributes={
     *         "swagger_context"={
     *         	   "description" = "The logo for this component",
     *             "type"="string",
     *             "format"="url",
	 *             "example"="https://www.my-organisation.com/logo.png",
	 *             "maxLength"=255
	 *         }
	 *     }
	 * )
	 *
     * @Assert\Url
     * @Assert\Length(
     *      max = 255
     * )
     * @Groups({"read"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $logo;

    /**
     * @var string The current production version of this component
     *
     * @example v0.1.2.3-beta
     *
     * @ApiProperty(
     *     attributes={
     *         "swagger_context"={
     *         	   "description" = "The current production version of this component",
     *             "type"="string",
     *             "format"="url",
	 *             "example"="v0.1.2.3-beta",
	 *             "maxLength"=255
	 *         }
	 *     }
	 * )
	 *
     * @Assert\Length(
     *      max = 255
     * )
     * @Groups({"read"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $version;

    /**
     * @var string The slug for this component
     *
     * @example my-organisation
	 *
	 * @ApiProperty(
	 *     attributes={
	 *         "swagger_context"={
	 *         	   "description" = "The slug for this component",
	 *             "type"="string",
	 *             "example"="my-organisation",
	 *             "maxLength"=255
	 *         }
	 *     }
	 * )
	 *
     * @Gedmo\Slug(fields={"name"})
     * @Assert\Length(
     *      max = 255
     * )
     * @Groups({"read"})
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @var string The link to the git repository for this component
     *
     * @example https://www.github.com/my-organisation/my-component.git
     *
     * @ApiProperty(
     * 	   iri="https://schema.org/url",
     *     attributes={
     *         "swagger_context"={
     *         	   "description" = "The link to the git repository for this component",
     *             "type"="string",
     *             "format"="url",
	 *             "example"="https://www.github.com/my-organisation/my-component.git",
	 *             "maxLength"=255,
	 *             "required" = true
	 *         }
	 *     }
	 * )
	 *
     * @Assert\NotNull
     * @Assert\Url
     * @Assert\Length(
     *      max = 255
     * )
     * @Groups({"read"})
     * @ORM\Column(type="string", length=255)
     */
    private $git;

    /**
     * @var string The git id for the repository for this component
     *
     * @example my-component
	 *
	 * @ApiProperty(
	 *     attributes={
	 *         "swagger_context"={
	 *         	   "description" = "The git id for the repository for this component",
	 *             "type"="string",
	 *             "example"="my-component",
	 *             "maxLength"=255
	 *         }
	 *     }
	 * )
	 *
     * @Assert\Length(
     *      max = 255
     * )
     * @Groups({"read"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $gitId;

    /**
     * @var string The git type for the repository for this component
     * @example({"Github", "Gitlab", "Bitbucket"})
	 *
	 * @ApiProperty(
	 *     attributes={
	 *         "swagger_context"={
	 *         	   "description" = "The git type for the repository for this component",
	 *             "type"="string",
	 *             "example"="github",
	 *             "maxLength"=255,
	 *             "enum"={"Github", "Gitlab", "Bitbucket"}
	 *         }
	 *     }
	 * )
	 *
     * @Assert\Length(
     *      max = 255
     * )
     * @Groups({"read"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $gitType;

    /**
     * @var Organisation $owner The organisation that ownes this component (or better said it's repository)
     *
     * @maxDepth(1)
     * @Assert\Valid
     * @ORM\ManyToOne(targetEntity="App\Entity\Organisation",cascade={"persist"})
     */
    private $owner;

    /**
	 * @var ArrayCollection $apis The APIs provided by this component
	 *
     * @Assert\Valid
	 * @maxDepth(1)
	 * @Groups({"read"})
     * @ORM\OneToMany(targetEntity="App\Entity\API", mappedBy="component",cascade={"persist"})
     */
    private $apis;

    /**
	 * @var ArrayCollection $organisations The organisations that provide this component
	 *
     * @Assert\Valid
	 * @maxDepth(1)
	 * @Groups({"read"})
     * @ORM\ManyToMany(targetEntity="App\Entity\Organisation", mappedBy="components",cascade={"persist"})
     */
    private $organisations;

    /**
	 * @var array $oas The OAS (formely swagger) documentation for this component
	 *
	 * @maxDepth(1)
	 * @Groups({"read"})
     * @ORM\Column(type="array", nullable=true)
     */
    private $oas = [];

    /**
	 * @var array $publiccode The publiccode documentation for this component
	 *
	 * @maxDepth(1)
	 * @Groups({"read"})
     * @ORM\Column(type="array", nullable=true)
     */
    private $publiccode = [];

    /**
	 * @var ArrayCollection v The organisations that provide this component
	 *
	 * @maxDepth(1)
	 * @Groups({"read"})
     * @ORM\OneToMany(targetEntity="App\Entity\ComponentFile", mappedBy="component", orphanRemoval=true,cascade={"persist"})
     */
    private $files;

    /**
     * @var boolean $commonground Whether tis component is intended for commonground
     *
	 * @Groups({"read"})
     * @ORM\Column(type="boolean")
     */
    private $commonground;

    /**
     * @var Datetime $checked The moment this component was last checked for commonground compliance
     *
	 * @Groups({"read"})
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $checked;
    
    /**
     * @var Datetime $parsed  The moment this component was last parsed for file
     *
     * @Groups({"read"})
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $parsed;

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
    
    /**
     * @var Datetime $updatedExternal The last time this components git was changed on the git provider
     *
     * @Groups({"read"})
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedExternal;

    public function __construct()
    {
        $this->apis = new ArrayCollection();
        $this->organisations = new ArrayCollection();
        $this->files = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(?string $logo): self
    {
        $this->logo = $logo;

        return $this;
    }

    public function getVersion(): ?string
    {
        return $this->version;
    }

    public function setVersion(?string $version): self
    {
        $this->version = $version;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getGit(): ?string
    {
        return $this->git;
    }

    public function setGit(string $git): self
    {
        $this->git = $git;

        return $this;
    }

    public function getGitId(): ?string
    {
        return $this->gitId;
    }

    public function setGitId(?string $gitId): self
    {
        $this->gitId = $gitId;

        return $this;
    }

    public function getGitType(): ?string
    {
        return $this->gitType;
    }

    public function setGitType(?string $gitType): self
    {
        $this->gitType = $gitType;

        return $this;
    }

    public function getOwner(): ?Organisation
    {
        return $this->owner;
    }

    public function setOwner(?Organisation $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return Collection|API[]
     */
    public function getApis(): Collection
    {
        return $this->apis;
    }

    public function addApi(API $api): self
    {
        if (!$this->apis->contains($api)) {
            $this->apis[] = $api;
            $api->setComponent($this);
        }

        return $this;
    }

    public function removeApi(API $api): self
    {
        if ($this->apis->contains($api)) {
            $this->apis->removeElement($api);
            // set the owning side to null (unless already changed)
            if ($api->getComponent() === $this) {
                $api->setComponent(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Organisation[]
     */
    public function getOrganisations(): Collection
    {
        return $this->organisations;
    }

    public function addOrganisation(Organisation $organisation): self
    {
        if (!$this->organisations->contains($organisation)) {
            $this->organisations[] = $organisation;
            $organisation->addComponent($this);
        }

        return $this;
    }

    public function removeOrganisation(Organisation $organisation): self
    {
        if ($this->organisations->contains($organisation)) {
            $this->organisations->removeElement($organisation);
            $organisation->removeComponent($this);
        }

        return $this;
    }

    public function getOas(): ?array
    {
        return $this->oas;
    }

    public function setOas(?array $oas): self
    {
        $this->oas = $oas;

        return $this;
    }

    public function getPubliccode(): ?array
    {
        return $this->publiccode;
    }

    public function setPubliccode(?array $publiccode): self
    {
        $this->publiccode = $publiccode;

        return $this;
    }

    /**
     * @return Collection|ComponentFile[]
     */
    public function getFiles(): Collection
    {
        return $this->files;
    }

    public function addFile(ComponentFile $file): self
    {
        if (!$this->files->contains($file)) {
            $this->files[] = $file;
            $file->setComponent($this);
        }

        return $this;
    }

    public function removeFile(ComponentFile $file): self
    {
        if ($this->files->contains($file)) {
            $this->files->removeElement($file);
            // set the owning side to null (unless already changed)
            if ($file->getComponent() === $this) {
                $file->setComponent(null);
            }
        }

        return $this;
    }

    public function getFilesOnType($type)
    {
    	$criteria = Criteria::create()
    	->andWhere(Criteria::expr()->gt('type', $type));

    	return $this->getFiles()->matching($criteria);
    }

    public function getCommonground(): ?bool
    {
        return $this->commonground;
    }

    public function setCommonground(bool $commonground): self
    {
        $this->commonground = $commonground;

        return $this;
    }

    public function getChecked(): ?\DateTimeInterface
    {
        return $this->checked;
    }

    public function setChecked(?\DateTimeInterface $checked): self
    {
        $this->checked = $checked;

        return $this;
    } 
    
    public function getParsed(): ?\DateTimeInterface
    {
    	return $this->parsed;
    }
    
    public function setParsed(?\DateTimeInterface $parsed): self
    {
    	$this->parsed = $parsed;
    	
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
    
    public function getUpdatedExternal(): ?\DateTimeInterface
    {
    	return $this->updatedExternal;
    }
    
    public function setUpdatedExternal(\DateTimeInterface $updatedExternal): self
    {
    	$this->updatedExternal= $updatedExternal;
    	return $this;
    }
}
