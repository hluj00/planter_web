<?php

namespace App\Entity;

use App\Repository\SettingsPlantRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SettingsPlantRepository::class)
 */
class SettingsPlant
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $id_settings_plant;

    /**
     * @ORM\Column(type="string", length=45)
     */
    private $name;

    /**
     * @ORM\Column(type="float")
     */
    private $moisture;

    /**
     * @ORM\Column(type="integer")
     */
    private $temperature;

    /**
     * @ORM\Column(type="integer")
     */
    private $light_level;

    /**
     * @ORM\Column(type="integer")
     */
    private $light_duraion;

    /**
     * @ORM\Column(type="integer")
     */
    private $user_id;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdSettingsPlant(): ?int
    {
        return $this->id_settings_plant;
    }

    public function setIdSettingsPlant(int $id_settings_plant): self
    {
        $this->id_settings_plant = $id_settings_plant;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getMoisture(): ?float
    {
        return $this->moisture;
    }

    public function setMoisture(float $moisture): self
    {
        $this->moisture = $moisture;

        return $this;
    }

    public function getTemperature(): ?int
    {
        return $this->temperature;
    }

    public function setTemperature(int $temperature): self
    {
        $this->temperature = $temperature;

        return $this;
    }

    public function getLightLevel(): ?int
    {
        return $this->light_level;
    }

    public function setLightLevel(int $light_level): self
    {
        $this->light_level = $light_level;

        return $this;
    }

    public function getLightDuraion(): ?int
    {
        return $this->light_duraion;
    }

    public function setLightDuraion(int $light_duraion): self
    {
        $this->light_duraion = $light_duraion;

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
