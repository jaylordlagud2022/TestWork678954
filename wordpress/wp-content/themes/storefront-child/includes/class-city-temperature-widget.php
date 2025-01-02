<?php

class City_Temperature_Widget extends WP_Widget {
    function __construct() {
        parent::__construct('city_temperature_widget', __('City Temperature Widget'), [
            'description' => __('Displays city name and current temperature.'),
        ]);
    }

    public function widget($args, $instance) {
        $city_id = $instance['city_id'];
        $city_name = get_the_title($city_id);
        $latitude = get_post_meta($city_id, 'latitude', true);
        $longitude = get_post_meta($city_id, 'longitude', true);

        // Fetch temperature from Open-Meteo API
        $response = wp_remote_get("https://api.open-meteo.com/v1/forecast?latitude=$latitude&longitude=$longitude&current_weather=true");
        $data = json_decode(wp_remote_retrieve_body($response), true);

        // Extract temperature data from the response
        $temperature = $data['current_weather']['temperature'] ?? 'N/A';

        // Display widget content
        echo $args['before_widget'];
        echo $args['before_title'] . $city_name . $args['after_title'];
        echo "<p>Temperature: ".$temperature."°C</p>";
        echo $args['after_widget'];
    }

    public function form($instance) {
        $city_id = $instance['city_id'] ?? '';
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('city_id'); ?>">City:</label>
            <input class="widefat" id="<?php echo $this->get_field_id('city_id'); ?>" name="<?php echo $this->get_field_name('city_id'); ?>" type="text" value="<?php echo esc_attr($city_id); ?>">
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = [];
        $instance['city_id'] = sanitize_text_field($new_instance['city_id']);
        return $instance;
    }
}

add_action('widgets_init', function() {
    register_widget('City_Temperature_Widget');
});
