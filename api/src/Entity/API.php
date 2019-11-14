<?php

namespace App\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\MaxDepth;

use App\Controller\ApiController;

/**
 * An API
 *
 * @category   	Entity
 *
 * @author     	Ruben van der Linde <ruben@conduction.nl>
 * @license    	EUPL 1.2 https://opensource.org/licenses/EUPL-1.2
 * @version    	1.0
 *
 * @link   		http//:www.common-ground.dev
 * @package		Common Ground Component
 * @subpackage  Commonground Registratie Component (CGRC)
 *
 * @ApiResource(
 *  normalizationContext={"groups"={"read"}, "enable_max_depth"=true},
 *  denormalizationContext={"groups"={"write"}, "enable_max_depth"=true},
 *  collectionOperations={
 *  	"get"
 *  },
 * 	itemOperations={
 *     "refresh" ={
 *         "method"="POST",
 *          "path"="/apis/{id}/refresh",
 *          "controller"=ApiRefresh::class
 *     },
 *     "get"
 *  }
 * )
 * @ORM\Entity(repositoryClass="App\Repository\APIRepository")
 */
class API
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
	 * @param string The name of this API
	 *
	 * @ApiProperty(
     * 	   iri="http://schema.org/name",
	 *     attributes={
	 *         "swagger_context"={
	 *         	   "description" = "The name of this API",
	 *             "type"="string",
	 *             "example"="My component",
	 *              "maxLength"=255
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
	 * @var string An short description of this API
     * @example This is the best API ever
	 *
	 * @ApiProperty(
     * 	   iri="https://schema.org/description",
	 *     attributes={
	 *         "swagger_context"={
	 *         	   "description" = "An short description of this API",
	 *             "type"="string",
	 *             "example"="This is the best API ever",
	 *              "maxLength"=2550
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
     * @var string The current production version of this component
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
	 * @var string The logo for this component
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
	 * @param string The slug for this api
     * @example my-organisation
	 *
	 * @ApiProperty(
	 *     attributes={
	 *         "swagger_context"={
	 *         	   "description" = "The slug for this api",
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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $slug;

    /**
	 * @param string $endpoint The location where api calls should be directed to
     * @example https://api.my-organisation.com
	 *
	 * @ApiProperty(
     * 	   iri="https://schema.org/url",
	 *     attributes={
	 *         "swagger_context"={
	 *         	   "description" = "The location where api calls should be directed to",
	 *             "type"="string",
	 *             "example"="https://api.my-organisation.com",
	 *             "maxLength"=255
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
    private $endpoint;

    /**
	 * @param string The location of the open api documentation of this api
     * @example https://api.my-organisation.com/docs
	 *
	 * @ApiProperty(
     * 	   iri="https://schema.org/url",
	 *     attributes={
	 *         "swagger_context"={
	 *         	   "description" = "The location of the open api documentation of this api",
	 *             "type"="string",
	 *             "example"="https://api.my-organisation.com/docs",
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
    private $documentation;

    /**
	 * @var ArrayCollection $component The common ground component that this api provides
	 *
	 * @maxDepth(1)
     * @Groups({"read"})
     * @ORM\ManyToOne(targetEntity="App\Entity\Component", inversedBy="apis")
     */
    private $component;

    /**
	 * @var ArrayCollection $organisations The organisations that provide this api
	 *
	 * @maxDepth(1)
     * @Groups({"read"})
     * @ORM\ManyToMany(targetEntity="App\Entity\Organisation", mappedBy="apis")
     */
    private $organisations;

    public function __construct()
    {
        $this->organisations = new ArrayCollection();
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

    public function getVersion(): ?string
    {
        return $this->version;
    }

    public function setVersion(?string $version): self
    {
        $this->version = $version;

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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getEndpoint(): ?string
    {
        return $this->endpoint;
    }

    public function setEndpoint(string $endpoint): self
    {
        $this->endpoint = $endpoint;

        return $this;
    }

    public function getDocumentation(): ?string
    {
    	return $this->documentation;
    }

    public function setDocumentation(?string $documentation): self
    {
    	$this->documentation = $documentation;

        return $this;
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
            $organisation->addApi($this);
        }

        return $this;
    }

    public function removeOrganisation(Organisation $organisation): self
    {
        if ($this->organisations->contains($organisation)) {
            $this->organisations->removeElement($organisation);
            $organisation->removeApi($this);
        }

        return $this;
    }
}
