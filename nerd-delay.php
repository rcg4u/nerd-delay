<?php
/*
Plugin Name: Nerd Delay
Plugin URI: https://narcolepticnerd.com
Description: Optimize WordPress sites by deferring or asynchronously loading scripts, optimizing CSS, and improving Core Web Vitals.
Version: 1.9.3
Author: NarcolepticNerd
Author URI: https://narcolepticnerd.com
License: GPL2
*/

define('NERD_DELAY_VERSION', '1.9.3');

// Prevent direct access to the file
if (!defined('ABSPATH')) {
    exit;
}

// Add settings menu under "Settings" and "Tools"
add_action('admin_menu', 'nerd_delay_add_admin_menu');
function nerd_delay_add_admin_menu() {
    // Add under "Settings"
    add_options_page(
        'Nerd Delay Settings',
        'Nerd Delay',
        'manage_options',
        'nerd-delay',
        'nerd_delay_options_page'
    );

    // Add under "Tools"
    add_management_page(
        'Nerd Delay Tools',
        'Nerd Delay',
        'manage_options',
        'nerd-delay-tools',
        'nerd_delay_options_page'
    );
}

// Enqueue custom styles for the settings page
add_action('admin_enqueue_scripts', 'nerd_delay_enqueue_admin_styles');
function nerd_delay_enqueue_admin_styles($hook) {
    if ($hook === 'settings_page_nerd-delay' || $hook === 'tools_page_nerd-delay-tools') {
        wp_enqueue_style('nerd-delay-admin-styles', plugin_dir_url(__FILE__) . 'assets/css/admin-styles.css');
    }
}

