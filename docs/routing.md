# Routing

Routes live in [routes/web.php](../routes/web.php). The router is created in [public/index.php](../public/index.php).

## Basics

```php
use App\Controllers\HomeController;

$router->get('/', [HomeController::class, 'index']);
$router->post('/users', [UserController::class, 'store']);
$router->put('/users/{id}', [UserController::class, 'update']);
$router->delete('/users/{id}', [UserController::class, 'destroy']);
```

## Closures

```php
$router->get('/health', function () {
    return ['ok' => true];
});
```

Arrays are returned as JSON. Strings and `Response` objects work as-is.

## Path parameters

```php
$router->get('/users/{id}', [UserController::class, 'show']);
```

```php
public function show(Request $request, string $id): Response
{
    // ...
}
```

## Controllers

Extend `Core\Controller`:

```php
namespace App\Controllers;

use Core\Controller;
use Core\Request;
use Core\Response;

class HomeController extends Controller
{
    public function index(Request $request): Response
    {
        return $this->view('pages/home', ['title' => 'Welcome']);
    }
}
```

Helpers on the base controller: `view()`, `json()`, `redirect()`.

Next: [Views](views.md) · [Database](database.md)
