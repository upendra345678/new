# Doctorly Dashboard for WooCommerce

A production-ready plugin that delivers a Doctorly-branded SaaS dashboard inside WooCommerce My Account + wp-admin.

## Features
- Replaces My Account Orders with **Doctorly Packages** and **My Invoices**.
- Manages six package plans mapped to hidden WooCommerce products.
- AJAX package purchase flow that redirects to native WooCommerce checkout.
- Uses default WooCommerce payment gateways (no custom gateway).
- Automatically assigns purchased plan to user metadata.
- Automatically generates invoices after order reaches processing/completed.
- Invoice print and PDF download support.
- Admin invoice design builder (logo, GST, colors, prefix, numbering, field toggles).

## Plan Mapping (Admin)
1. Go to **Doctorly Dashboard → Doctorly Packages Manager**.
2. Map each plan to a WooCommerce product ID.
3. Set duration, feature list, and visibility.
4. Save changes.

> On activation, sample hidden WooCommerce products are created automatically for all six plans.

## Invoice System
- Trigger: WooCommerce order status `processing` or `completed`.
- Stores invoice records in internal `doctorly_invoice` post type.
- Invoice number sequence uses:
  - Prefix (`invoice_prefix`)
  - Current counter (`current_invoice_number`)
- Generates invoice details from order + customer + package metadata.
- Download action streams generated PDF from `SimplePdf` class.

## Styling the Dashboard
- Frontend styles: `assets/css/frontend.css`
- Admin styles: `assets/css/admin.css`
- Extend Doctorly branding by updating these files or enqueueing additional custom styles.

## Important Endpoints
- `doctorly-dashboard`
- `doctorly-packages`
- `doctorly-invoices`

## Security Notes
- Uses role checks (`manage_woocommerce`) for admin actions.
- Uses nonces for AJAX/admin-post actions.
- Sanitizes and escapes user input/output.