// Register settings
add_action('admin_init', 'nerd_delay_settings_init');
function nerd_delay_settings_init() {
    register_setting('nerdDelay', 'nerd_delay_settings');

    add_settings_section(
        'nerd_delay_section',
        __('Nerd Delay Settings', 'nerd-delay'),
        'nerd_delay_settings_section_callback',
        'nerdDelay'
    );

    add_settings_section(
        'nerd_delay_htaccess_section',
        __('Htaccess Optimizations', 'nerd-delay'),
        'nerd_delay_htaccess_section_callback',
        'nerdDelay'
    );

    function nerd_delay_htaccess_section_callback() {
        echo '<div class="htaccess-warning" style="background-color: #ffebee; padding: 10px; border-radius: 4px; margin-bottom: 15px;">';
        echo '<p><strong>' . __('Warning:', 'nerd-delay') . '</strong> ' . __('These settings directly modify your physical .htaccess file. Improper configuration may cause server errors.', 'nerd-delay') . '</p>';
        echo '<button type="button" id="disable-all-htaccess" class="button button-secondary">' . __('Disable All Htaccess Options', 'nerd-delay') . '</button>';
        echo '</div>';
        
        // Add JavaScript to handle the "Disable All" button
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function($) {
                $('#disable-all-htaccess').on('click', function() {
                    if (confirm('<?php echo esc_js(__('Are you sure you want to disable all .htaccess optimizations? This will update your settings but won\'t remove existing rules from your .htaccess file.', 'nerd-delay')); ?>')) {
                        $('input[name^="nerd_delay_settings[htaccess_"]').prop('checked', false);
                        alert('<?php echo esc_js(__('All .htaccess options have been disabled. Click "Save Changes" to apply.', 'nerd-delay')); ?>');
                    }
                });
            });
        </script>
        <?php
    }

    add_action('admin_head', 'nerd_delay_htaccess_admin_styles');
    function nerd_delay_htaccess_admin_styles() {
        ?>
        <style type="text/css">
            .settings_page_nerd-delay #nerd_delay_htaccess_section,
            .tools_page_nerd-delay-tools #nerd_delay_htaccess_section {
                background-color: #ffebee;
                padding: 15px;
                border-radius: 4px;
                margin-bottom: 20px;
            }
            #disable-all-htaccess {
                margin-top: 10px;
            }
            .htaccess-warning {
                margin-bottom: 15px;
            }
        </style>
        <?php
    }
    add_settings_field(
        'nerd_delay_field_defer',
        __('Defer Scripts', 'nerd-delay'),
        'nerd_delay_field_defer_render',
        'nerdDelay',
        'nerd_delay_section'
    );

    add_settings_field(
        'nerd_delay_field_async',
        __('Async Scripts', 'nerd-delay'),
        'nerd_delay_field_async_render',
        'nerdDelay',
        'nerd_delay_section'
    );

    add_settings_field(
        'nerd_delay_field_preload_css',
        __('Preload CSS', 'nerd-delay'),
        'nerd_delay_field_preload_css_render',
        'nerdDelay',
        'nerd_delay_section'
    );

    add_settings_field(
        'nerd_delay_field_inline_css',
        __('Inline Critical CSS', 'nerd-delay'),
        'nerd_delay_field_inline_css_render',
        'nerdDelay',
        'nerd_delay_section'
    );

    add_settings_field(
        'nerd_delay_field_defer_css',
        __('Defer Non-Critical CSS', 'nerd-delay'),
        'nerd_delay_field_defer_css_render',
        'nerdDelay',
        'nerd_delay_section'
    );

    add_settings_field(
        'nerd_delay_field_lazy_load_images',
        __('Lazy Load Images', 'nerd-delay'),
        'nerd_delay_field_lazy_load_images_render',
        'nerdDelay',
        'nerd_delay_section'
    );

    add_settings_field(
        'nerd_delay_field_font_display',
        __('Font Display Swap', 'nerd-delay'),
        'nerd_delay_field_font_display_render',
        'nerdDelay',
        'nerd_delay_section'
    );

    // Add new settings fields for .htaccess optimizations
    add_settings_field(
        'nerd_delay_field_htaccess_gzip',
        __('Enable Gzip Compression', 'nerd-delay'),
        'nerd_delay_field_htaccess_gzip_render',
        'nerdDelay',
        'nerd_delay_htaccess_section'
    );

    add_settings_field(
        'nerd_delay_field_htaccess_browser_caching',
        __('Enable Browser Caching', 'nerd-delay'),
        'nerd_delay_field_htaccess_browser_caching_render',
        'nerdDelay',
        'nerd_delay_htaccess_section'
    );
    add_settings_field(
        'nerd_delay_field_htaccess_hsts',
        __('Enable HTTP Strict Transport Security (HSTS)', 'nerd-delay'),
        'nerd_delay_field_htaccess_hsts_render',
        'nerdDelay',
        'nerd_delay_htaccess_section'
    );

    add_settings_field(
        'nerd_delay_field_htaccess_clickjacking',
        __('Prevent Clickjacking', 'nerd-delay'),
        'nerd_delay_field_htaccess_clickjacking_render',
        'nerdDelay',
        'nerd_delay_htaccess_section'
    );

    add_settings_field(
        'nerd_delay_field_htaccess_mime_sniffing',
        __('Prevent MIME Sniffing', 'nerd-delay'),
        'nerd_delay_field_htaccess_mime_sniffing_render',
        'nerdDelay',
        'nerd_delay_htaccess_section'
    );

    add_settings_field(
        'nerd_delay_field_htaccess_xss_protection',
        __('Enable XSS Protection', 'nerd-delay'),
        'nerd_delay_field_htaccess_xss_protection_render',
        'nerdDelay',
        'nerd_delay_htaccess_section'
    );

    add_settings_field(
        'nerd_delay_field_htaccess_disable_directory_browsing',
        __('Disable Directory Browsing', 'nerd-delay'),
        'nerd_delay_field_htaccess_disable_directory_browsing_render',
        'nerdDelay',
        'nerd_delay_htaccess_section'
    );

    add_settings_field(
        'nerd_delay_field_htaccess_block_sensitive_files',
        __('Block Sensitive Files', 'nerd-delay'),
        'nerd_delay_field_htaccess_block_sensitive_files_render',
        'nerdDelay',
        'nerd_delay_htaccess_section'
    );

    add_settings_field(
        'nerd_delay_field_htaccess_redirect_http_to_https',
        __('Redirect HTTP to HTTPS', 'nerd-delay'),
        'nerd_delay_field_htaccess_redirect_http_to_https_render',
        'nerdDelay',
        'nerd_delay_htaccess_section'
    );

    add_settings_field(
        'nerd_delay_field_htaccess_prevent_hotlinking',
        __('Prevent Hotlinking', 'nerd-delay'),
        'nerd_delay_field_htaccess_prevent_hotlinking_render',
        'nerdDelay',
        'nerd_delay_htaccess_section'
    );

    add_settings_field(
        'nerd_delay_field_htaccess_block_bad_bots',
        __('Block Bad Bots', 'nerd-delay'),
        'nerd_delay_field_htaccess_block_bad_bots_render',
        'nerdDelay',
        'nerd_delay_htaccess_section'
    );
    // Add new settings fields
    add_settings_field(
        'nerd_delay_field_disable_xml_rpc',
        __('Disable XML-RPC', 'nerd-delay'),
        'nerd_delay_field_disable_xml_rpc_render',
        'nerdDelay',
        'nerd_delay_section'
    );

    add_settings_field(
        'nerd_delay_field_disable_rest_api',
        __('Disable REST API for Non-Authenticated Users', 'nerd-delay'),
        'nerd_delay_field_disable_rest_api_render',
        'nerdDelay',
        'nerd_delay_section'
    );

    add_settings_field(
        'nerd_delay_field_enable_csp',
        __('Enable Content Security Policy (CSP)', 'nerd-delay'),
        'nerd_delay_field_enable_csp_render',
        'nerdDelay',
        'nerd_delay_section'
    );

    add_settings_field(
        'nerd_delay_field_enable_referrer_policy',
        __('Enable Referrer Policy', 'nerd-delay'),
        'nerd_delay_field_enable_referrer_policy_render',
        'nerdDelay',
        'nerd_delay_section'
    );

    add_settings_field(
        'nerd_delay_field_enable_dns_prefetching',
        __('Enable DNS Prefetching', 'nerd-delay'),
        'nerd_delay_field_enable_dns_prefetching_render',
        'nerdDelay',
        'nerd_delay_section'
    );

    add_settings_field(
        'nerd_delay_field_disable_heartbeat_api',
        __('Disable WordPress Heartbeat API', 'nerd-delay'),
        'nerd_delay_field_disable_heartbeat_api_render',
        'nerdDelay',
        'nerd_delay_section'
    );

    add_settings_field(
        'nerd_delay_field_disable_emojis',
        __('Disable WordPress Emojis', 'nerd-delay'),
        'nerd_delay_field_disable_emojis_render',
        'nerdDelay',
        'nerd_delay_section'
    );

    add_settings_field(
        'nerd_delay_field_enable_http2_push',
        __('Enable HTTP/2 Push', 'nerd-delay'),
        'nerd_delay_field_enable_http2_push_render',
        'nerdDelay',
        'nerd_delay_section'
    );

    add_settings_field(
        'nerd_delay_field_enable_db_optimization',
        __('Enable Database Optimization', 'nerd-delay'),
        'nerd_delay_field_enable_db_optimization_render',
        'nerdDelay',
        'nerd_delay_section'
    );

    add_settings_field(
        'nerd_delay_field_browser_cache_duration',
        __('Browser Cache Duration (in days)', 'nerd-delay'),
        'nerd_delay_field_browser_cache_duration_render',
        'nerdDelay',
        'nerd_delay_section'
    );
}

