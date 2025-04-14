<?php
/*
Plugin Name: Nerd Delay
Plugin URI: https://narcolepticnerd.com
Description: Optimize WordPress sites by deferring or asynchronously loading scripts, optimizing CSS, and improving Core Web Vitals.
Version: 1.6
Author: NarcolepticNerd
Author URI: https://narcolepticnerd.com
License: GPL2
*/

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
        'nerd_delay_section'
    );

    add_settings_field(
        'nerd_delay_field_htaccess_browser_caching',
        __('Enable Browser Caching', 'nerd-delay'),
        'nerd_delay_field_htaccess_browser_caching_render',
        'nerdDelay',
        'nerd_delay_section'
    );

    add_settings_field(
        'nerd_delay_field_htaccess_hsts',
        __('Enable HTTP Strict Transport Security (HSTS)', 'nerd-delay'),
        'nerd_delay_field_htaccess_hsts_render',
        'nerdDelay',
        'nerd_delay_section'
    );

    add_settings_field(
        'nerd_delay_field_htaccess_clickjacking',
        __('Prevent Clickjacking', 'nerd-delay'),
        'nerd_delay_field_htaccess_clickjacking_render',
        'nerdDelay',
        'nerd_delay_section'
    );

    add_settings_field(
        'nerd_delay_field_htaccess_mime_sniffing',
        __('Prevent MIME Sniffing', 'nerd-delay'),
        'nerd_delay_field_htaccess_mime_sniffing_render',
        'nerdDelay',
        'nerd_delay_section'
    );

    add_settings_field(
        'nerd_delay_field_htaccess_xss_protection',
        __('Enable XSS Protection', 'nerd-delay'),
        'nerd_delay_field_htaccess_xss_protection_render',
        'nerdDelay',
        'nerd_delay_section'
    );

    add_settings_field(
        'nerd_delay_field_htaccess_disable_directory_browsing',
        __('Disable Directory Browsing', 'nerd-delay'),
        'nerd_delay_field_htaccess_disable_directory_browsing_render',
        'nerdDelay',
        'nerd_delay_section'
    );

    add_settings_field(
        'nerd_delay_field_htaccess_block_sensitive_files',
        __('Block Sensitive Files', 'nerd-delay'),
        'nerd_delay_field_htaccess_block_sensitive_files_render',
        'nerdDelay',
        'nerd_delay_section'
    );

    add_settings_field(
        'nerd_delay_field_htaccess_redirect_http_to_https',
        __('Redirect HTTP to HTTPS', 'nerd-delay'),
        'nerd_delay_field_htaccess_redirect_http_to_https_render',
        'nerdDelay',
        'nerd_delay_section'
    );

    add_settings_field(
        'nerd_delay_field_htaccess_prevent_hotlinking',
        __('Prevent Hotlinking', 'nerd-delay'),
        'nerd_delay_field_htaccess_prevent_hotlinking_render',
        'nerdDelay',
        'nerd_delay_section'
    );

    add_settings_field(
        'nerd_delay_field_htaccess_block_bad_bots',
        __('Block Bad Bots', 'nerd-delay'),
        'nerd_delay_field_htaccess_block_bad_bots_render',
        'nerdDelay',
        'nerd_delay_section'
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
}

function nerd_delay_field_defer_render() {
    $options = get_option('nerd_delay_settings');
    ?>
    <input type='checkbox' name='nerd_delay_settings[defer]' <?php checked(isset($options['defer'])); ?> value='1'>
    <?php
}

function nerd_delay_field_async_render() {
    $options = get_option('nerd_delay_settings');
    ?>
    <input type='checkbox' name='nerd_delay_settings[async]' <?php checked(isset($options['async'])); ?> value='1'>
    <?php
}

function nerd_delay_field_preload_css_render() {
    $options = get_option('nerd_delay_settings');
    ?>
    <input type='checkbox' name='nerd_delay_settings[preload_css]' <?php checked(isset($options['preload_css'])); ?> value='1'>
    <?php
}

