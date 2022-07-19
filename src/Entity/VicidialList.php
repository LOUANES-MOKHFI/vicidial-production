<?php

namespace App\Entity;

use App\Repository\VicidialListRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=VicidialListRepository::class)
 * @ORM\Table(name="`vicidial_list`")
 */
class VicidialList
{
  

    /**
     * * @ORM\Id
     * @ORM\Column(type="integer")
     */
    private $lead_id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $status;

    /**
     * @ORM\Column(type="integer")
     */
    private $vendor_lead_code;

    /**
     * @ORM\Column(type="integer")
     */
    private $user;

    /**
     * @ORM\Column(type="integer")
     */
    private $list_id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $phone_number;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $first_name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $security_phrase;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $last_local_call_time;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $entry_date;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $modify_date;

    

    public function getLeadId(): ?int
    {
        return $this->lead_id;
    }

    public function setLeadId(int $lead_id): self
    {
        $this->lead_id = $lead_id;

        return $this;
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

    public function getVendorLeadCode(): ?int
    {
        return $this->vendor_lead_code;
    }

    public function setVendorLeadCode(int $vendor_lead_code): self
    {
        $this->vendor_lead_code = $vendor_lead_code;

        return $this;
    }

    public function getUser(): ?int
    {
        return $this->user;
    }

    public function setUser(int $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getListId(): ?int
    {
        return $this->list_id;
    }

    public function setListId(int $list_id): self
    {
        $this->list_id = $list_id;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phone_number;
    }

    public function setPhoneNumber(string $phone_number): self
    {
        $this->phone_number = $phone_number;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->first_name;
    }

    public function setFirstName(string $first_name): self
    {
        $this->first_name = $first_name;

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

    public function getSecurityPhrase(): ?string
    {
        return $this->security_phrase;
    }

    public function setSecurityPhrase(string $security_phrase): self
    {
        $this->security_phrase = $security_phrase;

        return $this;
    }

    public function getLastLocalCallTime(): ?string
    {
        return $this->last_local_call_time;
    }

    public function setLastLocalCallTime(string $last_local_call_time): self
    {
        $this->last_local_call_time = $last_local_call_time;

        return $this;
    }

    public function getEntryDate(): ?string
    {
        return $this->entry_date;
    }

    public function setEntryDate(string $entry_date): self
    {
        $this->entry_date = $entry_date;

        return $this;
    }

    public function getModifyDate(): ?string
    {
        return $this->modify_date;
    }

    public function setModifyDate(string $modify_date): self
    {
        $this->modify_date = $modify_date;

        return $this;
    }
}
