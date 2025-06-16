> This project got me the top grade on my developer exam :D

# RY - Weather app made with Laravel and OpenWeatherMap API
Ry is a weather application built with Laravel that utilizes the OpenWeatherMap API to provide real-time weather data. It features a user-friendly interface, allowing users to search for current weather conditions in various locations.


## Features
- Search for current weather by city name
- Display current temperature, humidity, wind speed, and weather conditions
- Responsive design for mobile and desktop devices
- Error handling for invalid city names

## Installation
1. Clone the repository:
2. Install dependencies using Composer and NPM:
   ```bash
   composer install && npm install
   ```
3. Set up your `.env` file:
    ```bash
    cp .env.example .env
    ```
4. Setup your OpenWeatherMap API key in the `.env` file:
    ```env
    OPENWEATHER_API_KEY=your_api_key_here
    ```
   
5. Generate the application key:
    ```bash
    php artisan key:generate
    ```
6. Run the migrations:
    ```bash
    php artisan migrate
    ```
7. Build the frontend assets:
    ```bash
    npm run build:ssr
    ```
8. Start SSR server:
    ```bash
   node ./bootstrap/ssr.js/ssr.js
    ```
9. Start the Laravel development server:
    ```bash
    composer run dev
    ```


## Contributing
Contributions are welcome! Please submit a pull request or open an issue for any bugs or feature requests.

## License
This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Acknowledgements
- [Laravel](https://laravel.com) - The PHP framework used for this application.
- [OpenWeatherMap API](https://openweathermap.org/api) - The API used to fetch weather data.
- [Inertia.js](https://inertiajs.com) - For building single-page applications with Laravel.
- [Tailwind CSS](https://tailwindcss.com) - For styling the application.
- [Vue.js](https://vuejs.org) - For building the frontend components.
- [Shadcn-vue](https://shadcn-vue.com) - For UI components.

   

