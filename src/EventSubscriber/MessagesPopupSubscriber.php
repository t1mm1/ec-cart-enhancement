<?php

namespace Drupal\ec_cart_enhancements\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\OpenModalDialogCommand;
use Drupal\Core\Url;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Messenger\MessengerInterface;

/**
 * Shows popup with warnings for duplicate products.
 */
class MessagesPopupSubscriber implements EventSubscriberInterface {

  /**
   * Flag to prevent multiple popups.
   */
  protected static bool $popupAdded = FALSE;

  /**
   * The entity type manager.
   */
  protected EntityTypeManagerInterface $entityTypeManager;

  /**
   * The messenger service.
   */
  protected MessengerInterface $messenger;

  /**
   * Constructs a new LicenseWarningPopupSubscriber.
   */
  public function __construct(
    EntityTypeManagerInterface $entity_type_manager,
    MessengerInterface $messenger
  ) {
    $this->entityTypeManager = $entity_type_manager;
    $this->messenger = $messenger;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents(): array {
    return [
      KernelEvents::RESPONSE => ['onResponse', -50],
    ];
  }

  /**
   * Show popup for duplicate licensed products.
   */
  public function onResponse(ResponseEvent $event): void {
    $response = $event->getResponse();
    if (!$response instanceof AjaxResponse) {
      return;
    }

    if (self::$popupAdded) {
      return;
    }

    $purchased_entity = &ec_cart_enhancements_popup_entity_cache();
    if (!$purchased_entity) {
      return;
    }

    // Get messages from Request attributes.
    $request = $event->getRequest();
    $messages = $request->attributes->get('ec_cart_enhancements_messages', []);

    // Render popup.
    $view_builder = $this->entityTypeManager->getViewBuilder('commerce_product_variation');
    $content = [
      '#theme' => 'ec_cart_enhancement_popup',
      '#product_variation' => $view_builder->view($purchased_entity, 'dc_ajax_add_cart_popup'),
      '#product_variation_entity' => $purchased_entity,
      '#cart_url' => Url::fromRoute('commerce_cart.page')->toString(),
      '#messages' => $messages,
      '#has_messages' => !empty($messages),
    ];

    $response->addCommand(new OpenModalDialogCommand('', $content, ['width' => '700']));

    self::$popupAdded = TRUE;
    $purchased_entity = NULL;
  }

}
