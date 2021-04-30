<?php

namespace App\Entity;

use App\Repository\NotificationRepository;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity(repositoryClass=NotificationRepository::class)
 */
class Notification
{
    public static $TYPE_WATER_LEVEL = 3;
    public static $TYPE_LIGHT_LEVEL = 2;
    public static $TYPE_TEMPERATURE = 1;

    public static $PERIOD_PREVIOUS_DAY = 1;
    public static $PERIOD_LAST_24H = 2;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $value;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $send_at;

    /**
     * @ORM\Column(type="boolean")
     */
    private $send;

    /**
     * @ORM\Column(type="integer")
     */
    private $user_id;



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function getSend(): ?bool
    {
        return $this->send;
    }

    public function setSend($send): self
    {
        $this->send = $send;
        return $this;
    }

    public function setType(int $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getSendAt(): ?\DateTimeInterface
    {
        return $this->send_at;
    }

    public function setSendAt(?\DateTimeInterface $send_at): self
    {
        $this->send_at = $send_at;

        return $this;
    }

    public function getUserId(): ?int
    {
        return $this->user_id;
    }

    public function setUserId(int $user_id): self
    {
        $this->user_id = $user_id;

        return $this;
    }
}

