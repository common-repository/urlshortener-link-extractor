<?php

/*
Plugin Name: Urlshortener Link Extractor
Plugin URI: http://www.urlshortener.co
Description: This plugin extract with the help of API all the outbound links of your Blog or Website to your Urlshortener.co Account and short these automatically with Anti-Adblock Solution System to earn money.
Tags: link extractor, shortener, urlshortener, short links, outbound links, adblock, anti adblock plugin, anti-adblock, advertising, ads, cpa, traffic
Version: 1.0
Author URI: http://www.urlshortener.co
*/

function ShortenURL_activate()
{
    // Activation code here...
}

register_activation_hook(__FILE__, 'ShortenURL_activate');

function ShortenURL_deactivate()
{
    // Deactivation code here...
}

register_deactivation_hook(__FILE__, 'ShortenURL_deactivate');

function ShortenURL_scripts()
{
   // wp_enqueue_style('default', '/wp-content/plugins/imageZoom/css/default.css');
}

add_action('wp_enqueue_scripts', 'ShortenURL_scripts');

add_action('admin_menu', 'ShortenURL_admin_menu');

add_action('admin_init', 'register_shorten_url_settings');

function ShortenURL_admin_menu()
{
    add_options_page('Link Extractor', 'Link Extractor', 'manage_options', 'shortenurl_settings', 'shortenurl_admin');
}

function register_shorten_url_settings()
{
    register_setting('shorten-url-option-group', 'shorten_url_api_key');
    register_setting('shorten-url-option-group', 'shorten_url_access_token');
    register_setting('shorten-url-option-group', 'allow_shorten_url');
}

function shortenurl_admin()
{
    echo '<div class="wrap">' .
        '<h4>Urlshortener Link Extractor Settings</h4>' .
        '<form method="post" action="options.php">';
    settings_fields('shorten-url-option-group');
    do_settings_sections('shorten-url-option-group');
    echo '<table class="form-table">' .
        '<tbody>' .
        '<tr>' .
        '<th scope="row"><label for="shorten_url_api_key">Urlshortener.co API Key:</label></th>' .
        '<td>' .
        '<input name="shorten_url_api_key" type="text" id="shorten_url_api_key" value="' . esc_attr(get_option('shorten_url_api_key')) . '" />' .
        '</td>' .
        '</tr>' .
        '<tr>' .
        '<th scope="row"><label for="shorten_url_access_token">Urlshortener.co Access Token:</label></th>' .
        '<td>' .
        '<input name="shorten_url_access_token" type="text" id="shorten_url_access_token" value="' . esc_attr(get_option('shorten_url_access_token')) . '" />' .
        '</td>' .
        '</tr>' .
        '<tr>';
    $value = esc_attr(get_option('allow_shorten_url'));
    if ($value == 1) {
        $checked = 'checked value="1"';
    } else {
        $checked = 'value="1"';
    }
    echo '<th scope="row"><label for="allow_shorten_url">Shorten all outbound links</label></th>' .
        '<td><input name="allow_shorten_url" type="checkbox" id="allow_shorten_url" ' . $checked . '>' .
        '</td>' .
        '</tr>' .
        '</tbody>' .
        '</table><p><i>You API Key and Token can you find in your <a href="http://www.urlshortener.co" title="CPA Link Shortener" target=_blank>Urlshortener.co</a> Account! <br />If you don&#x92;t have an account, you can create one, it&#x92;s free!</i></p>';
    submit_button();
    echo '</form > ' .
        '</div > ';
}

add_action('save_post', 'get_content_to_shorten_url', 10, 3);

function get_content_to_shorten_url()
{
    $check = get_option('allow_shorten_url');
    $key = get_option('shorten_url_api_key');
    $token = get_option('shorten_url_access_token');
    if (isset($check) && !empty($check) && isset($key) && !empty($key) && isset($token) && !empty($token) && $check == 1) {
        $the_query = new WP_Query(array('post_type' => 'post'));
        while ($the_query->have_posts()):
            $the_query->the_post();
            $_post_id = get_the_id();
//Write Site URL below.
//Don't write http:// or anything like that. just domain.com or domain.net
            $_site_url = site_url();

//Getting Post content
            $_post_content = get_post_field('post_content', $_post_id);

            $site_parts = explode('.', $_site_url);
            $site_suffix = '.' . $site_parts[1];
//Using regular expression to match hyperlink
            preg_match_all('|<a.*(?=href=\"([^\"]*)\")[^>]*>([^<]*)</a>|i', $_post_content, $match);
            foreach ($match[0] as $link) {
                //Filtering out internal links
                $parts = explode($site_suffix, $link);
                $domain = explode('//', $parts[0]);
                if ($domain[1] != 'www.' . $site_parts[0] && $domain[1] != $site_parts[0]) {
                    $shortener = 'http://access.urlshortener.co/short/?token=' . $token . '&key=' . $key . '&absolute_url=' . $match[1][0];
                    $response = file_get_contents($shortener);
                    if (isset($response) && !empty($response)) {

                    }
                }
            }

        endwhile;
        wp_reset_postdata();
    }
}

?>