function nerd_delay_render_toggle_switch($name, $checked) {
    ?>
    <label class="nerd-delay-switch">
        <input type="checkbox" name="<?php echo esc_attr($name); ?>" <?php checked($checked); ?> value="1">
        <span class="nerd-delay-slider"></span>
    </label>
    <?php
}

function nerd_delay_field_defer_render() {
    $options = get_option('nerd_delay_settings');
    $is_activated = isset($options['defer']);
    nerd_delay_render_toggle_switch('nerd_delay_settings[defer]', $is_activated);
    if ($is_activated) {
        echo '<span class="activated-message">Active</span>';
    }
}

function nerd_delay_field_async_render() {
    $options = get_option('nerd_delay_settings');
    $is_activated = isset($options['async']);
    nerd_delay_render_toggle_switch('nerd_delay_settings[async]', $is_activated);
    if ($is_activated) {
        echo '<span class="activated-message">Active</span>';
    }
}

function nerd_delay_field_preload_css_render() {
    $options = get_option('nerd_delay_settings');
    $is_activated = isset($options['preload_css']);
    nerd_delay_render_toggle_switch('nerd_delay_settings[preload_css]', $is_activated);
    if ($is_activated) {
        echo '<span class="activated-message">Active</span>';
    }
}

function nerd_delay_field_inline_css_render() {
    $options = get_option('nerd_delay_settings');
    ?>
    <textarea name="nerd_delay_settings[inline_css]" rows="5" cols="50"><?php echo esc_textarea($options['inline_css'] ?? ''); ?></textarea>
    <p class="description">Enter critical CSS to inline.</p>
    <?php
    if (!empty($options['inline_css'])) {
        echo '<span class="activated-message">Active</span>';
    }
}

function nerd_delay_field_defer_css_render() {
    $options = get_option('nerd_delay_settings');
    $is_activated = isset($options['defer_css']);
    nerd_delay_render_toggle_switch('nerd_delay_settings[defer_css]', $is_activated);
    if ($is_activated) {
        echo '<span class="activated-message">Active</span>';
    }
}

function nerd_delay_field_lazy_load_images_render() {
    $options = get_option('nerd_delay_settings');
    $is_activated = isset($options['lazy_load_images']);
    nerd_delay_render_toggle_switch('nerd_delay_settings[lazy_load_images]', $is_activated);
    ?>
    <p class="description">Enable lazy loading for images to improve LCP.</p>
    <?php
    if ($is_activated) {
        echo '<span class="activated-message">Active</span>';
    }
}

function nerd_delay_field_font_display_render() {
    $options = get_option('nerd_delay_settings');
    $is_activated = isset($options['font_display']);
    nerd_delay_render_toggle_switch('nerd_delay_settings[font_display]', $is_activated);
    ?>
    <p class="description">Use font-display: swap to improve CLS.</p>
    <?php
    if ($is_activated) {
        echo '<span class="activated-message">Active</span>';
    }
}
// First, let's create a new function to render the htaccess buttons
function nerd_delay_render_htaccess_button($option_name, $rule, $description) {
    $lines = nerd_delay_get_rule_lines($rule);
    $is_active = !empty($lines);
    
    echo '<div class="nerd-delay-htaccess-option">';
    
    if ($is_active) {
        echo '<button type="button" class="button button-disabled" disabled>' . __('Already Added', 'nerd-delay') . '</button>';
        echo '<span class="activated-message">Active on lines: ' . implode(', ', $lines) . '</span>';
    } else {
        echo '<button type="button" class="button button-primary add-htaccess-rule" data-option="' . esc_attr($option_name) . '">' . 
             __('Add to .htaccess', 'nerd-delay') . '</button>';
    }
    
    echo '<p class="description">' . $description . ' <em>' . __('Modifies .htaccess file.', 'nerd-delay') . '</em></p>';
    echo '</div>';
}

// Now, let's update each htaccess render function to use this new approach
function nerd_delay_field_htaccess_gzip_render() {
    $rules = "
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css application/javascript application/json
</IfModule>";
    
    nerd_delay_render_htaccess_button(
        'htaccess_gzip',
        $rules,
        __('Enable Gzip compression for faster page loads.', 'nerd-delay')
    );
}

function nerd_delay_field_htaccess_browser_caching_render() {
    $rules = "
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType text/css \"access plus 1 month\"
    ExpiresByType application/javascript \"access plus 1 month\"
    ExpiresByType image/jpeg \"access plus 1 year\"
</IfModule>";
    
    nerd_delay_render_htaccess_button(
        'htaccess_browser_caching',
        $rules,
        __('Enable browser caching to improve load times.', 'nerd-delay')
    );
}

function nerd_delay_field_htaccess_hsts_render() {
    $rules = "Header always set Strict-Transport-Security \"max-age=31536000; includeSubDomains\"";
    
    nerd_delay_render_htaccess_button(
        'htaccess_hsts',
        $rules,
        __('Enable HTTP Strict Transport Security (HSTS) for secure connections.', 'nerd-delay')
    );
}

function nerd_delay_field_htaccess_clickjacking_render() {
    $rules = "Header always append X-Frame-Options SAMEORIGIN";
    
    nerd_delay_render_htaccess_button(
        'htaccess_clickjacking',
        $rules,
        __('Prevent clickjacking attacks by setting X-Frame-Options headers.', 'nerd-delay')
    );
}

