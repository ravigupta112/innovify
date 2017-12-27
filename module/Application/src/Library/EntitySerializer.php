<?php

namespace Application\Library;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Annotations\Annotation\Enum;

use Doctrine\ORM\Mapping\ClassMetadata,
    Doctrine\Common\Util\Inflector,
    Doctrine\ORM\EntityManager,
    Exception;

class EntitySerializer
{
    /**
     * @var Doctrine\ORM\EntityManager
     */
    protected $_em;
    /**
     * @var int
     */
    protected $_recursionDepth = 0;
    /**
     * @var int
     */
    protected $_maxRecursionDepth = 0;
    public function __construct($em)
    {
        $this->setEntityManager($em);
    }
    /**
     *
     * @return Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        return $this->_em;
    }
    public function setEntityManager(EntityManager $em)
    {
        $this->_em = $em;
        return $this;
    }
    protected function _serializeEntity($entity)
    {
        $className = get_class($entity);
        $metadata = $this->_em->getClassMetadata($className);
        $data = array();
        foreach ($metadata->fieldMappings as $field => $mapping) {
            $value = $metadata->reflFields[$field]->getValue($entity);
            $field = Inflector::tableize($field);
            if ($value instanceof \DateTime) {
                $data[$field] = (array)$value;
            } elseif (is_object($value)) {
                $data[$field] = (string)$value;
            } else {
                $data[$field] = $value;
            }
        }
        foreach ($metadata->associationMappings as $field => $mapping) {
            $key = Inflector::tableize($field);
            if ($mapping['isCascadeDetach']) {
                $data[$key] = $metadata->reflFields[$field]->getValue($entity);
                if (null !== $data[$key]) {
                    $data[$key] = $this->_serializeEntity($data[$key]);
                }
            } elseif ($mapping['isOwningSide'] && $mapping['type'] & ClassMetadata::TO_ONE) {
                if (null !== $metadata->reflFields[$field]->getValue($entity)) {
                    if ($this->_recursionDepth < $this->_maxRecursionDepth) {
                        $this->_recursionDepth++;
                        $data[$key] = $this->_serializeEntity(
                            $metadata->reflFields[$field]
                                ->getValue($entity)
                            );
                        $this->_recursionDepth--;
                    } else {
                        $data[$key] = $this->getEntityManager()
                            ->getUnitOfWork()
                            ->getEntityIdentifier(
                                $metadata->reflFields[$field]
                                    ->getValue($entity)
                                );
                    }
                } else {
                    $data[$key] = null;
                }
            }
        }
        return $data;
    }
    public function toArray($entity)
    {
        return $this->_serializeEntity($entity);
    }
    public function toJson($entity)
    {
        return json_encode($this->toArray($entity));
    }
    public function toXml($entity)
    {
        throw new Exception('Not yet implemented');
    }
    public function setMaxRecursionDepth($maxRecursionDepth)
    {
        $this->_maxRecursionDepth = $maxRecursionDepth;
    }
    public function getMaxRecursionDepth()
    {
        return $this->_maxRecursionDepth;
    }
}