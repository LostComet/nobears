<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use ApiPlatform\OpenApi\Model\Operation;
use ApiPlatform\OpenApi\Model\RequestBody;
use App\Repository\ApiTokenRepository;
use App\State\ApiTokenStateProcessor;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ApiTokenRepository::class)]
#[ApiResource(
    operations: [
        new Post(
            uriTemplate: 'authorize',
            openapi: new Operation(
                description: 'Creates and retrieves an API token by authenticating with user credentials.',
                requestBody: new RequestBody(
                    content: new \ArrayObject([
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'user' => [
                                        'properties' => [
                                            'email' => ['type' => 'string'],
                                            'password' => ['type' => 'string'],
                                        ],
                                    ],
                                ],
                            ],
                            'example' => [
                                'user' => [
                                    'email' => 'donald.duck@gmail.com',
                                    'password' => 'nobears',
                                ],
                            ],
                        ],
                    ]),
                ),
            ),
            processor: ApiTokenStateProcessor::class,
        ),
    ],
)]
class ApiToken
{
    CONST TOKEN_EXPIRES_MINUTES = 60;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $expiresAt = null;

    #[ORM\Column(length: 64)]
    private string $token;

    public function __construct()
    {
        $this->token = bin2hex(random_bytes(32));
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getExpiresAt(): ?\DateTimeImmutable
    {
        return $this->expiresAt;
    }

    public function setExpiresAt(?\DateTimeImmutable $expiresAt): static
    {
        $this->expiresAt = $expiresAt;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): static
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Tokens expires after 1 hour
     *
     * @return bool
     */
    public function isValid(): bool
    {
        $date = new \DateTimeImmutable();

        $dateExpired = $date->sub(new \DateInterval('PT' . self::TOKEN_EXPIRES_MINUTES . 'M'));

        if ($dateExpired > $this->expiresAt) {
            return false;
        }

        return true;
    }
}