function nerd_delay_field_htaccess_mime_sniffing_render() {
    $rules = "Header set X-Content-Type-Options nosniff";
    
    nerd_delay_render_htaccess_button(
        'htaccess_mime_sniffing',
        $rules,
        __('Prevent MIME sniffing by setting X-Content-Type-Options headers.', 'nerd-delay')
    );
}

function nerd_delay_field_htaccess_xss_protection_render() {
    $rules = "Header set X-XSS-Protection \"1; mode=block\"";
    
    nerd_delay_render_htaccess_button(
        'htaccess_xss_protection',
        $rules,
        __('Enable XSS protection by setting X-XSS-Protection headers.', 'nerd-delay')
    );
}

function nerd_delay_field_htaccess_disable_directory_browsing_render() {
    $rules = "Options -Indexes";
    
    nerd_delay_render_htaccess_button(
        'htaccess_disable_directory_browsing',
        $rules,
        __('Disable directory browsing for security.', 'nerd-delay')
    );
}

function nerd_delay_field_htaccess_block_sensitive_files_render() {
    $rules = "
<FilesMatch \"\\.(htaccess|htpasswd|ini|log|sh|bak|sql|swp|dist)$\">
    Order allow,deny
    Deny from all
</FilesMatch>";
    
    nerd_delay_render_htaccess_button(
        'htaccess_block_sensitive_files',
        $rules,
        __('Block access to sensitive files like .htaccess, .ini, etc.', 'nerd-delay')
    );
}

function nerd_delay_field_htaccess_redirect_http_to_https_render() {
    $rules = "
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]";
    
    nerd_delay_render_htaccess_button(
        'htaccess_redirect_http_to_https',
        $rules,
        __('Redirect all HTTP traffic to HTTPS.', 'nerd-delay')
    );
}

function nerd_delay_field_htaccess_prevent_hotlinking_render() {
    $site_domain = parse_url(site_url(), PHP_URL_HOST);
    $rules = "
RewriteCond %{HTTP_REFERER} !^$
RewriteCond %{HTTP_REFERER} !^https?://(www\\.)?" . preg_quote($site_domain, '/') . " [NC]
RewriteRule \\.(jpg|jpeg|png|gif|svg|webp)$ - [F,NC]";
    
    nerd_delay_render_htaccess_button(
        'htaccess_prevent_hotlinking',
        $rules,
        __('Prevent hotlinking of images and other resources.', 'nerd-delay')
    );
}

function nerd_delay_field_htaccess_block_bad_bots_render() {
    $rules = "
RewriteCond %{HTTP_USER_AGENT} (?:bot|spider|crawler|wget|curl|scraper) [NC]
RewriteRule .* - [F,L]";
    
    nerd_delay_render_htaccess_button(
        'htaccess_block_bad_bots',
        $rules,
        __('Block bad bots from accessing your site.', 'nerd-delay')
    );
}

// Now, let's add AJAX handling for adding rules to .htaccess
add_action('admin_enqueue_scripts', 'nerd_delay_enqueue_htaccess_scripts');
function nerd_delay_enqueue_htaccess_scripts($hook) {
    if ($hook === 'settings_page_nerd-delay' || $hook === 'tools_page_nerd-delay-tools') {
        wp_enqueue_script('nerd-delay-htaccess', plugin_dir_url(__FILE__) . 'assets/js/htaccess.js', array('jquery'), NERD_DELAY_VERSION, true);
        wp_localize_script('nerd-delay-htaccess', 'nerdDelayHtaccess', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('nerd_delay_htaccess_nonce'),
            'success' => __('Rule added to .htaccess successfully!', 'nerd-delay'),
            'error' => __('Error adding rule to .htaccess. Please check file permissions.', 'nerd-delay')
        ));
    }
}function nerd_delay_field_disable_xml_rpc_render() {
    $options = get_option('nerd_delay_settings');
    nerd_delay_render_toggle_switch('nerd_delay_settings[disable_xml_rpc]', isset($options['disable_xml_rpc']));
    ?>
    <p class="description">Disable XML-RPC to improve security.</p>
    <?php
}

function nerd_delay_field_disable_rest_api_render() {
    $options = get_option('nerd_delay_settings');
    nerd_delay_render_toggle_switch('nerd_delay_settings[disable_rest_api]', isset($options['disable_rest_api']));
    ?>
    <p class="description">Disable REST API for non-authenticated users.</p>
    <?php
}

function nerd_delay_field_enable_csp_render() {
    $options = get_option('nerd_delay_settings');
    nerd_delay_render_toggle_switch('nerd_delay_settings[enable_csp]', isset($options['enable_csp']));
    ?>
    <p class="description">Enable Content Security Policy (CSP) to mitigate XSS and data injection attacks.</p>
    <?php
}

function nerd_delay_field_enable_referrer_policy_render() {
    $options = get_option('nerd_delay_settings');
    nerd_delay_render_toggle_switch('nerd_delay_settings[enable_referrer_policy]', isset($options['enable_referrer_policy']));
    ?>
    <p class="description">Enable Referrer Policy to control referrer information sent with requests.</p>
    <?php
}

function nerd_delay_field_enable_dns_prefetching_render() {
    $options = get_option('nerd_delay_settings');
    nerd_delay_render_toggle_switch('nerd_delay_settings[enable_dns_prefetching]', isset($options['enable_dns_prefetching']));
    ?>
    <p class="description">Enable DNS prefetching to improve performance for external resources.</p>
    <?php
}

