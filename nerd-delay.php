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