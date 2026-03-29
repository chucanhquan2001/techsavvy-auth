<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Laravel\Passport\ClientRepository;
use Symfony\Component\Console\Command\Command;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command(
    'oauth:client:create
    {name : Human readable client name}
    {--grant=authorization_code : Grant type: authorization_code, password, client_credentials}
    {--redirect=* : Redirect URI for authorization_code clients}
    {--provider=users : Auth provider}
    {--public : Create a public client without a secret}',
    function (ClientRepository $clients): int {
        $grant = (string) $this->option('grant');
        $name = (string) $this->argument('name');
        $provider = (string) $this->option('provider');
        $redirects = array_values(array_filter((array) $this->option('redirect')));
        $public = (bool) $this->option('public');

        if ($grant === 'authorization_code' && $redirects === []) {
            $this->error('Authorization code clients require at least one --redirect URI.');

            return Command::FAILURE;
        }

        if ($grant === 'password' && $public) {
            $this->error('Password grant clients must be confidential and require a client secret.');

            return Command::FAILURE;
        }

        $client = match ($grant) {
            'password' => $clients->createPasswordGrantClient($name, $provider, ! $public),
            'client_credentials' => $clients->createClientCredentialsGrantClient($name),
            'authorization_code' => $clients->createAuthorizationCodeGrantClient(
                $name,
                $redirects,
                ! $public
            ),
            default => null,
        };

        if (! $client) {
            $this->error('Unsupported grant type.');

            return Command::FAILURE;
        }

        $this->info('OAuth client created successfully.');
        $this->line('Client ID: '.$client->getKey());
        $this->line('Client Secret: '.($client->plainSecret ?? '(public client)'));
        $this->line('Grant Types: '.implode(', ', $client->grant_types));
        $this->line('Redirect URIs: '.implode(', ', $client->redirect_uris));

        return Command::SUCCESS;
    }
)->purpose('Create an OAuth client for a consuming application.');
