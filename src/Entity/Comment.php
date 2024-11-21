<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Doctrine\Enum\TableEnum;
use App\Doctrine\Enum\RoleEnum;
use App\Doctrine\Traits\UuidTrait;
use Doctrine\ORM\Mapping as ORM;
use App\Doctrine\Traits\TimestampableTrait;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: TableEnum::COMMENT)]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Get(),
        new Post(security: RoleEnum::IS_GRANTED_USER),
        new Put(
            denormalizationContext: ['groups' => ['comment:update']],
            security: RoleEnum::IS_AUTHOR_OBJECT
        ),
        new Delete(security: RoleEnum::IS_ADMIN_OR_AUTHOR_OBJECT),
    ],
    order: ['createdAt' => 'DESC']
)]
#[ApiFilter(SearchFilter::class, properties: ['content.slug' => 'exact'])]
class Comment
{
    use UuidTrait;
    use TimestampableTrait;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank]
    #[Groups(['comment:update'])]
    public string $message;

    #[ORM\ManyToOne(targetEntity: Content::class)]
    public ?Content $content = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ApiProperty(writable: false)]
    public ?User $author = null;
}
