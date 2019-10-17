# Service Admin

## Exemplo rápido

### Criar um Admin client

```php
<?php
// Require the Composer autoloader.
require 'vendor/autoload.php';

use NetForce\Sdk\Admin\AdminClient;

// Instantiate an Amazon S3 client.
$admin = new AdminClient([
    'version'      => 'latest',
    'environment'  => AdminClient::envSandbox,
]);
```

### Fazer login

```php
<?php
// Require the Composer autoloader.
require 'vendor/autoload.php';

use NetForce\Sdk\Admin\AdminClient;

//... criar um AdminClient...

// Fazer login e carregar informações do usuário logado
$user = $admin->login('seuemail@netforce.com.br', 'sua_senha');

// Carregar informações do usuario logado
$me = $admin->me();
```