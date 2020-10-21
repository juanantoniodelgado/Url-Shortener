<?php

namespace App\Controller;

use App\Entity\StoredUrl;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class RedirectURLController extends AbstractController
{
    /**
     * Redirects to the original url from its token.
     *
     * @Route("/{token}", name="redirect")
     * @param string $token
     * @return RedirectResponse
     * @throws NotFoundHttpException
     */
    public function redirectAction(string $token): RedirectResponse
    {
        $em = $this->getDoctrine()->getManager();

        $origin = $em->getRepository(StoredUrl::class)->findByValidToken($token);

        if ($origin instanceof StoredUrl) {

            return $this->redirect($origin->getOrigin());

        } else {

            throw new NotFoundHttpException();
        }
    }
}