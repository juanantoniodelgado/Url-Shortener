<?php

namespace App\Repository;

use AppBundle\Entity\StoredUrl;
use Doctrine\ORM\EntityRepository;

class StoredUrlRepository extends EntityRepository
{
    /**
     * Finds an url by its given valid token
     * @param $token
     * @return StoredUrl|null
     */
    public function findByValidToken($token)
    {
        return $this->createQueryBuilder('st')
            ->select('st.origin')
            ->where('st.token = :token')
            ->andWhere('st.valid = 1')
            ->setParameter('token', $token)
            ->getQuery()->getSingleResult();
    }
}