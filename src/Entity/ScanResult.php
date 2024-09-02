<?php

// src/Entity/ScanResult.php
namespace App\Entity;

use App\Repository\ScanResultRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ScanResultRepository::class)]
class ScanResult
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: FileUpload::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $fileUpload;

    #[ORM\Column(type: 'integer')]
    private $vulnerabilitiesCount;

    #[ORM\Column(type: 'string', length: 255)]
    private $status;

    #[ORM\Column(type: 'datetime')]
    private $scannedAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFileUpload(): ?FileUpload
    {
        return $this->fileUpload;
    }

    public function setFileUpload(?FileUpload $fileUpload): self
    {
        $this->fileUpload = $fileUpload;

        return $this;
    }

    public function getVulnerabilitiesCount(): ?int
    {
        return $this->vulnerabilitiesCount;
    }

    public function setVulnerabilitiesCount(int $vulnerabilitiesCount): self
    {
        $this->vulnerabilitiesCount = $vulnerabilitiesCount;

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

    public function getScannedAt(): ?\DateTimeInterface
    {
        return $this->scannedAt;
    }

    public function setScannedAt(\DateTimeInterface $scannedAt): self
    {
        $this->scannedAt = $scannedAt;

        return $this;
    }
}
