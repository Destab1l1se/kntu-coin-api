<?php

namespace App\Entity;

use App\Repository\BlockTransactionRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=BlockTransactionRepository::class)
 */
class BlockTransaction
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Block::class, inversedBy="blockTransactions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $block;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     */
    private $sender;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $receiver;

    /**
     * @ORM\Column(type="integer")
     */
    private $coinQuantity;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBlock(): ?Block
    {
        return $this->block;
    }

    public function setBlock(?Block $block): self
    {
        $this->block = $block;

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

    public function getReceiver(): ?User
    {
        return $this->receiver;
    }

    public function setReceiver(?User $receiver): self
    {
        $this->receiver = $receiver;

        return $this;
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
}
