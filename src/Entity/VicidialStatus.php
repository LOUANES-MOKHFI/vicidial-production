<?php

namespace App\Entity;

use App\Repository\VicidialStatusRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=VicidialStatusRepository::class)
 * @ORM\Table(name="`vicidial_statuses`")
 */
class VicidialStatus
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string")
     */

    private $status;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $status_name;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getStatusName(): ?string
    {
        return $this->status_name;
    }

    public function setStatusName(string $status_name): self
    {
        $this->status_name = $status_name;

        return $this;
    }
}
