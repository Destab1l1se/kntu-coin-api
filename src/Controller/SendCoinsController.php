<?php

namespace App\Controller;

use App\Entity\CommittedTransaction;
use App\Entity\User;
use App\Services\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SendCoinsController extends AbstractController
{
    /**
     * @Route("/send-coins", name="send_coins")
     * @param Request $request
     * @param UserService $userService
     *
     * @return JsonResponse
     */
    public function sendCoins(Request $request, UserService $userService
    ): JsonResponse {
        $parametersAsArray = [];
        if ($content = $request->getContent()) {
            $parametersAsArray = json_decode($content, true);
        }

        $validationErrors = [];

        if (!isset($parametersAsArray['receiver_username'])) {
            $validationErrors['receiver_username']
                = ['Receiver username should not be blank'];
        } else {
            $userRepo = $this->getDoctrine()->getRepository(User::class);
            $receiver = $userRepo->findOneBy(
                ['username' => $parametersAsArray['receiver_username']]
            );

//            todo: check if is not himself

            if (!$receiver instanceof User) {
                $validationErrors['receiver_username']
                    = ['Given username does not exist'];
            }
        }

        if (!isset($parametersAsArray['coin_quantity'])) {
            $validationErrors['coin_quantity']
                = ['Coin quantity should not be blank'];
        } else {
            $coinQuantityString = $parametersAsArray['coin_quantity'];

            $coinQuantity = (int)filter_var(
                $coinQuantityString, FILTER_SANITIZE_NUMBER_INT
            );
            if (!$coinQuantity || $coinQuantity < 0) {
                $validationErrors['coin_quantity']
                    = ['Coin quantity should be integer value greater than zero'];
            }
        }

//        todo: add check if user have enough money to send

        if (count($validationErrors) > 0) {
            return new JsonResponse(['validation_errors' => $validationErrors]);
        }

        $sender = $userService->getCurrentUser();

        $transaction = new CommittedTransaction();
        $transaction->setSender($sender);
        $transaction->setReceiver($receiver);
        $transaction->setCoinQuantity($coinQuantity);

        $em = $this->getDoctrine()->getManager();
        $em->persist($transaction);;
        $em->flush();

        return new JsonResponse(['status' => 'ok']);
    }
}
