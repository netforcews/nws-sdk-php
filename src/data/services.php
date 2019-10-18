<?php
/**
 * Lista de serviços do SDK.
 */

return [
    'Nws\Admin\AdminClient' => [
        'endpoints' => require __DIR__ . '/admin/2019-10-18/endpoints.php',
        'functions' => require __DIR__ . '/admin/2019-10-18/functions.php',
    ],
];