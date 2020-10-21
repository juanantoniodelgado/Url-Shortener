<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\StoredUrl;

class EncodeURLController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Returns an encoded url from the given url.
     *
     * @Route("/encode", name="encode", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function encodeByUrlAction(Request $request): JsonResponse
    {
        $origin = $request->get('origin');

        if (!empty($origin) && filter_var($origin, FILTER_VALIDATE_URL)) {

            $url = new StoredUrl($origin);

            $url->generateToken();
            $this->em->persist($url);
            $this->em->flush();

            $response = array(
                'url' => $this->generateUrl('redirect', array(
                    'token' => $url->getToken()), UrlGeneratorInterface::ABSOLUTE_URL
                ),
                'token' => $url->getToken()
            );

            return new JsonResponse($response, Response::HTTP_OK);

        } else {

            return new JsonResponse(['message' => 'Origin URL not valid'], Response::HTTP_BAD_REQUEST);
        }
    }
}
