<?php

namespace Oro\Bundle\MagentoBundle\EventListener;

use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

use Oro\Bundle\AccountBundle\Entity\Account;
use Oro\Bundle\ContactBundle\Entity\Contact;
use Oro\Bundle\EntityBundle\ORM\DoctrineHelper;
use Oro\Bundle\MagentoBundle\Entity\Customer;
use Oro\Bundle\SearchBundle\Engine\IndexerInterface;
use Oro\Bundle\SearchBundle\EventListener\IndexListener;

class SearchIndexListener extends IndexListener
{
    /** @var array */
    private $entityDependencies = [
        Customer::class => [
            Account::class,
            Contact::class,
        ]
    ];

    /**
     * {@inheritdoc}
     */
    public function __construct(
        DoctrineHelper $doctrineHelper,
        IndexerInterface $searchIndexer,
        PropertyAccessorInterface $propertyAccessor
    ) {
        parent::__construct($doctrineHelper, $searchIndexer, $propertyAccessor);
    }

    /**
     * {@inheritdoc}
     */
    protected function getAssociatedEntitiesToReindex(EntityManager $entityManager, $entities)
    {
        $entitiesToReindex = [];

        foreach ($entities as $entity) {
            $className = ClassUtils::getClass($entity);
            $meta = $entityManager->getClassMetadata($className);

            foreach ($meta->getAssociationMappings() as $association) {
                if ($association['type'] === ClassMetadataInfo::MANY_TO_ONE
                    && $this->hasEntityDependencies($className, $association['targetEntity'])) {
                    $associationValue = $this->propertyAccessor->getValue($entity, $association['fieldName']);
                    if (is_object($associationValue)) {
                        $entitiesToReindex[spl_object_hash($associationValue)] = $associationValue;
                    }
                }
            }
        }

        return $entitiesToReindex;
    }

    /**
     * @param string $entityClass
     * @param string $targetClass
     *
     * @return bool
     */
    private function hasEntityDependencies($entityClass, $targetClass)
    {
        return array_key_exists($entityClass, $this->entityDependencies)
            && in_array($targetClass, $this->entityDependencies[$entityClass], null);
    }
}