function nerd_delay_field_inline_css_render() {
    $options = get_option('nerd_delay_settings');
    ?>
    <textarea name='nerd_delay_settings[inline_css]' rows='5' cols='50'><?php echo esc_textarea($options['inline_css'] ?? ''); ?></textarea>
    <p class="description">Enter critical CSS to inline.</p>
    <?php
}

function nerd_delay_field_defer_css_render() {
    $options = get_option('nerd_delay_settings');
    ?>
    <input type='checkbox' name='nerd_delay_settings[defer_css]' <?php checked(isset($options['defer_css'])); ?> value='1'>
    <?php
}

function nerd_delay_field_lazy_load_images_render() {
    $options = get_option('nerd_delay_settings');
    ?>
    <input type='checkbox' name='nerd_delay_settings[lazy_load_images]' <?php checked(isset($options['lazy_load_images'])); ?> value='1'>
    <p class="description">Enable lazy loading for images to improve LCP.</p>
    <?php
}

function nerd_delay_field_font_display_render() {
    $options = get_option('nerd_delay_settings');
    ?>
    <input type='checkbox' name='nerd_delay_settings[font_display]' <?php checked(isset($options['font_display'])); ?> value='1'>
    <p class="description">Use font-display: swap to improve CLS.</p>
    <?php
}

// Render functions for new settings fields
function nerd_delay_field_htaccess_gzip_render() {
    $options = get_option('nerd_delay_settings');
    ?>
    <input type='checkbox' name='nerd_delay_settings[htaccess_gzip]' <?php checked(isset($options['htaccess_gzip'])); ?> value='1'>
    <p class="description">Enable Gzip compression for faster page loads.</p>
    <?php
}

function nerd_delay_field_htaccess_browser_caching_render() {
    $options = get_option('nerd_delay_settings');
    ?>
    <input type='checkbox' name='nerd_delay_settings[htaccess_browser_caching]' <?php checked(isset($options['htaccess_browser_caching'])); ?> value='1'>
    <p class="description">Enable browser caching to improve load times.</p>
    <?php
}

function nerd_delay_field_htaccess_hsts_render() {
    $options = get_option('nerd_delay_settings');
    ?>
    <input type='checkbox' name='nerd_delay_settings[htaccess_hsts]' <?php checked(isset($options['htaccess_hsts'])); ?> value='1'>
    <p class="description">Enable HTTP Strict Transport Security (HSTS) for secure connections.</p>
    <?php
}

function nerd_delay_field_htaccess_clickjacking_render() {
    $options = get_option('nerd_delay_settings');
    ?>
    <input type='checkbox' name='nerd_delay_settings[htaccess_clickjacking]' <?php checked(isset($options['htaccess_clickjacking'])); ?> value='1'>
    <p class="description">Prevent clickjacking attacks by setting X-Frame-Options headers.</p>
    <?php
}

function nerd_delay_field_htaccess_mime_sniffing_render() {
    $options = get_option('nerd_delay_settings');
    ?>
    <input type='checkbox' name='nerd_delay_settings[htaccess_mime_sniffing]' <?php checked(isset($options['htaccess_mime_sniffing'])); ?> value='1'>
    <p class="description">Prevent MIME sniffing by setting X-Content-Type-Options headers.</p>
    <?php
}

function nerd_delay_field_htaccess_xss_protection_render() {
    $options = get_option('nerd_delay_settings');
    ?>
    <input type='checkbox' name='nerd_delay_settings[htaccess_xss_protection]' <?php checked(isset($options['htaccess_xss_protection'])); ?> value='1'>
    <p class="description">Enable XSS protection by setting X-XSS-Protection headers.</p>
    <?php
}

function nerd_delay_field_htaccess_disable_directory_browsing_render() {
    $options = get_option('nerd_delay_settings');
    ?>
    <input type='checkbox' name='nerd_delay_settings[htaccess_disable_directory_browsing]' <?php checked(isset($options['htaccess_disable_directory_browsing'])); ?> value='1'>
    <p class="description">Disable directory browsing for security.</p>
    <?php
}

