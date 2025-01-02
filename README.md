# WordPress Test Project from AbeloHost: Cities, Countries, and Temperature Widget

This is a WordPress project that displays a table of countries, cities, and their current temperatures using custom post types and taxonomies. It fetches the temperature data from the `api.open-meteo.com` API instead of OpenWeatherMap.

## Prerequisites

Before setting up the project, ensure you have the following:

- A WordPress installation set up.
- Access to the WordPress theme directory for your child theme.

## Setup Instructions

### 1. Clone the Repository or Copy the Files

- Clone or copy the project files into your WordPress theme directory, preferably inside a child theme.
- Ensure that the theme you are using is based on the Storefront theme (or any other theme you prefer) for compatibility.

### 2. Install WordPress

If you haven't already installed WordPress, you can follow the official instructions to do so: https://wordpress.org/support/article/how-to-install-wordpress/

### 3. Set Up the Child Theme

- In the `wp-content/themes/` directory, create a new folder for your child theme, e.g., `storefront-child`.
- Copy the `style.css` and `functions.php` files from this project into the `storefront-child` directory.

### 4. Enable the Child Theme

- In your WordPress admin dashboard, go to **Appearance > Themes** and activate your child theme.

### 5. Custom Post Type & Taxonomy

The theme includes a custom post type for "Cities" and a custom taxonomy for "Countries". These are registered using the following functions:

- `create_cities_post_type()`
- `create_countries_taxonomy()`

This allows you to add cities as posts and categorize them under different countries.

### 6. Add City Meta Data

Each city post will have latitude and longitude metadata. You can add the coordinates through the post editor, using the **City Coordinates** meta box. The latitude and longitude values are used to fetch the current temperature from the API.

### 7. Temperature Widget

A custom widget `City_Temperature_Widget` is included to display the temperature based on the city's coordinates. This widget can be added to any widget area within your WordPress site.

### 8. AJAX Search for Cities

A live search feature allows users to search for cities by name. As users type a city name, the table of cities will update dynamically using AJAX.

### 9. API Integration

This project uses the `api.open-meteo.com` API to fetch current weather data, specifically the temperature, for the cities based on their latitude and longitude. This API is used instead of the previously integrated OpenWeatherMap API.

Here’s how we fetch the temperature:

```php
$response = wp_remote_get("https://api.open-meteo.com/v1/forecast?latitude=$latitude&longitude=$longitude&current_weather=true");