function nerd_delay_field_disable_heartbeat_api_render() {
    $options = get_option('nerd_delay_settings');
    nerd_delay_render_toggle_switch('nerd_delay_settings[disable_heartbeat_api]', isset($options['disable_heartbeat_api']));
    ?>
    <p class="description">Disable WordPress Heartbeat API to reduce server load.</p>
    <?php
}

function nerd_delay_field_disable_emojis_render() {
    $options = get_option('nerd_delay_settings');
    nerd_delay_render_toggle_switch('nerd_delay_settings[disable_emojis]', isset($options['disable_emojis']));
    ?>
    <p class="description">Disable WordPress emojis for performance optimization.</p>
    <?php
}

function nerd_delay_field_enable_http2_push_render() {
    $options = get_option('nerd_delay_settings');
    nerd_delay_render_toggle_switch('nerd_delay_settings[enable_http2_push]', isset($options['enable_http2_push']));
    ?>
    <p class="description">Enable HTTP/2 server push for critical resources.</p>
    <?php
}

function nerd_delay_field_enable_db_optimization_render() {
    $options = get_option('nerd_delay_settings');
    nerd_delay_render_toggle_switch('nerd_delay_settings[enable_db_optimization]', isset($options['enable_db_optimization']));
    ?>
    <p class="description">Enable automatic database optimization and cleanup.</p>
    <?php
}

function nerd_delay_field_browser_cache_duration_render() {
    $options = get_option('nerd_delay_settings');
    ?>
    <input type="number" name="nerd_delay_settings[browser_cache_duration]" value="<?php echo esc_attr($options['browser_cache_duration'] ?? 30); ?>" min="1">
    <p class="description">Set the browser cache duration in days.</p>
    <?php
}

function nerd_delay_field_script_conditions_render() {
    $options = get_option('nerd_delay_settings');
    ?>
    <textarea name="nerd_delay_settings[script_conditions]" rows="5" cols="50"><?php echo esc_textarea($options['script_conditions'] ?? ''); ?></textarea>
    <p class="description">Specify conditions for script loading (e.g., only on specific pages).</p>
    <?php
}

function nerd_delay_settings_section_callback() {
    echo __('Configure the script, CSS, and Core Web Vitals optimization settings.', 'nerd-delay');
}

function nerd_delay_options_page() {
    ?>
    <form action='options.php' method='post'>
        <h2><?php _e('Nerd Delay', 'nerd-delay'); ?> <small><?php echo __('Version', 'nerd-delay') . ' ' . NERD_DELAY_VERSION; ?></small></h2>
        <?php
        settings_fields('nerdDelay');
        do_settings_sections('nerdDelay');
        submit_button();
        ?>
    </form>
    <div class="nerd-delay-info">
        <h3><?php _e('Core Web Vitals Optimization', 'nerd-delay'); ?></h3>
        <p><?php _e('Largest Contentful Paint (LCP): Preload key resources and lazy load images to improve LCP.', 'nerd-delay'); ?></p>
        <p><?php _e('First Input Delay (FID): Defer non-critical JavaScript to improve FID.', 'nerd-delay'); ?></p>
        <p><?php _e('Cumulative Layout Shift (CLS): Use font-display: swap and set size attributes for images to reduce CLS.', 'nerd-delay'); ?></p>
    </div>
    <button id="nerd-delay-scan"><?php _e('Scan for Scripts and CSS', 'nerd-delay'); ?></button>
    <div id="nerd-delay-scripts"></div>
    <div id="nerd-delay-active-scripts">
        <h3><?php _e('Active Scripts and CSS', 'nerd-delay'); ?></h3>
        <div id="active-scripts-table">
            <?php nerd_delay_display_active_scripts(); ?>
        </div>
    </div>
    <script type="text/javascript">
        document.getElementById('nerd-delay-scan').addEventListener('click', function() {
            var data = {
                'action': 'nerd_delay_scan_assets',
                'security': '<?php echo wp_create_nonce("nerd_delay_nonce"); ?>'
            };
            jQuery.post(ajaxurl, data, function(response) {
                document.getElementById('nerd-delay-scripts').innerHTML = response;
            });
        });

        document.addEventListener('click', function(event) {
            if (event.target && event.target.id === 'save-scripts') {
                var formData = jQuery('#nerd-delay-scripts-form').serialize();
                jQuery.post(ajaxurl, {
                    'action': 'nerd_delay_save_assets',
                    'security': '<?php echo wp_create_nonce("nerd_delay_nonce"); ?>',
                    'data': formData
                }, function(response) {
                    alert('Assets saved successfully!');
                    updateActiveScriptsTable(response.data.defer, response.data.async, response.data.preloadCss);
                });
            }
        });
    </script>
    <?php
}

