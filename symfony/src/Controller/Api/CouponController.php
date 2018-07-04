<?php

namespace App\Controller\Api;

use App\Annotation\SecureToken;
use App\Entity\Coupon;
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
     * @SecureToken
     */
    public function listAction(Request $request)
    {
        $query = [];

        if ($request->query->get('brand')) {
          $query['brand'] = $request->query->get('brand');
        }

        if ($request->query->get('value')) {
          $query['value'] = $request->query->get('value');
        }

        $limit = $request->query->get('limit')?:10;

        $coupons = $this->getDoctrine()->getRepository(Coupon::class)->findBy($query, null, $limit);
        $entityManager = $this->getDoctrine()->getManager();

        foreach ($coupons as $coupon) {
            $entityManager->remove($coupon);
        }

       $entityManager->flush();

        return $this->createApiResponse($coupons);
    }
}
