<?php

namespace App\Entity;

use App\Repository\EmployeeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EmployeeRepository::class)]
class Employee
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 60)]
    private ?string $name = null;

    #[ORM\Column(nullable: true)]
    private ?int $dependents = null;

    #[ORM\Column]
    private ?string $document = null;

    #[ORM\Column(length: 11)]
    private ?string $phone = null;

    #[ORM\ManyToOne(inversedBy: 'employeers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Department $department_id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getDependents(): ?int
    {
        return $this->dependents;
    }

    public function setDependents(?int $dependents): static
    {
        $this->dependents = $dependents;
        return $this;
    }

    public function getDocument(): ?int
    {
        return $this->document;
    }

    public function setDocument(string $document): static
    {
        $this->document = $document;
        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): static
    {
        $this->phone = $phone;
        return $this;
    }

    public function getDepartmentId(): ?Department
    {
        return $this->department_id;
    }

    public function setDepartmentId(?Department $department_id): static
    {
        $this->department_id = $department_id;
        return $this;
    }
}
