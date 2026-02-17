# Doctorly Dashboard for WooCommerce

## Installation
1. Upload the `doctorly-dashboard-for-woocommerce` folder to `/wp-content/plugins/`.
2. Activate **Doctorly Dashboard for WooCommerce**.
3. Ensure WooCommerce is active.
4. Visit **Doctorly Dashboard → Doctorly Packages Manager** to map/edit products.

## Assign Woo products to plans
- Plugin seeds 6 hidden WooCommerce simple products on activation.
- In **Doctorly Dashboard → Doctorly Packages Manager**, update each plan's Product ID, pricing, duration, feature list, and visibility.
- Save settings; package cards on My Account immediately reflect these values.

## Invoice system
- Invoice records are generated automatically when package orders move to `processing`/`completed`.
- Each invoice stores:
  - Invoice number (`prefix + sequence`)
  - Order ID
  - Plan slug
  - Payment method and transaction ID
  - Tax and totals
- Customers can open **My Invoices** and click **Print / PDF**.
- Admin can manage filters in **Doctorly Dashboard → Invoice Manager**.

## Styling dashboard
- Frontend styles: `assets/css/frontend.css`
- Admin styles: `assets/css/admin.css`
- JS flows:
  - frontend AJAX checkout: `assets/js/frontend.js`
  - admin logo uploader: `assets/js/admin.js`

## Notes
- Uses default WooCommerce payment gateways only.
- Loads CSS/JS only on account pages (frontend) and Doctorly admin pages.
- Endpoints: `doctorly-dashboard`, `doctorly-packages`, `doctorly-invoices`.
