# Nerd Delay Plugin

## Description

Nerd Delay optimizes WordPress sites by deferring or asynchronously loading scripts, optimizing CSS, and improving Core Web Vitals. The plugin includes enhanced features for better performance and security.

## Features

- **Script and CSS Optimization**
  - Defer or asynchronously load JavaScript files.
  - Preload and defer CSS files.
  - Inline critical CSS for faster rendering.

- **Core Web Vitals Improvements**
  - Optimize Largest Contentful Paint (LCP) by preloading key resources and lazy loading images.
  - Improve First Input Delay (FID) by deferring non-critical JavaScript.
  - Reduce Cumulative Layout Shift (CLS) with font-display: swap and size attributes for images.

- **Security Enhancements**
  - Enable Gzip compression and browser caching.
  - Implement HTTP Strict Transport Security (HSTS).
  - Prevent clickjacking, MIME sniffing, and XSS attacks.
  - Disable directory browsing and block access to sensitive files.
  - Redirect HTTP to HTTPS and prevent hotlinking.
  - Block bad bots from accessing your site.

- **Advanced Features**
  - Detailed logging and error handling for debugging.
  - User feedback and notifications for actions.
  - Customizable settings for browser caching duration and more.
  - Compatibility checks with other plugins.
  - Internationalization support for multiple languages.
  - Backup and restore settings functionality.
  - Advanced script management with conditional loading.

- **.htaccess Rule Management**
  - Dedicated section for `.htaccess` optimizations with visual warning indicators.
  - Each `.htaccess` setting checks if it is already applied.
  - Displays a message next to the toggle switch if the rule is active, including the line numbers where it is found in the `.htaccess` file.
  - Prevents duplicate entries in the `.htaccess` file.
  - "Disable All" button to quickly turn off all `.htaccess` modifications.
  - Safety measures to prevent server configuration issues.

## Installation

1. Upload the plugin files to the `/wp-content/plugins/nerd-delay` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Use the Settings->Nerd Delay screen to configure the plugin.

## Frequently Asked Questions

### How do I configure the plugin?

Navigate to the Settings->Nerd Delay screen in your WordPress admin dashboard to configure the plugin settings.

### How can I backup and restore my settings?

Use the export and import functionality in the settings page to backup and restore your plugin settings.

### Are the .htaccess modifications safe?

The plugin includes safety measures for `.htaccess` modifications, including:
- Visual indicators showing which rules are active and their line numbers
- A dedicated section with warning colors to highlight potential impact
- A "Disable All" button to quickly turn off all `.htaccess` modifications
- Careful rule management to prevent duplicate entries

## Changelog

### 1.9.4
- Added dedicated section for `.htaccess` optimizations with visual warning indicators
- Implemented "Disable All" button for `.htaccess` settings
- Improved safety measures for server configuration modifications
- Enhanced UI with color-coded sections for different types of optimizations

### 1.9.3
- Added AJAX functionality for scanning and managing scripts and CSS.
- Enhanced security with nonce verification for AJAX requests.
- Improved user interface for selecting script and CSS loading options.
- Updated version display on the settings screen.
- Added `.htaccess` rule management to check for existing rules and display line numbers.

### 1.9.1
- Initial release with basic script and CSS optimization features.

## Support

For support, please visit [our support page](https://narcolepticnerd.com/support).

## License

This plugin is licensed under the GPL2 license.