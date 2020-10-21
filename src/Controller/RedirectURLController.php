<?php

namespace App\Controller;

use App\Entity\StoredUrl;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class RedirectURLController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Redirects to the original url from its token.
     *
     * @Route("/r/{token}", name="redirect", methods={"GET"})
     * @param string $token
     * @return RedirectResponse
     * @throws NotFoundHttpException
     */
    public function redirectAction(string $token): RedirectResponse
    {
        $origin = $this->em->getRepository(StoredUrl::class)->findByValidToken($token);

        if ($origin instanceof StoredUrl) {

            return $this->redirect($origin->getOrigin());

        } else {

            throw new NotFoundHttpException();
        }
    }
}