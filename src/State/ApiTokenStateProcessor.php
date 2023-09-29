<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\ApiToken;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class ApiTokenStateProcessor implements ProcessorInterface
{
    public function __construct(
        private UserPasswordHasherInterface $userPasswordHasher,
        private EntityManagerInterface $entityManager
    )
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): ApiToken
    {
        if (
            empty($data->getUser()->getEmail()) ||
            empty($data->getUser()->getPlainPassword())
        ) {
            throw new AuthenticationException('Please specify user email/password.');
        }

        $user = $this->entityManager
            ->getRepository(User::class)
            ->findOneBy([
                'email' => $data->getUser()->getEmail()
            ])
        ;

        if (!$this->userPasswordHasher->isPasswordValid($user, $data->getUser()->getPlainPassword())) {
            throw new AuthenticationException('Incorrect email/password combination');
        }

        $apiToken = new ApiToken();
        $apiToken->setUser($user);
        $apiToken->setExpiresAt(new \DateTimeImmutable());

        $this->entityManager->persist($apiToken);
        $this->entityManager->flush();

        return $apiToken->setUser(null); // Hides user credentials for security reasons
    }
}
