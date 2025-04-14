# Nerd Delay Plugin

Nerd Delay is a WordPress plugin designed to optimize your website by deferring or asynchronously loading scripts, optimizing CSS, and improving Core Web Vitals. This plugin helps enhance your site's performance and user experience.

## Description
Nerd Delay optimizes WordPress sites by deferring or asynchronously loading scripts, optimizing CSS, and improving Core Web Vitals. It now includes optional `.htaccess` optimizations for enhanced performance and security.

## Plugin Information

- **Plugin Name:** Nerd Delay
- **Plugin URI:** [https://narcolepticnerd.com](https://narcolepticnerd.com)
- **Description:** Optimize WordPress sites by deferring or asynchronously loading scripts, optimizing CSS, and improving Core Web Vitals.
- **Version:** 1.9.1
- **Author:** NarcolepticNerd
- **Author URI:** [https://narcolepticnerd.com](https://narcolepticnerd.com)
- **License:** GPL2

## Features

- Defer or async JavaScript loading.
- Preload or inline critical CSS.
- Lazy load images to improve Largest Contentful Paint (LCP).
- Font display swap to improve Cumulative Layout Shift (CLS).
- Optional `.htaccess` optimizations:
  - Enable Gzip compression.
  - Enable browser caching.
  - Enable HTTP Strict Transport Security (HSTS).
  - Prevent clickjacking attacks.
  - Prevent MIME sniffing.
  - Enable XSS protection.
  - Disable directory browsing.
  - Block access to sensitive files.
  - Redirect HTTP to HTTPS.
  - Prevent hotlinking.
  - Block bad bots.
- **New in 1.9.1:** 
  - Fully implemented `.htaccess` optimization functions for better performance and security.
- **New in 1.9:** 
  - Disable XML-RPC.
  - Disable REST API for non-authenticated users.
  - Enable Content Security Policy (CSP).
  - Enable Referrer Policy.
  - Enable DNS Prefetching.
  - Disable WordPress Heartbeat API.
  - Disable WordPress Emojis.
  - Enable HTTP/2 Push.
  - Enable automatic database optimization and cleanup.
- **New in 1.8:** Advanced script grouping for better control over script loading order.
- **New in 1.8:** Improved lazy loading for background images.

## Installation

1. Upload the plugin files to the `/wp-content/plugins/nerd-delay` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Configure settings under "Settings > Nerd Delay".

## Usage

1. **Settings Page:** Access the Nerd Delay settings page under 'Settings' or 'Tools' in the WordPress admin dashboard.
2. **Configure Options:** Choose your desired options for deferring scripts, preloading CSS, inlining critical CSS, and more.
3. **Scan for Assets:** Click the "Scan for Scripts and CSS" button to detect assets on your site.
4. **Save Configuration:** After scanning, select the desired load options for each asset and click "Save" to apply the changes.

## AJAX Handlers

- **Scan Assets:** The plugin includes an AJAX handler to scan the homepage for scripts and CSS files.
- **Save Assets:** Another AJAX handler is used to save the selected load options for scripts and CSS.

## Core Web Vitals Optimization

- **Largest Contentful Paint (LCP):** Preload key resources and lazy load images.
- **First Input Delay (FID):** Defer non-critical JavaScript.
- **Cumulative Layout Shift (CLS):** Use `font-display: swap` and set size attributes for images.

## Development

- **AJAX Handlers:** Implemented for scanning and saving assets.
- **JavaScript:** Used for handling user interactions and updating the UI dynamically.
- **PHP Functions:** Various functions are used to render settings fields, apply optimizations, and handle AJAX requests.

## Changelog
### 1.9.1
- Fully implemented `.htaccess` optimization functions for better performance and security.

### 1.9
- Added new settings:
  - Disable XML-RPC.
  - Disable REST API for non-authenticated users.
  - Enable Content Security Policy (CSP).
  - Enable Referrer Policy.
  - Enable DNS Prefetching.
  - Disable WordPress Heartbeat API.
  - Disable WordPress Emojis.
  - Enable HTTP/2 Push.
  - Enable automatic database optimization and cleanup.
- Minor documentation updates.

### 1.8
- Added advanced script grouping for better control over script loading order.
- Improved lazy loading to support background images.
- Added new settings:
  - Disable XML-RPC.
  - Disable REST API for non-authenticated users.
  - Enable Content Security Policy (CSP).
  - Enable Referrer Policy.
  - Enable DNS Prefetching.
  - Disable WordPress Heartbeat API.
  - Disable WordPress Emojis.
  - Enable HTTP/2 Push.
  - Enable automatic database optimization and cleanup.
- Minor bug fixes and performance improvements.

### 1.7
- Added optional `.htaccess` optimizations:
  - Gzip compression.
  - Browser caching.
  - HSTS.
  - Clickjacking prevention.
  - MIME sniffing prevention.
  - XSS protection.
- Improved settings UI for better usability.

### 1.6
- Initial release.

## License

This plugin is licensed under the GPL2 license.

## Support

For support and inquiries, please visit [https://narcolepticnerd.com](https://narcolepticnerd.com).