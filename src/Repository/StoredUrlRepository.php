<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\StoredUrl;
use App\Exception\UrlNotFoundException;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\UnexpectedResultException;

final class StoredUrlRepository extends EntityRepository
{
    /**
     * Finds an url by its given valid token
     *
     * @param string $token
     * @return StoredUrl
     */
    public function findByValidToken(string $token): ? StoredUrl
    {
        try {
            return $this->createQueryBuilder('st')
                ->where('st.token = :token')
                ->andWhere('st.valid = 1')
                ->setParameter('token', $token)
                ->getQuery()->getSingleResult(AbstractQuery::HYDRATE_OBJECT);
        } catch (UnexpectedResultException $exception) {
            return null;
        }
    }
}