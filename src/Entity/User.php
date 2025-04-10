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
    private ?string $user_last_name = null;

    #[ORM\Column(length: 50)]
    private ?string $user_first_name = null;

    #[ORM\Column(length: 13, nullable: true)]
    private ?string $user_phone = null;

    #[ORM\Column(nullable: true)]
    private ?int $user_address_number = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $user_address_road = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $user_address_label = null;

    #[ORM\Column(length: 8, nullable: true)]
    private ?string $user_address_postal_code = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $user_address_city = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $user_address_state = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $user_address_country = null;

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
        return $this->user_last_name;
    }

    public function setUserLastName(string $user_last_name): static
    {
        $this->user_last_name = $user_last_name;

        return $this;
    }

    public function getUserFirstName(): ?string
    {
        return $this->user_first_name;
    }

    public function setUserFirstName(string $user_first_name): static
    {
        $this->user_first_name = $user_first_name;

        return $this;
    }

    public function getUserPhone(): ?string
    {
        return $this->user_phone;
    }

    public function setUserPhone(?string $user_phone): static
    {
        $this->user_phone = $user_phone;

        return $this;
    }

    public function getUserAddressNumber(): ?int
    {
        return $this->user_address_number;
    }

    public function setUserAddressNumber(?int $user_address_number): static
    {
        $this->user_address_number = $user_address_number;

        return $this;
    }

    public function getUserAddressRoad(): ?string
    {
        return $this->user_address_road;
    }

    public function setUserAddressRoad(?string $user_address_road): static
    {
        $this->user_address_road = $user_address_road;

        return $this;
    }

    public function getUserAddressLabel(): ?string
    {
        return $this->user_address_label;
    }

    public function setUserAddressLabel(?string $user_address_label): static
    {
        $this->user_address_label = $user_address_label;

        return $this;
    }

    public function getUserAddressPostalCode(): ?string
    {
        return $this->user_address_postal_code;
    }

    public function setUserAddressPostalCode(?string $user_address_postal_code): static
    {
        $this->user_address_postal_code = $user_address_postal_code;

        return $this;
    }

    public function getUserAddressCity(): ?string
    {
        return $this->user_address_city;
    }

    public function setUserAddressCity(?string $user_address_city): static
    {
        $this->user_address_city = $user_address_city;

        return $this;
    }

    public function getUserAddressState(): ?string
    {
        return $this->user_address_state;
    }

    public function setUserAddressState(?string $user_address_state): static
    {
        $this->user_address_state = $user_address_state;

        return $this;
    }

    public function getUserAddressCountry(): ?string
    {
        return $this->user_address_country;
    }

    public function setUserAddressCountry(?string $user_address_country): static
    {
        $this->user_address_country = $user_address_country;

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
