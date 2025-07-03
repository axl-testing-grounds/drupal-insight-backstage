<?php

namespace Drupal\drupal_insight_backstage\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\drupal_insight_backstage\IdpInsightService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Returns responses for drupal_insight_backstage routes.
 */
class IdpInsightsController extends ControllerBase {


  /**
   * Drupal Insight Backstage Service.
   *
   * @var \Drupal\drupal_insight_backstage\IdpInsightService
   */
  protected $idpService;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('drupal_insight_backstage.service'),
    );
  }

  /**
   * Constructs a SystemInfoController object.
   *
   * @param \Drupal\drupal_insight_backstage\IdpInsightService $idp_service
   *   Drupal Insight Backstage Service.
   */
  public function __construct(IdpInsightService $idp_service) {
    $this->idpService = $idp_service;
  }

  /**
   * Builds the response.
   */
  public function buildData($req_name, Request $request) {
    $idpToken = $request->headers->get('idp-token');
    $methodName = 'get' . $req_name;

    // If status check.
    if ($req_name == 'setupcheck') {
      $result = [
        'status' => $this->idpService->checkIfValidRequest($idpToken),
      ];
      if (!$result['status']) {
        $result['token'] = $idpToken;
      }
      return new JsonResponse($result, 200);
    }

    $statusCode = 500;
    if ($this->idpService->checkIfValidRequest($idpToken) && method_exists($this->idpService, $methodName)) {
      $result = call_user_func([$this->idpService, $methodName]);
      $statusCode = 200;
    }
    else {
      $result = [
        'error' => 'Invalid request',
      ];
    }

    return new JsonResponse($result, $statusCode);
  }

}
