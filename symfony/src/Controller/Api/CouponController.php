<?php

namespace App\Controller\Api;

use App\Entity\WebUrl;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/get-coupons")
 */
class CouponController extends ApiController
{
    /**
     * @Route("")
     * @Method("GET")
     */
    public function listAction(Request $request)
    {
        // $urls = $this->getDoctrine()->getRepository(WebUrl::class)->findAll();

        return $this->createApiResponse([
            ['brand' => 'Tesco', 'value' => $request->query->get('limit')],
            ['brand' => 'Sainsbury\'s', 'value' => 5],
          ]);
    }
}
