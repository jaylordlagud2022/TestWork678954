<?php
require_once get_stylesheet_directory() . '/includes/class-city-temperature-widget.php';

// Enqueue City Search Script
function enqueue_city_search_script() {
    wp_enqueue_script('city-search', get_stylesheet_directory_uri() . '/js/city-search.js', ['jquery'], null, true);

    // Localize the script with AJAX URL
    wp_localize_script('city-search', 'ajaxurl', admin_url('admin-ajax.php'));
}
add_action('wp_enqueue_scripts', 'enqueue_city_search_script');

// Enqueue Child Theme Styles
function storefront_child_enqueue_styles() {
    wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');
    wp_enqueue_style('child-style', get_stylesheet_directory_uri() . '/style.css', ['parent-style']);
}
add_action('wp_enqueue_scripts', 'storefront_child_enqueue_styles');

// Register Custom Post Type: Cities
function create_cities_post_type() {
    register_post_type('cities', [
        'labels' => [
            'name' => __('Cities'),
            'singular_name' => __('City'),
        ],
        'public' => true,
        'supports' => ['title', 'editor', 'custom-fields'],
        'has_archive' => true,
    ]);
}
add_action('init', 'create_cities_post_type');

// Register Custom Taxonomy: Countries
function create_countries_taxonomy() {
    register_taxonomy('countries', 'cities', [
        'labels' => [
            'name' => __('Countries'),
            'singular_name' => __('Country'),
        ],
        'hierarchical' => true,
        'show_admin_column' => true,
    ]);
}
add_action('init', 'create_countries_taxonomy');

// Add Meta Box
function add_city_meta_box() {
    add_meta_box('city_meta', 'City Coordinates', 'city_meta_box_callback', 'cities', 'normal', 'high');
}
add_action('add_meta_boxes', 'add_city_meta_box');

// Callback for Meta Box
function city_meta_box_callback($post) {
    $latitude = get_post_meta($post->ID, 'latitude', true);
    $longitude = get_post_meta($post->ID, 'longitude', true);
    ?>
    <label for="latitude">Latitude:</label>
    <input type="text" name="latitude" value="<?php echo esc_attr($latitude); ?>" /><br>
    <label for="longitude">Longitude:</label>
    <input type="text" name="longitude" value="<?php echo esc_attr($longitude); ?>" />
    <?php
}

// Save Meta Box Data
function save_city_meta($post_id) {
    if (isset($_POST['latitude'])) {
        update_post_meta($post_id, 'latitude', sanitize_text_field($_POST['latitude']));
    }
    if (isset($_POST['longitude'])) {
        update_post_meta($post_id, 'longitude', sanitize_text_field($_POST['longitude']));
    }
}
add_action('save_post', 'save_city_meta');

// Register City Temperature Widget
function register_city_temperature_widget() {
    register_widget('City_Temperature_Widget');
}
add_action('widgets_init', 'register_city_temperature_widget');

// Handle City Search AJAX Request
function search_city_ajax_handler() {
    global $wpdb;

    // Get the search query from the request
    $search_query = sanitize_text_field($_GET['query']);

    // SQL query to search cities by title
    $sql = "
        SELECT p.ID, p.post_title, t.name AS country,
        pm_lat.meta_value AS latitude, pm_lon.meta_value AS longitude
        FROM {$wpdb->posts} p
        INNER JOIN {$wpdb->term_relationships} tr ON p.ID = tr.object_id
        INNER JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
        INNER JOIN {$wpdb->terms} t ON tt.term_id = t.term_id
        LEFT JOIN {$wpdb->postmeta} pm_lat ON p.ID = pm_lat.post_id AND pm_lat.meta_key = 'latitude'
        LEFT JOIN {$wpdb->postmeta} pm_lon ON p.ID = pm_lon.post_id AND pm_lon.meta_key = 'longitude'
        WHERE p.post_type = 'cities'
        AND tt.taxonomy = 'countries'
        AND p.post_title LIKE %s
    ";
    $like_query = '%' . $wpdb->esc_like($search_query) . '%';
    $results = $wpdb->get_results($wpdb->prepare($sql, $like_query));

    if ($results) :
        foreach ($results as $city) :
            $temperature = get_city_temperature($city->latitude, $city->longitude);
            ?>
            <tr class="city-row">
                <td><?php echo esc_html($city->country); ?></td>
                <td><?php echo esc_html($city->post_title); ?></td>
                <td><?php echo esc_html($temperature); ?>°C</td>
            </tr>
            <?php
        endforeach;
    else :
        echo '<tr><td colspan="3">No cities found.</td></tr>';
    endif;

    wp_die(); // Always call wp_die() after an AJAX request
}
add_action('wp_ajax_search_city', 'search_city_ajax_handler');
add_action('wp_ajax_nopriv_search_city', 'search_city_ajax_handler');

// Fetch City Temperature
function get_city_temperature($latitude, $longitude) {
    // API request to fetch weather data
    $response = wp_remote_get("https://api.open-meteo.com/v1/forecast?latitude=$latitude&longitude=$longitude&current_weather=true");

    // Check for successful response
    if (is_wp_error($response)) {
        return 'N/A'; // Return N/A if the API call fails
    }

    $data = json_decode(wp_remote_retrieve_body($response), true);

    // Return temperature if available
    if (isset($data['current_weather']['temperature'])) {
        return $data['current_weather']['temperature'];
    }

    return 'N/A'; // Return N/A if the temperature data is not available
}