// AJAX handler for scanning scripts and CSS
add_action('wp_ajax_nerd_delay_scan_assets', 'nerd_delay_scan_assets');
function nerd_delay_scan_assets() {
    check_ajax_referer('nerd_delay_nonce', 'security');

    $url = home_url();
    $response = wp_remote_get($url);
    if (is_wp_error($response)) {
        echo 'Error fetching site content.';
        wp_die();
    }

    $body = wp_remote_retrieve_body($response);
    preg_match_all('/<script.*?src=["\'](.*?)["\'].*?>/i', $body, $script_matches);
    preg_match_all('/<link.*?rel=["\']stylesheet["\'].*?href=["\'](.*?)["\'].*?>/i', $body, $css_matches);

    echo '<form id="nerd-delay-scripts-form">';
    echo '<table>';
    echo '<thead><tr><th>Asset</th><th>Load Option</th><th>Suggested</th></thead>';
    echo '<tbody>';

    if (!empty($script_matches[1])) {
        foreach ($script_matches[1] as $script) {
            $suggested = nerd_delay_suggest_option($script);
            echo '<tr>';
            echo '<td>' . esc_html($script) . '</td>';
            echo '<td>';
            echo '<select name="load_option[' . esc_attr($script) . ']">';
            echo '<option value="off">Off</option>';
            echo '<option value="defer"' . selected($suggested, 'defer', false) . '>Defer</option>';
            echo '<option value="async"' . selected($suggested, 'async', false) . '>Async</option>';
            echo '</select>';
            echo '</td>';
            echo '<td>' . esc_html(ucfirst($suggested)) . '</td>';
            echo '</tr>';
        }
    }

    if (!empty($css_matches[1])) {
        foreach ($css_matches[1] as $css) {
            echo '<tr>';
            echo '<td>' . esc_html($css) . '</td>';
            echo '<td>';
            echo '<select name="css_option[' . esc_attr($css) . ']">';
            echo '<option value="off">Off</option>';
            echo '<option value="preload">Preload</option>';
            echo '</select>';
            echo '</td>';
            echo '<td>Preload</td>';
            echo '</tr>';
        }
    }

    echo '</tbody>';
    echo '</table>';
    echo '<button type="button" id="save-scripts">Save</button>';
    echo '</form>';

    wp_die();
}

// Suggest an option for each script
function nerd_delay_suggest_option($script) {
    // Simple heuristic for suggestion
    if (strpos($script, 'analytics') !== false || strpos($script, 'track') !== false) {
        return 'async';
    }
    return 'defer';
}

// AJAX handler for saving scripts and CSS
add_action('wp_ajax_nerd_delay_save_assets', 'nerd_delay_save_assets');
function nerd_delay_save_assets() {
    check_ajax_referer('nerd_delay_nonce', 'security');

    parse_str($_POST['data'], $data);
    $defer_scripts = [];
    $async_scripts = [];
    $preload_css = [];

    if (isset($data['load_option'])) {
        foreach ($data['load_option'] as $script => $option) {
            $script = sanitize_text_field($script);
            $option = sanitize_text_field($option);
            if ($option === 'defer') {
                $defer_scripts[] = $script;
            } elseif ($option === 'async') {
                $async_scripts[] = $script;
            }
        }
    }

    if (isset($data['css_option'])) {
        foreach ($data['css_option'] as $css => $option) {
            $css = sanitize_text_field($css);
            $option = sanitize_text_field($option);
            if ($option === 'preload') {
                $preload_css[] = $css;
            }
        }
    }

    update_option('nerd_delay_defer_scripts', $defer_scripts);
    update_option('nerd_delay_async_scripts', $async_scripts);
    update_option('nerd_delay_preload_css', $preload_css);

    wp_send_json_success(['defer' => $defer_scripts, 'async' => $async_scripts, 'preloadCss' => $preload_css]);
}

// Display active scripts and CSS
function nerd_delay_display_active_scripts() {
    $defer_scripts = get_option('nerd_delay_defer_scripts', []);
    $async_scripts = get_option('nerd_delay_async_scripts', []);
    $preload_css = get_option('nerd_delay_preload_css', []);

    if (empty($defer_scripts) && empty($async_scripts) && empty($preload_css)) {
        echo 'No active scripts or CSS.';
        return;
    }

    echo '<table>';
    echo '<thead><tr><th>Asset</th><th>Status</th></thead>';
    echo '<tbody>';
    foreach ($defer_scripts as $script) {
        echo '<tr>';
        echo '<td>' . esc_html($script) . '</td>';
        echo '<td><select data-script="' . esc_attr($script) . '"><option value="off">Off</option><option value="defer" selected>Defer</option><option value="async">Async</option></select></td>';
        echo '</tr>';
    }
    foreach ($async_scripts as $script) {
        echo '<tr>';
        echo '<td>' . esc_html($script) . '</td>';
        echo '<td><select data-script="' . esc_attr($script) . '"><option value="off">Off</option><option value="defer">Defer</option><option value="async" selected>Async</option></select></td>';
        echo '</tr>';
    }
    foreach ($preload_css as $css) {
        echo '<tr>';
        echo '<td>' . esc_html($css) . '</td>';
        echo '<td><select data-css="' . esc_attr($css) . '"><option value="off">Off</option><option value="preload" selected>Preload</option></select></td>';
        echo '</tr>';
    }
    echo '</tbody>';
    echo '</table>';
}

// Implement CSS optimizations based on settings
add_action('wp_head', 'nerd_delay_apply_css_optimizations');
function nerd_delay_apply_css_optimizations() {
    $options = get_option('nerd_delay_settings');

    // Inline Critical CSS
    if (!empty($options['inline_css'])) {
        echo '<style>' . esc_html($options['inline_css']) . '</style>';
    }

    // Preload CSS
    $preload_css = get_option('nerd_delay_preload_css', []);
    foreach ($preload_css as $css) {
        echo '<link rel="preload" href="' . esc_url(nerd_delay_get_cdn_url($css)) . '" as="style" onload="this.rel=\'stylesheet\'">';
    }

    // Defer Non-Critical CSS
    if (!empty($options['defer_css'])) {
        ?>
        <script type="text/javascript">
            document.addEventListener("DOMContentLoaded", function() {
                var link = document.createElement('link');
                link.rel = 'stylesheet';
                link.href = '<?php echo esc_url(nerd_delay_get_cdn_url('path/to/your/non-critical-stylesheet.css')); ?>';
                document.head.appendChild(link);
            });
        </script>
        <?php
    }

    // Lazy Load Images
    if (!empty($options['lazy_load_images'])) {
        add_filter('wp_get_attachment_image_attributes', function($attr) {
            $attr['loading'] = 'lazy';
            return $attr;
        });
    }

    // Font Display Swap
    if (!empty($options['font_display'])) {
        add_filter('style_loader_tag', function($html, $handle, $href, $media) {
            if (strpos($html, 'fonts.googleapis.com') !== false) {
                $html = str_replace("rel='stylesheet'", "rel='stylesheet' onload=\"this.media='all'\"", $html);
                $html .= "<noscript><link rel='stylesheet' href='$href'></noscript>";
            }
            return $html;
        }, 10, 4);
    }
}

