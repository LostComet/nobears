<?php

namespace App\Filter;

use ApiPlatform\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query\Parameter;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\PropertyInfo\Type;

final class RadiusFilter extends AbstractFilter
{
    protected function filterProperty(string $property, $value, QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, Operation $operation = null, array $context = []): void
    {
        if (
            $property !== 'radius' ||
            empty($value['distance']) || !is_numeric($value['distance']) ||
            empty($value['latitude']) || !is_numeric($value['latitude']) ||
            empty($value['longitude']) || !is_numeric($value['longitude'])
        ) {
            return;
        }

        $queryBuilder
            ->addSelect(sprintf('
                (
                    6371 * acos(
                        cos(radians(:latitude)) *
                        cos(radians(%1$s.latitude)) *
                        cos(
                            radians(%1$s.longitude) -
                            radians(:longitude)
                        ) +
                        sin(radians(:latitude)) *
                        sin(radians(%1$s.latitude))
                    )
                ) AS distance
            ', $queryBuilder->getRootAliases()[0]))
            ->having('distance < :distance')
            ->orderBy('distance', 'ASC')
            ->setParameters(new ArrayCollection([
                new Parameter('distance', $value['distance']),
                new Parameter('latitude', $value['latitude']),
                new Parameter('longitude', $value['longitude']),
            ]))
        ;
    }

    public function getDescription(string $resourceClass): array
    {
        $description['radius[distance]'] = [
            'property' => null,
            'type' => Type::BUILTIN_TYPE_INT,
            'required' => false,
        ];

        $description['radius[latitude]'] = [
            'property' => null,
            'type' => Type::BUILTIN_TYPE_FLOAT,
            'required' => false,
        ];

        $description['radius[longitude]'] = [
            'property' => null,
            'type' => Type::BUILTIN_TYPE_FLOAT,
            'required' => false,
        ];

        return $description;
    }
}