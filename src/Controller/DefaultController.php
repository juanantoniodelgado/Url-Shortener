<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use App\Form\ShortenUrlForm;
use App\Entity\StoredUrl;

class DefaultController extends AbstractController
{
    /**
     * Homepage.
     *
     * @Route("/", name="homepage")
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request): Response
    {
        $url = new StoredUrl();
        $form = $this->createForm(ShortenUrlForm::class, $url);
        $form->handleRequest($request);
        $error = false;

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            if (filter_var($url->getOrigin(), FILTER_VALIDATE_URL)) {

                $url->generateToken();
                $em->persist($url);
                $em->flush();

                return $this->redirectToRoute('success_page', array('token' => $url->getToken()));

            } else {

                // Submitted value is not a valid url
                $error = true;
            }
        }

        return $this->render('default/index.html.twig', array(
            'form' => $form->createView(),
            'error' => $error
        ));
    }

    /**
     * Success shortened link creation page.
     *
     * @Route("/link-ready/{token}", name="success_page")
     * @param string $token
     * @return Response
     * @throws NotFoundHttpException
     */
    public function successPageAction(string $token): Response
    {
        $em = $this->getDoctrine()->getManager();
        $url = $em->getRepository(StoredUrl::class)->findByValidToken($token);

        if ($url instanceof StoredUrl) {

            return $this->render('default/success.html.twig', array(
                'url' => $url,
                'token' => $token
            ));

        } else {

            throw new NotFoundHttpException();
        }
    }

    /**
     * Returns an encoded url from the given url.
     *
     * @Route("/api/{origin}", name="encodeByUrl", requirements={"origin"=".+"})
     * @param string $origin
     * @return JsonResponse
     */
    public function encodeByUrlAction(string $origin): JsonResponse
    {
        if (filter_var($origin, FILTER_VALIDATE_URL)) {

            $em = $this->getDoctrine()->getManager();
            $url = new StoredUrl($origin);

            $url->generateToken();
            $em->persist($url);
            $em->flush();

            $response = array(
                'status' => 'ok',
                'url' => $this->generateUrl('redirect', array('token' => $url->getToken()), UrlGeneratorInterface::ABSOLUTE_URL),
                'token' => $url->getToken()
            );

        } else {

            $response = array(
                'status' => 'error',
                'message' => 'Url not valid'
            );
        }

        return new JsonResponse($response);
    }

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
