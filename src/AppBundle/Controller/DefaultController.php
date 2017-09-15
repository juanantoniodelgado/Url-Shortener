<?php

namespace AppBundle\Controller;

use Doctrine\ORM\NoResultException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Form\ShortenUrlForm;
use AppBundle\Entity\StoredUrl;

class DefaultController extends Controller
{
    /**
     * Homepage.
     *
     * @param Request $request
     * @Route("/", name="homepage")
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $url = new StoredUrl();
        $form = $this->createForm(ShortenUrlForm::class, $url);
        $form->handleRequest($request);
        $error = false;

        if ($form->isSubmitted() && $form->isValid()) {

            $helper = $this->get('app.url_checker');
            $em = $this->getDoctrine()->getManager();

            if ($helper->isUrlValid($url->getOrigin())) {

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
     * @param string $token
     * @Route("/link-ready/{token}", name="success_page")
     * @return Response
     * @throws NotFoundHttpException
     */
    public function successPageAction($token)
    {
        $em = $this->getDoctrine()->getManager();
        $url = $em->getRepository('AppBundle:StoredUrl')->findByValidToken($token);

        if ($url === null) {

            throw new NotFoundHttpException();
        }

        return $this->render(':default:success.html.twig', array(
            'url' => $url,
            'token' => $token
        ));
    }

    /**
     * Api documentation page.
     *
     * @Route("/api-docs", name="api_docs")
     * @return Response
     */
    public function apiDocAction()
    {
        return $this->render(':default:api_docs.html.twig');
    }

    /**
     * Returns an encoded url from the given url.
     *
     * @Route("/api/{origin}", name="encodeByUrl", requirements={"origin"=".+"})
     * @param string $origin
     * @return JsonResponse
     */
    public function encodeByUrlAction($origin)
    {
        $helper = $this->get('app.url_checker');

        if ($helper->isUrlValid($origin)) {

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

            $response = array('status' => 'failed');
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
    public function redirectAction($token)
    {
        $em = $this->getDoctrine()->getManager();

        try {

            $origin = $em->getRepository('AppBundle:StoredUrl')->findByValidToken($token);
            return $this->redirect($origin['origin']);

        } catch(NoResultException $e) {

            throw new NotFoundHttpException();
        }
    }
}
