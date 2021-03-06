<?php

namespace App\Entity;

use App\Repository\UserSettingsRepository;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\This;

/**
 * @ORM\Entity(repositoryClass=UserSettingsRepository::class)
 */
class UserSettings
{
    public static $PERIOD_PREVIOUS_DAY = 1;
    public static $PERIOD_LAST_24H = 2;



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
     * @ORM\Column(type="string", length=45)
     */
    private $ifttt_endpoint;

    /**
     * @ORM\Column(type="integer")
     */
    private $notificationPeriodType;

    /**
     * UserSettings constructor.
     * @param $user_id
     */
    public function __construct($user_id)
    {
        $this->user_id = $user_id;
    }

    public function getIftttEndpoint()
    {
        return $this->ifttt_endpoint;
    }

    public function setIftttEndpoint($ifttt_endpoint): self
    {
        $this->ifttt_endpoint = $ifttt_endpoint;
        return $this;
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

    public function getSendNotificationsAtToday(): \DateTime
    {
        $time = $this->getSendNotificationsAt()->format('H:i:s');
        $time = explode(":", $time);
        $sendAt = new \DateTime('now');
        $sendAt->setTime($time[0], $time[1], $time[2]);


        return $sendAt;
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

    public function __toString()
    {
        return sprintf("[%d] - (%d)\n", $this->user_id, $this->send_notifications);
    }

    public function getNotificationPeriodType(): ?int
    {
        return $this->notificationPeriodType;
    }

    public function setNotificationPeriodType(int $notificationPeriodType): self
    {
        $this->notificationPeriodType = $notificationPeriodType;

        return $this;
    }


}
