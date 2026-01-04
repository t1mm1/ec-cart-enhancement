# EC Cart View Enhancements

A Drupal module that provides enhanced functionality for the cart modal popup displayed after adding products to cart.

## Description

This module extends the functionality of the `dc_ajax_add_cart_popup` module by providing a customizable template for the cart modal window and adding support for Commerce License products. It solves common issues with displaying messages, warnings, and handling duplicate licensed product additions.

## Features

- **Custom Modal Template**: Provides a customizable Twig template for the cart popup modal
- **Message Display**: Shows system messages and warnings in the popup
- **Cart Items List**: Displays the list of items currently in the cart
- **Commerce License Support**: Seamlessly integrates with Commerce License module
- **Duplicate Prevention**: Handles duplicate licensed product additions gracefully, showing appropriate warnings for digital products that can only be added once
- **Enhanced User Experience**: Prevents modal issues when re-adding licensed products (e.g., downloadable files, digital content)

## Requirements

- Drupal 10 or 11
- Commerce Cart (`commerce_cart`)
- Commerce Order (`commerce_order`)
- DC AJAX Add Cart (`dc_ajax_add_cart:dc_ajax_add_cart`)
- DC AJAX Add Cart Popup (`dc_ajax_add_cart:dc_ajax_add_cart_popup`)
- Commerce License (optional, for licensed product support)

## Installation

1. Download and place the module in your `modules/custom` or `modules/contrib` directory
2. Enable the module using Drush:
   ```bash
   drush en ec_cart_enhancements
   ```
   Or through the Drupal admin interface at `admin/modules`

3. Clear cache:
   ```bash
   drush cr
   ```

## How It Works

### Order Processor Decorator

The module decorates the Commerce License order processor to preserve warning and error messages that would otherwise be lost during AJAX cart operations. These messages are stored in request attributes and later displayed in the popup.

### Event Subscriber

A Symfony event subscriber listens to response events and injects the enhanced modal dialog when products are added to cart. It renders:
- Product variation view
- System messages (warnings, errors)
- Cart summary
- Link to full cart page

### Custom Template

The module uses the `ec_cart-enhancement-popup.html.twig` template, which can be overridden in your theme for complete customization.

## Template Override

To customize the popup appearance, copy the template to your theme:

```
themes/YOUR_THEME/templates/ec-cart-enhancement-popup.html.twig
```

Available template variables:
- `product_variation`: Rendered product variation
- `product_variation_entity`: Product variation entity object
- `cart_url`: URL to the cart page
- `messages`: Array of messages (type and message content)
- `has_messages`: Boolean indicating if there are messages to display
- `carts`: Array of cart's data

## Use Cases

### Digital Products / Licensed Content

Perfect for sites selling:
- Downloadable files
- Software licenses
- Digital memberships
- Online courses
- Any product with license restrictions

### Enhanced User Feedback

Provides clear feedback when:
- User attempts to add a licensed product they already own
- Quantity restrictions apply
- Product cannot be added to cart for any reason

## CSS Customization

The module includes a CSS library that can be customized. Override styles by:

1. Creating your own CSS file in your theme
2. Modifying the styles in `css/styles.css` (if using the module's CSS)

## Technical Details

**Services:**
- `ec_cart_enhancements.order_processor`: Decorates the Commerce License order processor
- `ec_cart_enhancements.messages_popup_subscriber`: Event subscriber for popup display

**Decoration Priority:** 100 (runs after Commerce License processor)

## Troubleshooting

### Popup not appearing
- Ensure all required modules are enabled
- Clear Drupal cache
- Check browser console for JavaScript errors

### Messages not displaying
- Verify Commerce License is properly configured
- Check that products have license configuration
- Enable Drupal logging to see if messages are being generated

## Support and Contribution

This is a custom module. For issues or feature requests, please contact your development team or module maintainer.

## License

This module follows the same license as Drupal core (GPL v2 or later).

## Credits

Developed for Drupal Commerce 3.x and Drupal 10/11.

## Author

Pavel Kasianov.

Linkedin: https://www.linkedin.com/in/pkasianov/</br>
Drupal org: https://www.drupal.org/u/pkasianov
