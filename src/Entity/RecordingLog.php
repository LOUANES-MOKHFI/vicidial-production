<?php

namespace App\Entity;

use App\Repository\RecordingLogRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RecordingLogRepository::class)
 */
class RecordingLog
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */

    private $recording_id;

    /**
     * @ORM\Column(type="integer")
     */
    private $leadId;

    /**
     * @ORM\Column(type="integer")
     */
    private $start_time;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $length_in_sec;

    /**
     * @ORM\Column(type="text")
     */
    private $filename;

    /**
     * @ORM\Column(type="text")
     */
    private $location;

    /**
     * @ORM\Column(type="integer")
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLeadId(): ?int
    {
        return $this->leadId;
    }

    public function setLeadId(int $leadId): self
    {
        $this->leadId = $leadId;

        return $this;
    }

    public function getStartTime(): ?string
    {
        return $this->start_time;
    }

    public function setStartTime(string $start_time): self
    {
        $this->start_time = $start_time;

        return $this;
    }

    public function getLengthInSec(): ?string
    {
        return $this->length_in_sec;
    }

    public function setLengthInSec(string $length_in_sec): self
    {
        $this->length_in_sec = $length_in_sec;

        return $this;
    }

    public function getRecordingId(): ?int
    {
        return $this->recording_id;
    }

    public function setRecordingId(int $recording_id): self
    {
        $this->recording_id = $recording_id;

        return $this;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): self
    {
        $this->filename = $filename;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(string $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getUser(): ?string
    {
        return $this->user;
    }

    public function setUser(string $user): self
    {
        $this->user = $user;

        return $this;
    }
}
