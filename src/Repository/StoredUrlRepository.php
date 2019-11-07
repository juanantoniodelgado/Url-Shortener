<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\StoredUrl;
use App\Exception\UrlNotFoundException;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\UnexpectedResultException;

final class StoredUrlRepository extends EntityRepository
{
    /**
     * Finds an url by its given valid token
     *
     * @param string $token
     * @return StoredUrl
     * @throws UrlNotFoundException
     */
    public function findByValidToken(string $token): StoredUrl
    {
        try {
            return $this->createQueryBuilder('st')
                ->select('st.origin')
                ->where('st.token = :token')
                ->andWhere('st.valid = 1')
                ->setParameter('token', $token)
                ->getQuery()->getSingleResult();

        } catch (UnexpectedResultException $exception) {

            throw new UrlNotFoundException();
        }
    }
}