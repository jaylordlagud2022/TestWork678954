# WordPress Test Project from AbeloHost: Cities, Countries, and Temperature Widget

This is a WordPress project that displays a table of countries, cities, and their current temperatures using custom post types and taxonomies. It fetches the temperature data from the `api.open-meteo.com` API instead of OpenWeatherMap.

## Prerequisites

Before setting up the project, ensure you have the following:

- A WordPress installation set up.
- Access to the WordPress theme directory for your child theme.

## Setup Instructions

### 1. Clone the Repository or Copy the Files

- Clone or copy the project files into your WordPress theme directory
- 
### 2. Install WordPress

If you haven't already installed WordPress, you can follow the official instructions to do so: https://wordpress.org/support/article/how-to-install-wordpress/

### 3. Enable the Child Theme

- In your WordPress admin dashboard, go to **Appearance > Themes** and activate the storefront-child theme.

### 4. Custom Post Type & Taxonomy

The theme includes a custom post type for "Cities" and a custom taxonomy for "Countries". These are registered using the following functions:

- `create_cities_post_type()`
- `create_countries_taxonomy()`

This allows you to add cities as posts and categorize them under different countries.

### 5. Add City Meta Data

Each city post will have latitude and longitude metadata. You can add the coordinates through the post editor, using the **City Coordinates** meta box. The latitude and longitude values are used to fetch the current temperature from the API.

### 6. Temperature Widget

A custom widget `City_Temperature_Widget` is included to display the temperature based on the city's coordinates. This widget can be added to any widget area within your WordPress site.

### 7. AJAX Search for Cities

A live search feature allows users to search for cities by name. As users type a city name, the table of cities will update dynamically using AJAX.

### 8. API Integration

This project uses the `api.open-meteo.com` API to fetch current weather data, specifically the temperature, for the cities based on their latitude and longitude. This API is used instead of the OpenWeatherMap API.

Here’s how we fetch the temperature:

```php
$response = wp_remote_get("https://api.open-meteo.com/v1/forecast?latitude=$latitude&longitude=$longitude&current_weather=true");
