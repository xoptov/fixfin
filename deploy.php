<?php

require('vendor/deployer/deployer/recipe/symfony.php');

server('production', 'fixfin.ru')
    ->user('xoptov')
    ->identityFile()
    ->stage('prod')
    ->env('deploy_path', '/var/www/prod');

server('staging', 'fixfin.ru')
    ->user('xoptov')
    ->identityFile()
    ->stage('stage')
    ->env('deploy_path', '/var/www/stage');

set('repository', 'git@bitbucket.org:xoptov/rublic.git');
set('shared_dirs', ['app/logs', 'web/uploads', 'web/media']);
set('writable_dirs', ['app/logs', 'app/cache', 'web/uploads', 'web/media']);
set('assets', ['web/bundles']);

task('deploy', [
    'deploy:prepare',
    'deploy:release',
    'deploy:update_code',
    'deploy:create_cache_dir',
    'deploy:shared',
    'deploy:writable',
    'deploy:assets',
    'deploy:vendors',
    'deploy:cache:warmup',
    'deploy:symlink',
    'cleanup',
])->desc('Deploy your project');