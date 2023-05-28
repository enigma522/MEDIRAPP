<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ORM\Index(name:"created_at_index",columns:["created_at"])]
class Message
{
    use Timestamp;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'text')]
    private $content;

    #[ORM\ManyToOne(targetEntity: Users::class, inversedBy: "messages")]
    private $user;

    #[ORM\ManyToOne(targetEntity: Conversation::class, inversedBy: "conversations")]
    private $conversation;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $created_at;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getUser(): ?Users
    {
        return $this->user;
    }

    public function setUser(?Users $users): self
    {
        $this->user = $users;

        return $this;
    }

    public function getConversation(): ?Conversation
    {
        return $this->conversation;
    }

    public function setConversation(?Conversation $conversations): self
    {
        $this->conversation = $conversations;

        return $this;
    }

    public function __construct(){
        $this->created_at=new \DateTimeImmutable();
        
 }

    
}
