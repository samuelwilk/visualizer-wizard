<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var ?string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    /**
     * NOTE: This is only used when setting a users' password. NEVER EVER should this be committed to the DB in
     * its plain text form.
     */
    #[Assert\NotNull]
    private ?string $plainPassword = null;

    /**
     * @var Collection<int, VisualizationBuilderProgress>
     */
    #[ORM\OneToMany(targetEntity: VisualizationBuilderProgress::class, mappedBy: 'lastModifiedBy')]
    private Collection $visualizationBuilderProgress;

    public function __construct()
    {
        $this->visualizationBuilderProgress = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
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

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): static
    {
        $this->plainPassword = $plainPassword;

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

    /**
     * @return Collection<int, VisualizationBuilderProgress>
     */
    public function getVisualizationBuilderProgress(): Collection
    {
        return $this->visualizationBuilderProgress;
    }

    public function addVisualizationBuilderProgress(VisualizationBuilderProgress $visualizationBuilderProgress): static
    {
        if (!$this->visualizationBuilderProgress->contains($visualizationBuilderProgress)) {
            $this->visualizationBuilderProgress->add($visualizationBuilderProgress);
            $visualizationBuilderProgress->setLastModifiedBy($this);
        }

        return $this;
    }

    public function removeVisualizationBuilderProgress(VisualizationBuilderProgress $visualizationBuilderProgress): static
    {
        if ($this->visualizationBuilderProgress->removeElement($visualizationBuilderProgress)) {
            // set the owning side to null (unless already changed)
            if ($visualizationBuilderProgress->getLastModifiedBy() === $this) {
                $visualizationBuilderProgress->setLastModifiedBy(null);
            }
        }

        return $this;
    }
}
