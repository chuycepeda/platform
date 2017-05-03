<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

/**
 * API version number
 */
$apiVersion = '3';
$apiBase = 'api/v' . $apiVersion;

$app->get('/', "API\IndexController@index");
$app->get($apiBase . '[/]', "API\IndexController@index");
$app->group(['prefix' => $apiBase, 'namespace' => 'API'], function () use ($app) {

    // Collections
    $app->group([
            'middleware' => ['auth:api', 'scope:collections,sets'],
            'namespace' => 'Collections'
    ], function () use ($app) {
        $app->get('/collections[/]', 'CollectionsController@index');
        $app->post('/collections[/]', 'CollectionsController@store');
        $app->group(['prefix' => 'collections/'], function () use ($app) {
            $app->get('/{id}[/]', 'CollectionsController@show');
            $app->put('/{id}[/]', 'CollectionsController@update');
            $app->delete('/{id}[/]', 'CollectionsController@destroy');

            $app->get('/{set_id}/posts[/]', 'PostsController@index');
            $app->post('/{set_id}/posts[/]', 'PostsController@store');
            $app->group(['prefix' => '/{set_id:[0-9]+}/posts/'], function () use ($app) {
                $app->get('/{id}[/]', 'PostsController@show');
                //$app->put('/{id}[/]', 'PostsController@update');
                $app->delete('/{id}[/]', 'PostsController@destroy');
            });
        });
    });

    // Config
    // Define /config outside the group otherwise prefix breaks optional trailing slash
    $app->get('/config[/]', ['uses' => 'ConfigController@index']);
    // $app->post('/config[/]', ['middleware' => 'oauth:config', 'uses' => 'ConfigController@store']);
    $app->group(['prefix' => 'config/'], function () use ($app) {
        $app->get('/{id}[/]', ['middleware' => ['auth:api', 'scope:config'], 'uses' => 'ConfigController@show']);
        $app->put('/{id}[/]', ['middleware' => ['auth:api', 'scope:config'], 'uses' => 'ConfigController@update']);
        // $app->delete('/{id}[/]', ['middleware' => 'oauth:config', 'uses' => 'ConfigController@destroy']);
    });

    // Contacts
    $app->group(['middleware' => ['auth:api', 'scope:contacts']], function () use ($app) {
        $app->get('/contacts[/]', 'ContactsController@index');
        $app->post('/contacts[/]', 'ContactsController@store');
        $app->group(['prefix' => 'contacts/'], function () use ($app) {
            $app->get('/{id}[/]', 'ContactsController@show');
            $app->put('/{id}[/]', 'ContactsController@update');
            $app->delete('/{id}[/]', 'ContactsController@destroy');
        });
    });

    // CSV + Import
    $app->group(['middleware' => ['auth:api', 'scope:csv'], 'namespace' => 'CSV'], function () use ($app) {
        $app->get('/csv[/]', 'CSVController@index');
        $app->post('/csv[/]', 'CSVController@store');
        $app->group(['prefix' => 'csv/'], function () use ($app) {
            $app->get('/{id}[/]', 'CSVController@show');
            $app->put('/{id}[/]', 'CSVController@update');
            $app->delete('/{id}[/]', 'CSVController@destroy');

            $app->post('/{id}/import[/]', 'CSVImportController@store');
        });
    });


    // Data providers
    $app->group(['middleware' => ['auth:api', 'scope:dataproviders']], function () use ($app) {
        $app->get('/dataproviders[/]', 'DataProvidersController@index');
        $app->get('/dataproviders/{id}[/]', 'DataProvidersController@show');
    });

    // Forms
    $app->group(['middleware' => ['auth:api', 'scope:forms'], 'namespace' => 'Forms'], function () use ($app) {
        $app->get('/forms[/]', 'FormsController@index');
        $app->post('/forms[/]', 'FormsController@store');
        $app->group(['prefix' => 'forms/'], function () use ($app) {
            $app->get('/{id:[0-9]+}[/]', 'FormsController@show');
            $app->put('/{id:[0-9]+}[/]', 'FormsController@update');
            $app->delete('/{id:[0-9]+}[/]', 'FormsController@destroy');

            $app->get('/attributes[/]', 'AttributesController@index');
            $app->get('/stages[/]', 'StagesController@index');

            // Sub-form routes
            $app->group(['prefix' => '/{form_id:[0-9]+}'], function () use ($app) {
                // Attributes
                $app->get('/attributes[/]', 'AttributesController@index');
                $app->post('/attributes[/]', 'AttributesController@store');
                $app->group(['prefix' => 'attributes/'], function () use ($app) {
                    $app->get('/{id}[/]', 'AttributesController@show');
                    $app->put('/{id}[/]', 'AttributesController@update');
                    $app->delete('/{id}[/]', 'AttributesController@destroy');
                });

                // Stages
                $app->get('/stages[/]', 'StagesController@index');
                $app->post('/stages[/]', 'StagesController@store');
                $app->group(['prefix' => 'stages/'], function () use ($app) {
                    $app->get('/{id}[/]', 'StagesController@show');
                    $app->put('/{id}[/]', 'StagesController@update');
                    $app->delete('/{id}[/]', 'StagesController@destroy');
                });

                // Roles
                $app->get('/roles[/]', 'RolesController@index');
                $app->put('/roles[/]', 'RolesController@replace');
            });
        });
    });

    // Layers
    $app->group(['middleware' => ['auth:api', 'scope:layers']], function () use ($app) {
        $app->get('/layers[/]', 'LayersController@index');
        $app->post('/layers[/]', 'LayersController@store');
        $app->group(['prefix' => 'layers/'], function () use ($app) {
            $app->get('/{id}[/]', 'LayersController@show');
            $app->put('/{id}[/]', 'LayersController@update');
            $app->delete('/{id}[/]', 'LayersController@destroy');
        });
    });

    // Media
    $app->group(['middleware' => ['auth:api', 'scope:media']], function () use ($app) {
        $app->get('/media[/]', 'MediaController@index');
        $app->post('/media[/]', 'MediaController@store');
        $app->group(['prefix' => 'media/'], function () use ($app) {
            $app->get('/{id}[/]', 'MediaController@show');
            $app->put('/{id}[/]', 'MediaController@update');
            $app->delete('/{id}[/]', 'MediaController@destroy');
        });
    });

    // Messages
    $app->group(['middleware' => ['auth:api', 'scope:messages']], function () use ($app) {
        $app->get('/messages[/]', 'MessagesController@index');
        $app->post('/messages[/]', 'MessagesController@store');
        $app->group(['prefix' => 'messages/'], function () use ($app) {
            $app->get('/{id}[/]', 'MessagesController@show');
            $app->get('/{id}/post[/]', 'MessagesController@showPost');
            $app->put('/{id}[/]', 'MessagesController@update');
            // $app->delete('/{id}[/]', 'MessagesController@destroy');
        });
    });

    // Migration
    $app->group(['middleware' => ['auth:api', 'scope:migrate']], function () use ($app) {
        $app->get('/migration[/]', 'MigrationController@index');
        $app->get('/migration/status[/]', 'MigrationController@status');
        $app->post('/migration/rollback[/]', 'MigrationController@rollback');
        $app->post('/migration/migrate[/]', 'MigrationController@migrate');
    });

    // Notifications
    $app->group(['middleware' => ['auth:api', 'scope:notifications']], function () use ($app) {
        $app->get('/notifications[/]', 'NotificationsController@index');
        $app->post('/notifications[/]', 'NotificationsController@store');
        $app->group(['prefix' => 'notifications/'], function () use ($app) {
            $app->get('/{id}[/]', 'NotificationsController@show');
            $app->put('/{id}[/]', 'NotificationsController@update');
            $app->delete('/{id}[/]', 'NotificationsController@destroy');
        });
    });

    // Password reset
    $app->post('/passwordreset[/]', 'PasswordResetController@store');
    $app->post('/passwordreset/confirm[/]', 'PasswordResetController@confirm');

    // Permissions
    $app->group(['middleware' => ['auth:api', 'scope:permissions']], function () use ($app) {
        $app->get('/permissions[/]', 'PermissionsController@index');
        $app->get('/permissions/{id}[/]', 'PermissionsController@show');
    });

    // Posts
    $app->group(['middleware' => ['auth:api', 'scope:posts'], 'namespace' => 'Posts'], function () use ($app) {
        $app->get('/posts[/]', 'PostsController@index');
        $app->post('/posts[/]', 'PostsController@store');
        $app->group(['prefix' => 'posts/'], function () use ($app) {
            $app->get('/{id:[0-9]+}[/]', 'PostsController@show');
            $app->put('/{id:[0-9]+}[/]', 'PostsController@update');
            $app->delete('/{id:[0-9]+}[/]', 'PostsController@destroy');
            // GeoJSON
            $app->get('/geojson[/]', 'GeoJSONController@index');
            $app->get('/geojson/{zoom}/{x}/{y}[/]', 'GeoJSONController@index');
            $app->get('/{id:[0-9]+}/geojson[/]', 'GeoJSONController@show');

            // Export
            $app->get('/export[/]', 'ExportController@index');

            // Export
            $app->get('/stats[/]', 'PostsController@stats');

            // Sub-form routes
            $app->group(['prefix' => '/{parent_id:[0-9]+}'], function () use ($app) {
                // Revisions
                $app->get('/revisions[/]', 'RevisionsController@index');
                $app->group(['prefix' => 'revisions/'], function () use ($app) {
                    $app->get('/{id}[/]', 'RevisionsController@show');
                });

                // Translations
                $app->get('/translations[/]', 'TranslationsController@index');
                $app->post('/translations[/]', 'TranslationsController@store');
                $app->group(['prefix' => 'translations/'], function () use ($app) {
                    $app->get('/{id:[0-9]+}[/]', 'TranslationsController@show');
                    $app->put('/{id:[0-9]+}[/]', 'TranslationsController@update');
                    $app->delete('/{id:[0-9]+}[/]', 'TranslationsController@destroy');
                    $app->get('/{locale:[A-Za-z_]+}[/]', 'TranslationsController@show');
                    $app->put('/{locale:[A-Za-z_]+}[/]', 'TranslationsController@update');
                    $app->delete('/{locale:[A-Za-z_]+}[/]', 'TranslationsController@destroy');
                });
            });

        });
    });

    // Roles
    $app->group(['middleware' => ['auth:api', 'scope:roles']], function () use ($app) {
        $app->get('/roles[/]', 'RolesController@index');
        $app->post('/roles[/]', 'RolesController@store');
        $app->group(['prefix' => 'roles/'], function () use ($app) {
            $app->get('/{id}[/]', 'RolesController@show');
            $app->put('/{id}[/]', 'RolesController@update');
            $app->delete('/{id}[/]', 'RolesController@destroy');
        });
    });

    // Register
    $app->post('/register[/]', 'RegisterController@store');

    // Saved Searches
    $app->group(['middleware' => ['auth:api', 'scope:savedsearches']], function () use ($app) {
        $app->get('/savedsearches[/]', 'SavedSearchesController@index');
        $app->post('/savedsearches[/]', 'SavedSearchesController@store');
        $app->group(['prefix' => 'savedsearches/'], function () use ($app) {
            $app->get('/{id}[/]', 'SavedSearchesController@show');
            $app->put('/{id}[/]', 'SavedSearchesController@update');
            $app->delete('/{id}[/]', 'SavedSearchesController@destroy');
        });
    });

    // Tags
    $app->group(['middleware' => ['auth:api', 'scope:tags']], function () use ($app) {
        $app->get('/tags[/]', 'TagsController@index');
        $app->post('/tags[/]', 'TagsController@store');
        $app->group(['prefix' => 'tags/'], function () use ($app) {
            $app->get('/{id}[/]', 'TagsController@show');
            $app->put('/{id}[/]', 'TagsController@update');
            $app->delete('/{id}[/]', 'TagsController@destroy');
        });
    });

    // Users
    $app->group(['middleware' => ['auth:api', 'scope:users']], function () use ($app) {
        $app->get('/users[/]', 'UsersController@index');
        $app->post('/users[/]', 'UsersController@store');
        $app->group(['prefix' => 'users/'], function () use ($app) {
            $app->get('/{id:[0-9]+}[/]', 'UsersController@show');
            $app->put('/{id:[0-9]+}[/]', 'UsersController@update');
            $app->delete('/{id:[0-9]+}[/]', 'UsersController@destroy');
            $app->get('/me[/]', 'UsersController@showMe');
            $app->put('/me[/]', 'UsersController@updateMe');
        });
    });

    // Web hooks
    $app->group(['middleware' => ['auth:api', 'scope:webhooks']], function () use ($app) {
        $app->get('/webhooks[/]', 'WebhooksController@index');
        $app->post('/webhooks[/]', 'WebhooksController@store');
        $app->group(['prefix' => 'webhooks/'], function () use ($app) {
            $app->get('/{id:[0-9]+}[/]', 'WebhooksController@show');
            $app->put('/{id:[0-9]+}[/]', 'WebhooksController@update');
            $app->delete('/{id:[0-9]+}[/]', 'WebhooksController@destroy');
        });
    });
});

// Migration
$app->get('/migrate[/]', 'MigrateController@migrate');