function nerd_delay_field_htaccess_block_sensitive_files_render() {
    $options = get_option('nerd_delay_settings');
    ?>
    <input type='checkbox' name='nerd_delay_settings[htaccess_block_sensitive_files]' <?php checked(isset($options['htaccess_block_sensitive_files'])); ?> value='1'>
    <p class="description">Block access to sensitive files like .htaccess, .ini, etc.</p>
    <?php
}

function nerd_delay_field_htaccess_redirect_http_to_https_render() {
    $options = get_option('nerd_delay_settings');
    ?>
    <input type='checkbox' name='nerd_delay_settings[htaccess_redirect_http_to_https]' <?php checked(isset($options['htaccess_redirect_http_to_https'])); ?> value='1'>
    <p class="description">Redirect all HTTP traffic to HTTPS.</p>
    <?php
}

function nerd_delay_field_htaccess_prevent_hotlinking_render() {
    $options = get_option('nerd_delay_settings');
    ?>
    <input type='checkbox' name='nerd_delay_settings[htaccess_prevent_hotlinking]' <?php checked(isset($options['htaccess_prevent_hotlinking'])); ?> value='1'>
    <p class="description">Prevent hotlinking of images and other resources.</p>
    <?php
}

function nerd_delay_field_htaccess_block_bad_bots_render() {
    $options = get_option('nerd_delay_settings');
    ?>
    <input type='checkbox' name='nerd_delay_settings[htaccess_block_bad_bots]' <?php checked(isset($options['htaccess_block_bad_bots'])); ?> value='1'>
    <p class="description">Block bad bots from accessing your site.</p>
    <?php
}

function nerd_delay_field_disable_xml_rpc_render() {
    $options = get_option('nerd_delay_settings');
    ?>
    <input type='checkbox' name='nerd_delay_settings[disable_xml_rpc]' <?php checked(isset($options['disable_xml_rpc'])); ?> value='1'>
    <p class="description">Disable XML-RPC to improve security.</p>
    <?php
}

function nerd_delay_field_disable_rest_api_render() {
    $options = get_option('nerd_delay_settings');
    ?>
    <input type='checkbox' name='nerd_delay_settings[disable_rest_api]' <?php checked(isset($options['disable_rest_api'])); ?> value='1'>
    <p class="description">Disable REST API for non-authenticated users.</p>
    <?php
}

function nerd_delay_field_enable_csp_render() {
    $options = get_option('nerd_delay_settings');
    ?>
    <input type='checkbox' name='nerd_delay_settings[enable_csp]' <?php checked(isset($options['enable_csp'])); ?> value='1'>
    <p class="description">Enable Content Security Policy (CSP) to mitigate XSS and data injection attacks.</p>
    <?php
}

function nerd_delay_field_enable_referrer_policy_render() {
    $options = get_option('nerd_delay_settings');
    ?>
    <input type='checkbox' name='nerd_delay_settings[enable_referrer_policy]' <?php checked(isset($options['enable_referrer_policy'])); ?> value='1'>
    <p class="description">Enable Referrer Policy to control referrer information sent with requests.</p>
    <?php
}

function nerd_delay_field_enable_dns_prefetching_render() {
    $options = get_option('nerd_delay_settings');
    ?>
    <input type='checkbox' name='nerd_delay_settings[enable_dns_prefetching]' <?php checked(isset($options['enable_dns_prefetching'])); ?> value='1'>
    <p class="description">Enable DNS prefetching to improve performance for external resources.</p>
    <?php
}

function nerd_delay_field_disable_heartbeat_api_render() {
    $options = get_option('nerd_delay_settings');
    ?>
    <input type='checkbox' name='nerd_delay_settings[disable_heartbeat_api]' <?php checked(isset($options['disable_heartbeat_api'])); ?> value='1'>
    <p class="description">Disable WordPress Heartbeat API to reduce server load.</p>
    <?php
}

