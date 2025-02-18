<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ORM\Table(name: '`order`')]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'orders')]
    #[ORM\JoinColumn(nullable: false)]
    private $user;

    #[ORM\Column( options: ['default' => 'CURRENT_TIMESTAMP'])]
    private ?\DateTimeImmutable $createdAt = null ;

    #[ORM\Column(type: 'string', length: 255)]
    private $carrierName;

    #[ORM\Column(type: 'integer', length: 255)]
    private $carrierPrice;

    #[ORM\Column(type: 'text')]
    private $delivery;

    #[ORM\OneToMany(mappedBy: 'bindedOrder', targetEntity: OrderDetails::class)]
    private $orderDetails;

    #[ORM\Column(type: 'string', length: 255)]
    private $reference;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $stripeSession;

    #[ORM\Column(type: 'integer')]
    private $state;

    #[ORM\Column(type:'float')]
    private ?float $total = null;

    #[ORM\ManyToOne(inversedBy: 'orders')]
    private ?Carrier $carriers = null;

    public function __construct()
    {
        $this->orderDetails = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }


    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    
    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCarrierName(): ?string
    {
        return $this->carrierName;
    }

    public function setCarrierName(string $carrierName): self
    {
        $this->carrierName = $carrierName;

        return $this;
    }

    public function getCarrierPrice(): ?string
    {
        return $this->carrierPrice;
    }

    public function setCarrierPrice(string $carrierPrice): self
    {
        $this->carrierPrice = $carrierPrice;

        return $this;
    }

    public function getDelivery(): ?string
    {
        return $this->delivery;
    }

    public function setDelivery(string $delivery): self
    {
        $this->delivery = $delivery;

        return $this;
    }

    /**
     * @return Collection|OrderDetails[]
     */
    public function getOrderDetails(): Collection
    {
        return $this->orderDetails;
    }

    public function addOderDetail(OrderDetails $oderDetail): self
    {
        if (!$this->orderDetails->contains($oderDetail)) {
            $this->orderDetails[] = $oderDetail;
            $oderDetail->setBindedOrder($this);
        }

        return $this;
    }

    public function removeOderDetail(OrderDetails $oderDetail): self
    {
        if ($this->orderDetails->removeElement($oderDetail)) {
            // set the owning side to null (unless already changed)
            if ($oderDetail->getBindedOrder() === $this) {
                $oderDetail->setBindedOrder(null);
            }
        }

        return $this;
    }

   //[ORM\PreFlush]
   //public function preFlush()
    //{
    //    $total = 0;
    //    foreach ($this->getOrderDetails() as $item) {
    //        $total += $item->getTotal();
    //    }
    //    $this->total =$total;
  
   // }

    public function getTotalQuantity():float
    {
        $total = 0;
        foreach ($this->getOrderDetails() as $product) {
            $total += $product->getQuantity();
        }
        return $total;
  
    }

    public function calculateCarrierPrice(float $totalPrice): void
    {
        // Le prix du transporteur est gratuit si le total dépasse 49 €
        if ($totalPrice > 49) {
            $this->carrierPrice = 0;
        } else {
            $this->carrierPrice = $this->carrierPrice; // Sinon, utiliser le prix du transporteur par défaut
        }
    }


    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    public function getStripeSession(): ?string
    {
        return $this->stripeSession;
    }

    public function setStripeSession(?string $stripeSession): self
    {
        $this->stripeSession = $stripeSession;

        return $this;
    }

    public function getState(): ?int
    {
        return $this->state;
    }

    public function setState(int $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getTotal(): ?int
    {
        return $this->total;
    }

    public function setTotal(int $total): static
    {
        $this->total = $total;

        return $this;
    }

    public function getCarriers(): ?Carrier
    {
        return $this->carriers;
    }

    public function setCarriers(?Carrier $carriers): static
    {
        $this->carriers = $carriers;

        return $this;
    }

    public function getStateAsString(): string
    {
        switch ($this->state) {
            case 0:
                return 'En attente';
            case 1:
                return 'Validée';
            case 2:
                return 'Expédiée';
            default:
                return 'Non défini';
        }
    }
}
