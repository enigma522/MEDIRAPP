<?php

namespace App\Entity;

use App\Repository\PatientRepository;
use App\Repository\UsersRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints\DateTime;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints\Date;

/**
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({"patient" = "Patient","medecin"="Medecin"})
 * @ORM\HasLifecycleCallbacks
 */
#[ORM\Entity(repositoryClass: UsersRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class Users implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];



    #[ORM\OneToMany(targetEntity: Message::class, mappedBy: "user")]
    private $messages;
    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 100)]
    private ?string $lastname = null;

    #[ORM\Column(length: 100)]
    private ?string $firstname = null;

    #[ORM\Column(length: 255)]
    private ?string $adress = null;

    #[ORM\Column(length: 5)]
    private ?string $zipcode = null;

    #[ORM\Column(length: 150)]
    private ?string $city = null;

    #[ORM\Column(options: ['default' => 'CURRENT_TIMESTAMP'])]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(length: 20)]
    private ?string $user_type = null;

    #[ORM\Column(nullable: true)]
    private ?int $Age = null;

    #[ORM\Column(type: "datetime", nullable: true)]
    private ?\DateTime $birthday = null;

    #[ORM\Column(nullable: true, length: 20)]
    private ?string $phoneNumber = null;

    #[ORM\ManyToMany(targetEntity: Conversation::class, mappedBy: 'participant')]
    private Collection $conversations;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $user_token = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $user_connection_id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $specialite = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\ManyToMany(targetEntity: self::class, inversedBy: 'mes_medecin')]
    private Collection $patients;

    #[ORM\ManyToMany(targetEntity: self::class, mappedBy: 'patients')]
    private Collection $mes_medecin;

    #[ORM\OneToMany(mappedBy: 'patient', targetEntity: TodoEvents::class)]
    private Collection $todoEvents;

    #[ORM\OneToMany(mappedBy: 'patientId', targetEntity: Todo::class)]
    private Collection $todos;




    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }
    public function __construct()
    {
        $this->created_at = new \DateTimeImmutable();
        $this->conversations = new ArrayCollection();
        $this->patients = new ArrayCollection();
        $this->mes_medecin = new ArrayCollection();
        $this->todoEvents = new ArrayCollection();
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
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getAdress(): ?string
    {
        return $this->adress;
    }

    public function setAdress(string $adress): self
    {
        $this->adress = $adress;

        return $this;
    }

    public function getZipcode(): ?string
    {
        return $this->zipcode;
    }

    public function setZipcode(string $zipcode): self
    {
        $this->zipcode = $zipcode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUserType(): ?string
    {
        return $this->user_type;
    }

    public function setUserType(string $user_type): self
    {

        $this->user_type = $user_type;

        return $this;
    }

    /**
     * @return Collection<int, Conversation>
     */
    public function getConversations(): Collection
    {
        return $this->conversations;
    }

    public function addConversation(Conversation $conversation): self
    {
        if (!$this->conversations->contains($conversation)) {
            $this->conversations->add($conversation);
        }

        return $this;
    }

    public function removeConversation(Conversation $conversation): self
    {
        if ($this->conversations->removeElement($conversation)) {
        }

        return $this;
    }

    public function getUserToken(): ?string
    {
        return $this->user_token;
    }

    public function setUserToken(string $user_token): self
    {
        $this->user_token = $user_token;

        return $this;
    }

    public function getUserConnectionId(): ?string
    {
        return $this->user_connection_id;
    }

    public function setUserConnectionId(string $user_connection_id): self
    {
        $this->user_connection_id = $user_connection_id;

        return $this;
    }


    public function getBirthday(): ?\DateTime
    {
        return $this->birthday;
    }

    public function setBirthday(\DateTime $birthday): self
    {

        $this->birthday = $birthday;
        if ($birthday != null) {
            $now = new \DateTime();
            $this->setAge($now->diff($birthday)->y);
        }

        return $this;
    }

    public function getAge(): ?int
    {
        return $this->Age;
    }

    public function setAge(int $Age): self
    {

        $this->Age = $Age;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getSpecialite(): ?string
    {
        return $this->specialite;
    }

    public function setSpecialite(?string $specialite): self
    {
        $this->specialite = $specialite;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getPatients(): Collection
    {
        return $this->patients;
    }

    public function addPatient(self $patient): self
    {
        if (!$this->patients->contains($patient)) {
            $this->patients->add($patient);
        }

        return $this;
    }

    public function removePatient(self $patient): self
    {
        $this->patients->removeElement($patient);

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getMesMedecin(): Collection
    {
        return $this->mes_medecin;
    }

    public function addMesMedecin(self $mesMedecin): self
    {
        if (!$this->mes_medecin->contains($mesMedecin)) {
            $this->mes_medecin->add($mesMedecin);
            $mesMedecin->addPatient($this);
        }

        return $this;
    }

    public function removeMesMedecin(self $mesMedecin): self
    {
        if ($this->mes_medecin->removeElement($mesMedecin)) {
            $mesMedecin->removePatient($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, TodoEvents>
     */
    public function getTodoEvents(): Collection
    {
        return $this->todoEvents;
    }

    public function addTodoEvent(TodoEvents $todoEvent): self
    {
        if (!$this->todoEvents->contains($todoEvent)) {
            $this->todoEvents->add($todoEvent);
            $todoEvent->setPatient($this);
        }

        return $this;
    }

    public function removeTodoEvent(TodoEvents $todoEvent): self
    {
        if ($this->todoEvents->removeElement($todoEvent)) {
            // set the owning side to null (unless already changed)
            if ($todoEvent->getPatient() === $this) {
                $todoEvent->setPatient(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Todo>
     */
    public function getTodos(): Collection
    {
        return $this->todos;
    }

    public function addTodo(Todo $todo): self
    {
        if (!$this->todos->contains($todo)) {
            $this->todos->add($todo);
            $todo->setPatientId($this);
        }

        return $this;
    }

    public function removeTodo(Todo $todo): self
    {
        if ($this->todos->removeElement($todo)) {
            // set the owning side to null (unless already changed)
            if ($todo->getPatientId() === $this) {
                $todo->setPatientId(null);
            }
        }

        return $this;
    }
}
