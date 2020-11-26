<?php

namespace App\Controller;

use App\Entity\CommittedTransaction;
use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class RegisterController extends AbstractController
{
    /**
     * @Route("/register", name="register", methods={"POST"})
     *
     * @param ValidatorInterface           $validator
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param Request                      $request
     *
     * @param JWTTokenManagerInterface     $JWTManager
     *
     * @return Response
     */
    public function register(
        Request $request, ValidatorInterface $validator,
        UserPasswordEncoderInterface $passwordEncoder,
        JWTTokenManagerInterface $JWTManager
    ): Response {
        $entityManager    = $this->getDoctrine()->getManager();
        $user             = new User();
        $validationErrors = [];

        $parametersAsArray = [];
        if ($content = $request->getContent()) {
            $parametersAsArray = json_decode($content, true);
        }

        $user->setFirstName($parametersAsArray['first_name']);
        $user->setLastName($parametersAsArray['last_name']);
        $user->setUsername($parametersAsArray['username']);

        $plainPassword = $parametersAsArray['password'];

        if (!$plainPassword) {
            $validationErrors['password'] = ['Password is required'];
        }

        $violations = $validator->validate($user);

        if (count($violations) > 0) {
            $validationErrors = array_merge(
                $validationErrors,
                $this->getValidationErrorsFromViolations($violations)
            );

            return new JsonResponse(['validation_errors' => $validationErrors]);
        }

        $encodedPassword = $passwordEncoder->encodePassword(
            $user, $plainPassword
        );
        $user->setPassword($encodedPassword);

        $user->setCoinBalance(0);

        $entityManager->persist($user);
        $entityManager->flush();

//        add 10k coin to balance right after registration
        $transaction = new CommittedTransaction();
        $transaction->setSender(null);
        $transaction->setReceiver($user);
        $transaction->setCoinQuantity(10000);

        $entityManager->persist($transaction);;
        $entityManager->flush();

        $token = $JWTManager->create($user);

        return new JsonResponse(['token' => $token]);
    }

    private function getValidationErrorsFromViolations(
        ConstraintViolationListInterface $violationList
    ) {
        $errors = [];

        foreach ($violationList as $violation) {
            $propertyPath = $violation->getPropertyPath();

            if (!isset($errors[$propertyPath])) {
                $errors[$propertyPath] = [];
            }

            array_push($errors[$propertyPath], $violation->getMessage());
        }

        return $errors;
    }

}