function nerd_delay_field_disable_emojis_render() {
    $options = get_option('nerd_delay_settings');
    ?>
    <input type='checkbox' name='nerd_delay_settings[disable_emojis]' <?php checked(isset($options['disable_emojis'])); ?> value='1'>
    <p class="description">Disable WordPress emojis for performance optimization.</p>
    <?php
}

function nerd_delay_field_enable_http2_push_render() {
    $options = get_option('nerd_delay_settings');
    ?>
    <input type='checkbox' name='nerd_delay_settings[enable_http2_push]' <?php checked(isset($options['enable_http2_push'])); ?> value='1'>
    <p class="description">Enable HTTP/2 server push for critical resources.</p>
    <?php
}

function nerd_delay_field_enable_db_optimization_render() {
    $options = get_option('nerd_delay_settings');
    ?>
    <input type='checkbox' name='nerd_delay_settings[enable_db_optimization]' <?php checked(isset($options['enable_db_optimization'])); ?> value='1'>
    <p class="description">Enable automatic database optimization and cleanup.</p>
    <?php
}

function nerd_delay_settings_section_callback() {
    echo __('Configure the script, CSS, and Core Web Vitals optimization settings.', 'nerd-delay');
}

function nerd_delay_options_page() {
    ?>
    <form action='options.php' method='post'>
        <h2>Nerd Delay</h2>
        <?php
        settings_fields('nerdDelay');
        do_settings_sections('nerdDelay');
        submit_button();
        ?>
    </form>
    <div class="nerd-delay-info">
        <h3>Core Web Vitals Optimization</h3>
        <p><strong>Largest Contentful Paint (LCP):</strong> Preload key resources and lazy load images to improve LCP.</p>
        <p><strong>First Input Delay (FID):</strong> Defer non-critical JavaScript to improve FID.</p>
        <p><strong>Cumulative Layout Shift (CLS):</strong> Use font-display: swap and set size attributes for images to reduce CLS.</p>
    </div>
    <button id="nerd-delay-scan">Scan for Scripts and CSS</button>
    <div id="nerd-delay-scripts"></div>
    <div id="nerd-delay-active-scripts">
        <h3>Active Scripts and CSS</h3>
        <div id="active-scripts-table">
            <?php nerd_delay_display_active_scripts(); ?>
        </div>
    </div>
    <script type="text/javascript">
        document.getElementById('nerd-delay-scan').addEventListener('click', function() {
            var data = {
                'action': 'nerd_delay_scan_assets',
            };
            jQuery.post(ajaxurl, data, function(response) {
                document.getElementById('nerd-delay-scripts').innerHTML = response;
            });
        });

        function updateActiveScriptsTable(deferScripts, asyncScripts, preloadCss) {
            var tableHtml = '<table><thead><tr><th>Asset</th><th>Status</th></tr></thead><tbody>';
            deferScripts.forEach(function(script) {
                tableHtml += '<tr><td>' + script + '</td><td><select data-script="' + script + '"><option value="off">Off</option><option value="defer" selected>Defer</option><option value="async">Async</option></select></td></tr>';
            });
            asyncScripts.forEach(function(script) {
                tableHtml += '<tr><td>' + script + '</td><td><select data-script="' + script + '"><option value="off">Off</option><option value="defer">Defer</option><option value="async" selected>Async</option></select></td></tr>';
            });
            preloadCss.forEach(function(css) {
                tableHtml += '<tr><td>' + css + '</td><td><select data-css="' + css + '"><option value="off">Off</option><option value="preload" selected>Preload</option></select></td></tr>';
            });
            tableHtml += '</tbody></table>';
            document.getElementById('active-scripts-table').innerHTML = tableHtml;
        }

        document.addEventListener('click', function(event) {
            if (event.target && event.target.id === 'save-scripts') {
                var formData = jQuery('#nerd-delay-scripts-form').serialize();
                jQuery.post(ajaxurl, {
                    'action': 'nerd_delay_save_assets',
                    'data': formData
                }, function(response) {
                    alert('Assets saved successfully!');
                    updateActiveScriptsTable(response.defer, response.async, response.preloadCss);
                });
            }
        });
    </script>
    <?php
}

