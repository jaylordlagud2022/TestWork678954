<?php
/**
 * Template Name: Countries, Cities, and Temperatures Table with Search
 * Template Post Type: page
 */
get_header();

// Custom Action Hook Before Table
do_action('before_cities_table');

?>

<div class="content-wrapper">
    <div class="main-content">
        <div class="cities-table">
            <h2>Countries, Cities, and Current Temperature</h2>

            <!-- Search Form for Cities -->
            <form id="city-search-form" method="get">
                <label for="city-search">Search City:</label>
                <input type="text" id="city-search" name="city-search" placeholder="Enter city name" />
                <button type="submit">Search</button>
            </form>

            <table id="cities-table">
                <thead>
                    <tr>
                        <th>Country</th>
                        <th>City</th>
                        <th>Temperature (°C)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    global $wpdb;

                    // Get the cities with associated countries using a custom query with $wpdb
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
                        ORDER BY p.post_title
                    ";

                    $results = $wpdb->get_results($sql);

                    if ($results) :
                        foreach ($results as $city) :
                            // Fetch temperature
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
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="sidebar">
        <?php get_sidebar(); ?>
    </div>
</div>

<?php
// Custom Action Hook After Table
do_action('after_cities_table');

get_footer();
