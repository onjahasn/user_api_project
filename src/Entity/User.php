<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'user')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 100)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    private ?string $firstName = null;

    #[ORM\Column(type: 'string', length: 100)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    private ?string $lastName = null;

    #[ORM\Column(type: 'string', unique: true)]
    #[Assert\NotBlank]
    #[Assert\Email]
    private ?string $email = null;

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank(message: 'Le mot de passe ne peut pas être vide.')]
    private ?string $password = null;

    #[ORM\Column(type: 'json')]
    private array $roles = [];

    /**
     * Retourne l'identifiant unique (l'email dans ce cas).
     */
    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    /**
     * Retourne l'email (compatibilité avec les anciennes versions de Symfony).
     */
    public function getUsername(): string
    {
        return $this->email;
    }

    /**
     * Retourne le mot de passe de l'utilisateur.
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * Définit le mot de passe (qui doit être haché).
     */
    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    /**
     * Retourne les rôles de l'utilisateur (inclut toujours ROLE_USER par défaut).
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER'; // ROLE_USER est toujours inclus
        return array_unique($roles);
    }

    /**
     * Définit les rôles de l'utilisateur.
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }

    /**
     * Retourne l'ID de l'utilisateur.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Définit le prénom.
     */
    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;
        return $this;
    }

    /**
     * Retourne le prénom.
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * Définit le nom de famille.
     */
    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;
        return $this;
    }

    /**
     * Retourne le nom de famille.
     */
    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    /**
     * Définit l'email.
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    /**
     * Retourne l'email.
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Méthode requise par UserInterface, mais non utilisée ici.
     */
    public function getSalt(): ?string
    {
        return null; // Bcrypt ou Argon2 n'ont pas besoin de salt explicite
    }

    /**
     * Méthode pour nettoyer les données sensibles après authentification.
     */
    public function eraseCredentials(): void
    {
        // Par exemple, vous pourriez nettoyer ici un mot de passe en clair temporairement stocké
    }
}
