<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
// use Doctrine\ODM\MongoDB\Mapping\Annotations\EmbedOne;
use Symfony\Component\Validator\Constraints\Date;
use Doctrine\Common\Collections\ArrayCollection;
use App\Document\User;
use Doctrine\Common\Collections\Collection;

#[MongoDB\Document]
class Group
{
    #[MongoDB\Id]
    public string $id;

    #[MongoDB\Field(type: 'string')]
    public ?string $status = null;

    #[MongoDB\Field(type: 'string')]
    public ?string $authors = null;
    
    #[MongoDB\Field(type: "date")]
    public ?\DateTimeInterface $createdAt = null;
    
    // #[MongoDB\Field(type: "string")]
    // public ?string $reservationDate = null;
    #[MongoDB\Field(type: "date")]
    public  ?\DateTimeInterface $reservationDate = null;
  

    #[MongoDB\EmbedMany(targetDocument: Guest::class)]
    public ArrayCollection $guests;


    public function __construct()
    {
        $this->guests = new ArrayCollection();
    }
    // #[MongoDB\ReferenceOne(targetDocument: User::class)]
    // private ?User $user = null; // Référence à l'utilisateur

    
    //ID
    public function getId(): string
    {
        return $this->id;
    }

    public function getStatus(): string
    {
        
        return $this->status;
    }

    public function setStatus(string $status): Group
    {
        $this->status = $status;

        return $this;
    }

    //status
    public function getAuthors(): string
    {
        
        return $this->authors;
    }

    public function setAuthors(string $authors): Group
    {
        $this->authors = $authors;

        return $this;
    }


    public function getCreateGroupDate(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreateGroupDate( ?\DateTimeInterface $createdAt): Group
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getReservationDate():  ?\DateTimeInterface
    {
        return $this->reservationDate;
    }

    public function setReservationDate( ?\DateTimeInterface $reservationDate): Group
    {
        $this->reservationDate = $reservationDate;
        return $this;
    }

    //Jointure user
    
    public function getGuests(): ?ArrayCollection
    {
        return $this->guests;
    }

    public function setGuests(?ArrayCollection $guests): void
    {
        $this->guests = $guests;
    }

    public function addGuest(Guest $guest): Group
    {
        $this->guests->add($guest);
        return $this;
    }

    public function removeGuest(Guest $guest): Group
    {
        $this->guests->removeElement($guest);
        return $this;
    }

}


#[MongoDB\EmbeddedDocument]
class Guest
{
    // An example of a string type property
    #[MongoDB\ReferenceOne(targetDocument: User::class)]
    public ?User $guest = null;

    // An example of a string type property
    #[MongoDB\Field(type: "string")]
    public string $invitation = '';

    #[MongoDB\Field(type: 'string')]
    public string $username = '';

    // The getters and setters for each of our properties
    public function getGuest(): ?User
    {
        return $this->guest;
    }

    public function setGuest(?User $guest): Guest
    {
        $this->guest = $guest;
        return $this;
    }

    public function getInvitation(): string
    {
        return $this->invitation;
    }

    public function setInvitation(string $invitation): Guest
    {
        $this->invitation = $invitation;
        return $this;
    }
    public function getUsername(): string
    {
        return $this->username;
    }
    public function setUsername(string $username): Guest
    {
        $this->username = $username;

        return $this;
    }
}