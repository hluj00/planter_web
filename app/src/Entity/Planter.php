<?php

namespace App\Entity;

use App\Repository\PlanterRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PlanterRepository::class)
 */
class Planter
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=40)
     */
    private $name;

    /**
     * @ORM\Column(type="integer")
     */
    private $user_id;

    /**
     * @ORM\Column(type="integer")
     */
    private $plant_presets_id;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $color;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getUserId(): ?int
    {
        return $this->user_id;
    }

    public function setUserId(int $user_id): self
    {
        $this->user_id = $user_id;

        return $this;
    }

    public function getPlantPresetsId(): ?int
    {
        return $this->plant_presets_id;
    }

    public function setPlantPresetsId(int $plant_presets_id): self
    {
        $this->plant_presets_id = $plant_presets_id;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor($color): self
    {
        $this->color = $color;

        return $this;
    }


}