// Apply .htaccess optimizations based on settings
add_action('init', 'nerd_delay_apply_htaccess_optimizations');
function nerd_delay_apply_htaccess_optimizations() {
    $options = get_option('nerd_delay_settings');

    if (!empty($options['htaccess_gzip'])) {
        nerd_delay_enable_gzip();
    }

    if (!empty($options['htaccess_browser_caching'])) {
        nerd_delay_enable_browser_caching();
    }

    if (!empty($options['htaccess_hsts'])) {
        nerd_delay_enable_hsts();
    }

    if (!empty($options['htaccess_clickjacking'])) {
        nerd_delay_prevent_clickjacking();
    }

    if (!empty($options['htaccess_mime_sniffing'])) {
        nerd_delay_prevent_mime_sniffing();
    }

    if (!empty($options['htaccess_xss_protection'])) {
        nerd_delay_enable_xss_protection();
    }

    if (!empty($options['htaccess_disable_directory_browsing'])) {
        nerd_delay_disable_directory_browsing();
    }

    if (!empty($options['htaccess_block_sensitive_files'])) {
        nerd_delay_block_sensitive_files();
    }

    if (!empty($options['htaccess_redirect_http_to_https'])) {
        nerd_delay_redirect_http_to_https();
    }

    if (!empty($options['htaccess_prevent_hotlinking'])) {
        nerd_delay_prevent_hotlinking();
    }

    if (!empty($options['htaccess_block_bad_bots'])) {
        nerd_delay_block_bad_bots();
    }
}

// Enable Gzip Compression
function nerd_delay_enable_gzip() {
    // Add Gzip compression rules to .htaccess
    $rules = "
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css application/javascript application/json
</IfModule>";
    nerd_delay_update_htaccess($rules);
}

// Enable Browser Caching
function nerd_delay_enable_browser_caching() {
    // Add browser caching rules to .htaccess
    $rules = "
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType text/css \"access plus 1 month\"
    ExpiresByType application/javascript \"access plus 1 month\"
    ExpiresByType image/jpeg \"access plus 1 year\"
</IfModule>";
    nerd_delay_update_htaccess($rules);
}

// Enable HTTP Strict Transport Security (HSTS)
function nerd_delay_enable_hsts() {
    // Add HSTS rules to .htaccess
    $rules = "Header always set Strict-Transport-Security \"max-age=31536000; includeSubDomains\"";
    nerd_delay_update_htaccess($rules);
}

// Prevent Clickjacking
function nerd_delay_prevent_clickjacking() {
    // Add X-Frame-Options header to .htaccess
    $rules = "Header always append X-Frame-Options SAMEORIGIN";
    nerd_delay_update_htaccess($rules);
}

// Prevent MIME Sniffing
function nerd_delay_prevent_mime_sniffing() {
    // Add X-Content-Type-Options header to .htaccess
    $rules = "Header set X-Content-Type-Options nosniff";
    nerd_delay_update_htaccess($rules);
}

// Enable XSS Protection
function nerd_delay_enable_xss_protection() {
    // Add X-XSS-Protection header to .htaccess
    $rules = "Header set X-XSS-Protection \"1; mode=block\"";
    nerd_delay_update_htaccess($rules);
}

// Disable Directory Browsing
function nerd_delay_disable_directory_browsing() {
    // Add rules to disable directory browsing in .htaccess
    $rules = "Options -Indexes";
    nerd_delay_update_htaccess($rules);
}

// Block Access to Sensitive Files
function nerd_delay_block_sensitive_files() {
    // Add rules to block access to sensitive files in .htaccess
    $rules = "
<FilesMatch \"\\.(htaccess|htpasswd|ini|log|sh|bak|sql|swp|dist)$\">
    Order allow,deny
    Deny from all
</FilesMatch>";
    nerd_delay_update_htaccess($rules);
}

// Redirect HTTP to HTTPS
function nerd_delay_redirect_http_to_https() {
    // Add rules to redirect HTTP to HTTPS in .htaccess
    $rules = "
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]";
    nerd_delay_update_htaccess($rules);
}

// Prevent Hotlinking
function nerd_delay_prevent_hotlinking() {
    // Add rules to prevent hotlinking in .htaccess
    $rules = "
RewriteCond %{HTTP_REFERER} !^$
RewriteCond %{HTTP_REFERER} !^https?://(www\\.)?yourdomain\\.com [NC]
RewriteRule \\.(jpg|jpeg|png|gif|svg|webp)$ - [F,NC]";
    nerd_delay_update_htaccess($rules);
}

// Block Bad Bots
function nerd_delay_block_bad_bots() {
    // Add rules to block bad bots in .htaccess
    $rules = "
RewriteCond %{HTTP_USER_AGENT} (?:bot|spider|crawler|wget|curl|scraper) [NC]
RewriteRule .* - [F,L]";
    nerd_delay_update_htaccess($rules);
}

