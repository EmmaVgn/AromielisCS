<?php

namespace App\Entity;

use App\Repository\TagRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TagRepository::class)]
class Tag
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updateAt = null;

    /**
     * @var Collection<int, Advise>
     */
    #[ORM\ManyToMany(targetEntity: Advise::class, inversedBy: 'tags')]
    private Collection $advises;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $color = null;

    public function __construct()
    {
        $this->advises = new ArrayCollection();
        $this->updateAt = new \DateTimeImmutable();
    }

    public function __toString(): string
    {
        return $this->name;
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
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

    public function getUpdateAt(): ?\DateTimeImmutable
    {
        return $this->updateAt;
    }

    public function setUpdateAt(\DateTimeImmutable $updateAt): static
    {
        $this->updateAt = $updateAt;

        return $this;
    }

    /**
     * @return Collection<int, Advise>
     */
    public function getAdvises(): Collection
    {
        return $this->advises;
    }

    public function addAdvise(Advise $advise): static
    {
        if (!$this->advises->contains($advise)) {
            $this->advises->add($advise);
            $advise->addTag($this); // Synchronisation bidirectionnelle
        }

        return $this;
    }

    public function removeAdvise(Advise $advise): static
    {
        if ($this->advises->removeElement($advise)) {
            $advise->removeTag($this); // Synchronisation bidirectionnelle
        }

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(?string $color): static
    {
        $this->color = $color;

        return $this;
    }
}
