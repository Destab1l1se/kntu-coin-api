<?php

namespace App\Controller;

use App\Entity\Block;
use App\Entity\BlockTransaction;
use App\Entity\CommittedTransaction;
use App\Services\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class MiningController extends AbstractController
{
    /**
     * @Route("/mining/get-data", name="get_mining_data", methods={"GET"})
     */
    public function getMiningData(): JsonResponse
    {
        $committedTransactionsRepo = $this->getDoctrine()->getRepository(
            CommittedTransaction::class
        );
        $approvedCommittedTransactions
                                   = $committedTransactionsRepo->findByApprovedForNextBlock(
            true
        );

        if (empty($approvedCommittedTransactions)) {
            $nonApprovedTransactions
                = $committedTransactionsRepo->findByApprovedForNextBlock(false);
            $em = $this->getDoctrine()->getManager();

            foreach ($nonApprovedTransactions as $nonApprovedTransaction) {
                $nonApprovedTransaction->setApprovedForNextBlock(true);

                $em->persist($nonApprovedTransaction);;
            }

            $em->flush();

            $approvedCommittedTransactions = $nonApprovedTransactions;
        }

        $committedTransactionsData = $this->getCommittedTransactionsData(
            $approvedCommittedTransactions
        );

        $blockRepo = $this->getDoctrine()->getRepository(
            Block::class
        );

        /** @var Block $lastBlock */
        $lastBlock = $blockRepo->findOneBy([], ['id' => 'DESC']);

        return new JsonResponse(
            [
                'committed_transactions' => $committedTransactionsData,
                'last_block_hash'        => $lastBlock->getHash(),
            ]
        );
    }

    /**
     * @Route("/mining/close-block", name="close_block", methods={"POST"})
     * @param Request     $request
     * @param UserService $userService
     *
     * @return JsonResponse
     */
    public function closeBlock(Request $request, UserService $userService
    ): JsonResponse {
        $parametersAsArray = [];
        if ($content = $request->getContent()) {
            $parametersAsArray = json_decode($content, true);
        }

        $expectedLastBlockHash = $parametersAsArray['last_block_hash'];
        $newNonce              = $parametersAsArray['new_nonce'];

        $blockRepo = $this->getDoctrine()->getRepository(
            Block::class
        );

        /** @var Block $actualLastBlock */
        $actualLastBlock = $blockRepo->findOneBy([], ['id' => 'DESC']);

        if ($actualLastBlock->getHash() !== $expectedLastBlockHash) {
            return new JsonResponse(
                [
                    'status'        => 'error',
                    'error_message' => 'Provided last block hash is not correct',
                ]
            );
        }
        $committedTransactionsRepo = $this->getDoctrine()->getRepository(
            CommittedTransaction::class
        );
        $approvedCommittedTransactions
                                   = $committedTransactionsRepo->findByApprovedForNextBlock(
            true
        );

        $committedTransactionsData = $this->getCommittedTransactionsData(
            $approvedCommittedTransactions
        );
        $jsonTransactions          = json_encode(
            $committedTransactionsData, JSON_UNESCAPED_SLASHES
        );
        $lastBlockHash             = $expectedLastBlockHash;

        $string  = $newNonce . $lastBlockHash . $jsonTransactions;
        $newHash = hash('sha256', $string);

        if (str_starts_with($newHash, '000')) {
            $em = $this->getDoctrine()->getManager();

            $newBlock = new Block();
            $newBlock->setPrevBlock($actualLastBlock);
            $newBlock->setHash($newHash);
            $newBlock->setNonce($newNonce);

            $em->persist($newBlock);
            $em->flush();

            foreach (
                $approvedCommittedTransactions as $approvedCommittedTransaction
            ) {
                $blockTransaction = new BlockTransaction();
                $blockTransaction->setBlock($newBlock);
                $blockTransaction->setCoinQuantity(
                    $approvedCommittedTransaction->getCoinQuantity()
                );
                $blockTransaction->setSender(
                    $approvedCommittedTransaction->getSender()
                );
                $blockTransaction->setReceiver(
                    $approvedCommittedTransaction->getReceiver()
                );

                $em->persist($blockTransaction);
                $em->remove($approvedCommittedTransaction);
            }

            $rewardTransaction = new CommittedTransaction();
            $rewardTransaction->setSender(null);
            $rewardTransaction->setCoinQuantity(50);
            $rewardTransaction->setReceiver($userService->getCurrentUser());

            $em->persist($rewardTransaction);

            $em->flush();

            return new JsonResponse(
                [
                    'status' => 'success',
                ]
            );
        } else {
            return new JsonResponse(
                [
                    'status'        => 'error',
                    'error_message' => 'Provided nonce is not correct',
                ]
            );
        }
    }

    protected
    function getCommittedTransactionsData(
        $committedTransactions
    ): array {
        $serializer = new Serializer([new ObjectNormalizer()]);

        return array_map(
            function ($transaction) use ($serializer) {
                return $serializer->normalize($transaction);
            }, $committedTransactions
        );
    }
}
