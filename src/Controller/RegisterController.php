<?php

namespace App\Controller;

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

        $user->setFirstName($request->request->get('first_name'));
        $user->setLastName($request->request->get('last_name'));
        $user->setUsername($request->request->get('username'));

        $plainPassword = $request->request->get('password');

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

        $entityManager->persist($user);
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
