const colors = require('tailwindcss/colors');

module.exports = {
    content: [
        '../../vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        '../../vendor/laravel/jetstream/**/*.blade.php',
        '../../vendor/hungnm28/laravel-form/resources/views/components/*.blade.php',
        '../../vendor/hungnm28/laravel-form/resources/views/components/**/*.blade.php',
        '../../storage/framework/views/*.php',
        './Resources/views/livewire/**/*.blade.php',
        './Resources/views/**/*.blade.php',
        './Resources/views/*.blade.php',
        './Resources/views/layouts/*.blade.php',
    ],

    theme: {

    },

    variants: {
        extend: {
            opacity: ['disabled'],
            width: ['hover', 'focus'],

        }
    },

    plugins: [require('@tailwindcss/forms'), require('@tailwindcss/typography')],
};
