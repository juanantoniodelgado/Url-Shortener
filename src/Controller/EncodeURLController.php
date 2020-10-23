<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\StoredUrl;

class EncodeURLController extends AbstractFOSRestController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Returns an encoded url from the given url.
     *
     * @Post("/encode", name="encode")
     * @RequestParam(name="origin", strict=true, nullable=false)
     *
     * @param ParamFetcher $fetcher
     * @return JsonResponse
     */
    public function encodeByUrlAction(ParamFetcher $fetcher): Response
    {
        $origin = $fetcher->get('origin');

        if (filter_var($origin, FILTER_VALIDATE_URL)) {

            $url = new StoredUrl($origin);

            $url->generateToken();
            $this->em->persist($url);
            $this->em->flush();

            $response = [
                'url' => $this->generateUrl(
                    'redirect',
                    ['token' => $url->getToken()],
                    UrlGeneratorInterface::ABSOLUTE_URL
                ),
                'token' => $url->getToken()
            ];

            return $this->handleView($this->view($response, 200));

        } else {

            throw new BadRequestHttpException('Origin URL not valid');
        }
    }
}
