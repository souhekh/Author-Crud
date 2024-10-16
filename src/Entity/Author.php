<?php

namespace App\Entity;

use App\Repository\AuthorRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: AuthorRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'There is already an author with this email')]
class Author
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Username cannot be blank')]
    #[Assert\Length(
        min: 2,
        max: 50,
        minMessage: 'Your username must be at least {{ limit }} characters long',
        maxMessage: 'Your username cannot be longer than {{ limit }} characters'
    )]
    private ?string $username = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Email cannot be blank')]
    #[Assert\Email(message: 'The email {{ value }} is not a valid email.')]
    private ?string $email = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $picture = null;

    private ?File $pictureFile = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'Number of books cannot be blank')]
    #[Assert\PositiveOrZero(message: 'Number of books must be zero or positive')]
    private ?int $nbBooks = null;

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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(?string $picture): self
    {
        $this->picture = $picture;
        return $this;
    }

    public function getPictureFile(): ?File
    {
        return $this->pictureFile;
    }

    public function setPictureFile(?File $pictureFile = null): void
    {
        $this->pictureFile = $pictureFile;

        if (null !== $pictureFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getNbBooks(): ?int
    {
        return $this->nbBooks;
    }

    public function setNbBooks(int $nbBooks): static
    {
        $this->nbBooks = $nbBooks;

        return $this;
    }
}
