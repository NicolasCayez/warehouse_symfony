<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_USERNAME', fields: ['username'])]
#[UniqueEntity(fields: ['username'], message: 'There is already an account with this username')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $username = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 50)]
    private ?string $userLastName = null;

    #[ORM\Column(length: 50)]
    private ?string $userFirstName = null;

    #[ORM\Column(length: 25, nullable: true)]
    private ?string $userPhone = null;

    #[ORM\Column(nullable: true)]
    private ?int $userAddressNumber = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $userAddressRoad = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $userAddressLabel = null;

    #[ORM\Column(length: 8, nullable: true)]
    private ?string $userAddressPostalCode = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $userAddressCity = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $userAddressState = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $userAddressCountry = null;

    /**
     * @var Collection<int, Warehouse>
     */
    #[ORM\ManyToMany(targetEntity: Warehouse::class, inversedBy: 'users')]
    private Collection $warehouses;

    public function __construct()
    {
        $this->warehouses = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getUserLastName(): ?string
    {
        return $this->userLastName;
    }

    public function setUserLastName(string $userLastName): static
    {
        $this->userLastName = $userLastName;

        return $this;
    }

    public function getUserFirstName(): ?string
    {
        return $this->userFirstName;
    }

    public function setUserFirstName(string $userFirstName): static
    {
        $this->userFirstName = $userFirstName;

        return $this;
    }

    public function getUserPhone(): ?string
    {
        return $this->userPhone;
    }

    public function setUserPhone(?string $userPhone): static
    {
        $this->userPhone = $userPhone;

        return $this;
    }

    public function getUserAddressNumber(): ?int
    {
        return $this->userAddressNumber;
    }

    public function setUserAddressNumber(?int $userAddressNumber): static
    {
        $this->userAddressNumber = $userAddressNumber;

        return $this;
    }

    public function getUserAddressRoad(): ?string
    {
        return $this->userAddressRoad;
    }

    public function setUserAddressRoad(?string $userAddressRoad): static
    {
        $this->userAddressRoad = $userAddressRoad;

        return $this;
    }

    public function getUserAddressLabel(): ?string
    {
        return $this->userAddressLabel;
    }

    public function setUserAddressLabel(?string $userAddressLabel): static
    {
        $this->userAddressLabel = $userAddressLabel;

        return $this;
    }

    public function getUserAddressPostalCode(): ?string
    {
        return $this->userAddressPostalCode;
    }

    public function setUserAddressPostalCode(?string $userAddressPostalCode): static
    {
        $this->userAddressPostalCode = $userAddressPostalCode;

        return $this;
    }

    public function getUserAddressCity(): ?string
    {
        return $this->userAddressCity;
    }

    public function setUserAddressCity(?string $userAddressCity): static
    {
        $this->userAddressCity = $userAddressCity;

        return $this;
    }

    public function getUserAddressState(): ?string
    {
        return $this->userAddressState;
    }

    public function setUserAddressState(?string $userAddressState): static
    {
        $this->userAddressState = $userAddressState;

        return $this;
    }

    public function getUserAddressCountry(): ?string
    {
        return $this->userAddressCountry;
    }

    public function setUserAddressCountry(?string $userAddressCountry): static
    {
        $this->userAddressCountry = $userAddressCountry;

        return $this;
    }

    /**
     * @return Collection<int, Warehouse>
     */
    public function getWarehouses(): Collection
    {
        return $this->warehouses;
    }

    public function addWarehouse(Warehouse $warehouse): static
    {
        if (!$this->warehouses->contains($warehouse)) {
            $this->warehouses->add($warehouse);
        }

        return $this;
    }

    public function removeWarehouse(Warehouse $warehouse): static
    {
        $this->warehouses->removeElement($warehouse);

        return $this;
    }
}