// AJAX handler for scanning scripts and CSS
add_action('wp_ajax_nerd_delay_scan_assets', 'nerd_delay_scan_assets');
function nerd_delay_scan_assets() {
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
    echo '<thead><tr><th>Asset</th><th>Load Option</th><th>Suggested</th></tr></thead>';
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
    parse_str($_POST['data'], $data);
    $defer_scripts = [];
    $async_scripts = [];
    $preload_css = [];

    if (isset($data['load_option'])) {
        foreach ($data['load_option'] as $script => $option) {
            if ($option === 'defer') {
                $defer_scripts[] = $script;
            } elseif ($option === 'async') {
                $async_scripts[] = $script;
            }
        }
    }

    if (isset($data['css_option'])) {
        foreach ($data['css_option'] as $css => $option) {
            if ($option === 'preload') {
                $preload_css[] = $css;
            }
        }
    }

    update_option('nerd_delay_defer_scripts', $defer_scripts);
    update_option('nerd_delay_async_scripts', $async_scripts);
    update_option('nerd_delay_preload_css', $preload_css);

    // Return the updated lists for dynamic update
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
    echo '<thead><tr><th>Asset</th><th>Status</th></tr></thead>';
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
    // ...existing code for enabling Gzip...
}

// Enable Browser Caching
function nerd_delay_enable_browser_caching() {
    // Add browser caching rules to .htaccess
    // ...existing code for enabling browser caching...
}

// Enable HTTP Strict Transport Security (HSTS)
function nerd_delay_enable_hsts() {
    // Add HSTS rules to .htaccess
    // ...existing code for enabling HSTS...
}

// Prevent Clickjacking
function nerd_delay_prevent_clickjacking() {
    // Add X-Frame-Options header to .htaccess
    // ...existing code for preventing clickjacking...
}

// Prevent MIME Sniffing
function nerd_delay_prevent_mime_sniffing() {
    // Add X-Content-Type-Options header to .htaccess
    // ...existing code for preventing MIME sniffing...
}

// Enable XSS Protection
function nerd_delay_enable_xss_protection() {
    // Add X-XSS-Protection header to .htaccess
    // ...existing code for enabling XSS protection...
}

// Disable Directory Browsing
function nerd_delay_disable_directory_browsing() {
    // Add rules to disable directory browsing in .htaccess
    // Options -Indexes
}

// Block Access to Sensitive Files
function nerd_delay_block_sensitive_files() {
    // Add rules to block access to sensitive files in .htaccess
    // <FilesMatch "\.(htaccess|htpasswd|ini|log|sh|bak|sql|swp|dist)$">
    // Order allow,deny
    // Deny from all
    // </FilesMatch>
}

// Redirect HTTP to HTTPS
function nerd_delay_redirect_http_to_https() {
    // Add rules to redirect HTTP to HTTPS in .htaccess
    // RewriteCond %{HTTPS} off
    // RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
}

// Prevent Hotlinking
function nerd_delay_prevent_hotlinking() {
    // Add rules to prevent hotlinking in .htaccess
    // RewriteCond %{HTTP_REFERER} !^$
    // RewriteCond %{HTTP_REFERER} !^https?://(www\.)?yourdomain\.com [NC]
    // RewriteRule \.(jpg|jpeg|png|gif|svg|webp)$ - [F,NC]
}

// Block Bad Bots
function nerd_delay_block_bad_bots() {
    // Add rules to block bad bots in .htaccess
    // RewriteCond %{HTTP_USER_AGENT} (?:bot|spider|crawler|wget|curl|scraper) [NC]
    // RewriteRule .* - [F,L]
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