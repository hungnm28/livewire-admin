import {defineConfig} from 'vite';
import laravel, {refreshPaths} from 'laravel-vite-plugin';
import collectModuleAssetsPaths from './vite-module-loader.js';

const paths = [
    'resources/css/app.css',
    'resources/js/app.js',
];
const allPaths = await collectModuleAssetsPaths(paths, 'Modules');

export default defineConfig({
    plugins: [
        laravel({
            input: allPaths,
            refresh: [
                ...refreshPaths,
                'app/Livewire/**',
                'Modules/*/**',
                'Modules/**',
                'Modules/***',
                'Modules/****',
            ],
        }),
    ],
});
