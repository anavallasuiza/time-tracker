<?php

require 'recipe/composer.php';

serverList('servers.yml');

set('shared_files', [
    '.env',
]);

set('shared_dirs', [
    'storage/app',
    'storage/framework/cache',
    'storage/framework/sessions',
    'storage/framework/views',
    'storage/logs',
    'storage/gettext',
    'public/storage',
]);

set('writable_dirs', [
    'public/storage',
    'bootstrap/cache',
    'storage'
]);

task('deploy:upload', function () {
    $release = env('release_path');
    $content = [
        'app',
        'bootstrap',
        'cache.json',
        'composer.json',
        'composer.lock',
        'config',
        'database',
        'public',
        'artisan',
        'storage'
    ];
    foreach ($content as $path) {
        upload($path, $release.'/'.$path);
    }
    foreach (['lang', 'views'] as $path) {
        upload('resources/'.$path, $release.'/resources/'.$path);
    }
});

task('deploy:publish', function () {
    $path = env('release_path');
    run("php {$path}/artisan vendor:publish");
    run("php {$path}/artisan migrate");
});

task('deploy', [
    'deploy:prepare',
    'deploy:release',
    'deploy:upload',
    'deploy:vendors',
    'deploy:shared',
    'deploy:writable',
    'deploy:symlink',
    'deploy:publish',
    'cleanup',
]);