// Helper function to update .htaccess
function nerd_delay_update_htaccess($rules) {
    $htaccess_file = ABSPATH . '.htaccess';
    if (is_writable($htaccess_file)) {
        $result = file_put_contents($htaccess_file, $rules . PHP_EOL, FILE_APPEND);
        if ($result === false) {
            error_log('Failed to update .htaccess file.');
        }
    } else {
        error_log('.htaccess file is not writable.');
    }
}

// Function to get CDN URL if WP Rocket is using a CDN
function nerd_delay_get_cdn_url($url) {
    $wp_rocket_settings = get_option('wp_rocket_settings');
    if (!empty($wp_rocket_settings['cdn']) && !empty($wp_rocket_settings['cdn_cnames'])) {
        $cdn_url = $wp_rocket_settings['cdn_cnames'][0]; // Assuming the first CDN URL is used
        $site_url = site_url();
        if (strpos($url, $site_url) === 0) {
            $url = str_replace($site_url, $cdn_url, $url);
        }
    }
    return $url;
}

function nerd_delay_log($message) {
    if (WP_DEBUG) {
        error_log('[Nerd Delay] ' . $message);
    }
}

function nerd_delay_admin_notice($message, $type = 'success') {
    echo '<div class="notice notice-' . esc_attr($type) . ' is-dismissible"><p>' . esc_html($message) . '</p></div>';
}

function nerd_delay_export_settings() {
    $options = get_option('nerd_delay_settings');
    header('Content-Type: application/json');
    header('Content-Disposition: attachment; filename="nerd-delay-settings.json"');
    echo json_encode($options);
    exit;
}

function nerd_delay_get_rule_lines($rule) {
    $htaccess_file = ABSPATH . '.htaccess';
    $lines = [];
    if (file_exists($htaccess_file)) {
        $contents = file($htaccess_file, FILE_IGNORE_NEW_LINES);
        $rule_lines = explode("\n", trim($rule));

        foreach ($contents as $line_number => $line_content) {
            if (strpos($line_content, trim($rule_lines[0])) !== false) {
                $match = true;
                foreach ($rule_lines as $offset => $rule_line) {
                    if (!isset($contents[$line_number + $offset]) || trim($contents[$line_number + $offset]) !== trim($rule_line)) {
                        $match = false;
                        break;
                    }
                }
                if ($match) {
                    $lines = range($line_number + 1, $line_number + count($rule_lines));
                    break;
                }
            }
        }
    }
    return $lines;
}

// Add this function to handle removing rules when options are disabled
function nerd_delay_maybe_remove_htaccess_rules($old_value, $value) {
    $htaccess_options = [
        'htaccess_gzip', 'htaccess_browser_caching', 'htaccess_hsts',
        'htaccess_clickjacking', 'htaccess_mime_sniffing', 'htaccess_xss_protection',
        'htaccess_disable_directory_browsing', 'htaccess_block_sensitive_files',
        'htaccess_redirect_http_to_https', 'htaccess_prevent_hotlinking',
        'htaccess_block_bad_bots'
    ];
    
    foreach ($htaccess_options as $option) {
        if (isset($old_value[$option]) && !isset($value[$option])) {
            // Option was disabled, remove the corresponding rules
            nerd_delay_remove_htaccess_rule($option);
        }
    }
}
add_action('update_option_nerd_delay_settings', 'nerd_delay_maybe_remove_htaccess_rules', 10, 2);

function nerd_delay_remove_htaccess_rule($option) {
    // Get the rule pattern based on the option
    $rule_pattern = '';
    switch ($option) {
        case 'htaccess_gzip':
            $rule_pattern = "<IfModule mod_deflate.c>";
            break;
        case 'htaccess_browser_caching':
            $rule_pattern = "<IfModule mod_expires.c>";
            break;
        case 'htaccess_hsts':
            $rule_pattern = "# HSTS (HTTP Strict Transport Security)";
            break;
        case 'htaccess_clickjacking':
            $rule_pattern = "# Protect against clickjacking";
            break;
        case 'htaccess_mime_sniffing':
            $rule_pattern = "# Disable MIME-type sniffing";
            break;
        case 'htaccess_xss_protection':
            $rule_pattern = "# Enable XSS protection";
            break;
        case 'htaccess_disable_directory_browsing':
            $rule_pattern = "# Disable directory browsing";
            break;
        case 'htaccess_block_sensitive_files':
            $rule_pattern = "# Block access to sensitive files";
            break;
        case 'htaccess_redirect_http_to_https':
            $rule_pattern = "# Redirect HTTP to HTTPS";
            break;
        case 'htaccess_prevent_hotlinking':
            $rule_pattern = "# Prevent image hotlinking";
            break;
        case 'htaccess_block_bad_bots':
            $rule_pattern = "# Block bad bots";
            break;
    }
    
    if (empty($rule_pattern)) {
        return;
    }
    
    $htaccess_file = ABSPATH . '.htaccess';
    if (file_exists($htaccess_file) && is_writable($htaccess_file)) {
        $htaccess_content = file_get_contents($htaccess_file);
        
        // Find and remove the rule block
        $pattern = '/\s*' . preg_quote($rule_pattern, '/') . '.*?<\/IfModule>\s*/s';
        $new_content = preg_replace($pattern, "\n", $htaccess_content);
        
        if ($new_content !== $htaccess_content) {
            file_put_contents($htaccess_file, $new_content);
            nerd_delay_log("Removed {$option} rules from .htaccess");
        }
    }
}