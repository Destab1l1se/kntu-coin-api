<?php

namespace App\Entity;

use App\Repository\CommittedTransactionRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CommittedTransactionRepository::class)
 */
class CommittedTransaction
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
    private $coinQuantity;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $receiver;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     */
    private $sender;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCoinQuantity(): ?int
    {
        return $this->coinQuantity;
    }

    public function setCoinQuantity(int $coinQuantity): self
    {
        $this->coinQuantity = $coinQuantity;

        return $this;
    }

    public function getReceiver(): ?User
    {
        return $this->receiver;
    }

    public function setReceiver(?User $receiver): self
    {
        $this->receiver = $receiver;

        return $this;
    }

    public function getSender(): ?User
    {
        return $this->sender;
    }

    public function setSender(?User $sender): self
    {
        $this->sender = $sender;

        return $this;
    }
}
