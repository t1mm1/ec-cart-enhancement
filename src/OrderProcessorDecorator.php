<?php

namespace Drupal\ec_cart_enhancements;

use Drupal\commerce_order\OrderProcessorInterface;
use Drupal\commerce_order\Entity\OrderInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Decorates Order Processor to preserve messages for popup.
 */
class OrderProcessorDecorator implements OrderProcessorInterface {

  /**
   * The decorated order processor.
   */
  protected OrderProcessorInterface $innerProcessor;

  /**
   * The messenger service.
   */
  protected MessengerInterface $messenger;

  /**
   * The request stack.
   */
  protected RequestStack $requestStack;

  /**
   * Constructs a new OrderProcessorDecorator.
   */
  public function __construct(
    OrderProcessorInterface $inner_processor,
    MessengerInterface $messenger,
    RequestStack $request_stack
  ) {
    $this->innerProcessor = $inner_processor;
    $this->messenger = $messenger;
    $this->requestStack = $request_stack;
  }

  /**
   * {@inheritdoc}
   */
  public function process(OrderInterface $order): void {
    // Run original processor
    $this->innerProcessor->process($order);

    // Get messages that License added
    $messages = $this->messenger->all();
    $output = [];

    if (isset($messages[MessengerInterface::TYPE_WARNING])) {
      foreach ($messages[MessengerInterface::TYPE_WARNING] as $message) {
        $output[] = [
          'type' => 'warning',
          'message' => $message,
        ];
      }
    }

    if (isset($messages[MessengerInterface::TYPE_ERROR])) {
      foreach ($messages[MessengerInterface::TYPE_ERROR] as $message) {
        $output[] = [
          'type' => 'error',
          'message' => $message,
        ];
      }
    }

    // Save to Request attributes
    if (!empty($output)) {
      $request = $this->requestStack->getCurrentRequest();
      $request?->attributes->set('ec_cart_enhancements_messages', $output);
    }
  }

}
