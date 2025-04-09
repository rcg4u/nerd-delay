# Nerd Delay Plugin

Nerd Delay is a WordPress plugin designed to optimize your website by deferring or asynchronously loading scripts, optimizing CSS, and improving Core Web Vitals. This plugin helps enhance your site's performance and user experience.

## Plugin Information

- **Plugin Name:** Nerd Delay
- **Plugin URI:** [https://narcolepticnerd.com](https://narcolepticnerd.com)
- **Description:** Optimize WordPress sites by deferring or asynchronously loading scripts, optimizing CSS, and improving Core Web Vitals.
- **Version:** 1.6
- **Author:** NarcolepticNerd
- **Author URI:** [https://narcolepticnerd.com](https://narcolepticnerd.com)
- **License:** GPL2

## Features

- **Defer or Async Scripts:** Choose to defer or asynchronously load JavaScript files to improve page load times.
- **Preload CSS:** Preload CSS files to enhance the Largest Contentful Paint (LCP) metric.
- **Inline Critical CSS:** Inline critical CSS to improve rendering performance.
- **Defer Non-Critical CSS:** Defer loading of non-critical CSS to improve First Input Delay (FID).
- **Lazy Load Images:** Enable lazy loading for images to improve LCP.
- **Font Display Swap:** Use `font-display: swap` to improve Cumulative Layout Shift (CLS).

## Installation

1. Upload the `nerd-delay` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Configure the plugin settings under the 'Settings' or 'Tools' menu in the WordPress admin dashboard.

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

## License

This plugin is licensed under the GPL2 license.

## Support

For support and inquiries, please visit [https://narcolepticnerd.com](https://narcolepticnerd.com).