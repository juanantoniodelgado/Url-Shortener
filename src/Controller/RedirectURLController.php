<?php

namespace App\Controller;

use App\Entity\StoredUrl;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations\Get;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RedirectURLController extends AbstractFOSRestController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Redirects to the original url from its token.
     *
     * @Get("/r/{token}", name="redirect", requirements={"token"="\S*"})
     *
     * @param string $token
     * @return Response
     * @throws NotFoundHttpException
     */
    public function redirectAction(string $token): Response
    {
        $origin = $this->em->getRepository(StoredUrl::class)->findByValidToken($token);

        if ($origin instanceof StoredUrl) {

            return $this->handleView($this->redirectView($origin->getOrigin(), 302));

        } else {

            throw new NotFoundHttpException();
        }
    }
}