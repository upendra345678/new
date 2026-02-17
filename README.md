# Doctorly Dashboard for WooCommerce

## What this plugin does
- Creates a Doctorly-branded dashboard experience in WooCommerce My Account.
- Replaces default Orders nav with **Doctorly Packages** and **My Invoices**.
- Provides custom admin area: overview, package mapping manager, invoice manager, invoice design builder.
- Uses default WooCommerce gateways for payment collection.
- Generates invoice records automatically from successful package orders.

## Setup
1. Install and activate WooCommerce.
2. Upload this plugin and activate it.
3. Visit **Doctorly Dashboard** in wp-admin.
4. In **Doctorly Packages Manager** map each Doctorly plan to a WooCommerce product ID.
5. Optionally click **Create Sample Package Products** to auto-create hidden simple products for all plans.

## How product mapping works
- Plans are stored in option `doctorly_package_plans`.
- Each plan has a `product_id`, `duration`, `features`, and `visible` toggle.
- During AJAX buy flow, selected plan product is added to cart and user is redirected to WooCommerce checkout.

## Invoice system
- Invoice records are stored as custom post type `doctorly_invoice`.
- Invoice number sequence is generated using:
  - prefix option `doctorly_invoice_prefix`
  - last number option `doctorly_invoice_last_number`
- On `woocommerce_payment_complete` and processing status, plugin:
  - assigns plan to user meta
  - sets purchase and expiry dates
  - creates invoice metadata (amount, tax, payment method, txn id)

## Styling
- Frontend styles: `assets/css/frontend.css`
- Admin styles: `assets/css/admin.css`
- You can override by dequeuing these and loading your own theme/plugin style sheet.

## Notes
- Endpoints flushed on plugin activation/deactivation.
- Assets loaded only on My Account pages or Doctorly admin pages.
