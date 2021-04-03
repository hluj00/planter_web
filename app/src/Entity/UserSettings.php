<?php

namespace App\Entity;

use App\Repository\UserSettingsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UserSettingsRepository::class)
 */
class UserSettings
{


    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    private $user_id;

    /**
     * @ORM\Column(type="time")
     */
    private $send_notifications_at;

    /**
     * @ORM\Column(type="boolean")
     */
    private $send_notifications;

    /**
     * UserSettings constructor.
     * @param $user_id
     */
    public function __construct($user_id)
    {
        $this->user_id = $user_id;
    }


    public function getSendNotificationsAt(): ?\DateTimeInterface
    {
        return $this->send_notifications_at;
    }

    public function setSendNotificationsAt(\DateTimeInterface $send_notifications_at): self
    {
        $this->send_notifications_at = $send_notifications_at;

        return $this;
    }

    public function getSendNotifications(): ?bool
    {
        return $this->send_notifications;
    }

    public function setSendNotifications(bool $send_notifications): self
    {
        $this->send_notifications = $send_notifications;

        return $this;
    }

    public function getUserId(): ?int
    {
        return $this->user_id;
    }
}
