<?php

namespace App\Entity;

use App\Repository\ConversationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ConversationRepository::class)]

class Conversation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;


    

    #[ORM\OneToMany(targetEntity: Message::class, mappedBy: 'conversation')]
    private $messages;

    #[ORM\ManyToMany(targetEntity: Users::class, inversedBy: 'conversations')]
    private Collection $participant;

    

    public function __construct()
    {
        
        $this->messages = new ArrayCollection();
        $this->participant = new ArrayCollection();
    }
    public function getId(): ?int
    {
        return $this->id;
    }

   

    

    

    

    /**
     * @return Collection<int, Message>
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): self
    {
        if (!$this->messages->contains($message)) {
            $this->messages->add($message);
            $message->setConversation($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): self
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getConversation() === $this) {
                $message->setConversation(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Users>
     */
    public function getParticipant(): Collection
    {
        return $this->participant;
    }
}
