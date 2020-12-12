<?php

namespace App\Entity;

use App\Repository\BlockRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=BlockRepository::class)
 */
class Block
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $hash;

    /**
     * @ORM\ManyToOne(targetEntity=Block::class)
     */
    private $prevBlock;

    /**
     * @ORM\OneToMany(targetEntity=BlockTransaction::class, mappedBy="block")
     */
    private $blockTransactions;

    /**
     * @ORM\Column(type="integer")
     */
    private $nonce;

    public function __construct()
    {
        $this->blockTransactions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHash(): ?string
    {
        return $this->hash;
    }

    public function setHash(string $hash): self
    {
        $this->hash = $hash;

        return $this;
    }

    public function getPrevBlock(): ?self
    {
        return $this->prevBlock;
    }

    public function setPrevBlock(?self $prevBlock): self
    {
        $this->prevBlock = $prevBlock;

        return $this;
    }

    /**
     * @return Collection|BlockTransaction[]
     */
    public function getBlockTransactions(): Collection
    {
        return $this->blockTransactions;
    }

    public function addBlockTransaction(BlockTransaction $blockTransaction
    ): self {
        if (!$this->blockTransactions->contains($blockTransaction)) {
            $this->blockTransactions[] = $blockTransaction;
            $blockTransaction->setBlock($this);
        }

        return $this;
    }

    public function removeBlockTransaction(BlockTransaction $blockTransaction
    ): self {
        if ($this->blockTransactions->removeElement($blockTransaction)) {
            // set the owning side to null (unless already changed)
            if ($blockTransaction->getBlock() === $this) {
                $blockTransaction->setBlock(null);
            }
        }

        return $this;
    }

    public function getNonce(): ?int
    {
        return $this->nonce;
    }

    public function setNonce(int $nonce): self
    {
        $this->nonce = $nonce;

        return $this;
    }
}
