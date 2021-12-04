# lumen-middleware


## jsonStandardResponse

Provides a standard way of providing JSON formatted output no matter what the application sends

**For all endpoints**

Modify your `app.php`

```php
$app->middleware([
    treehousetim\lumen_middleware\jsonStandardResponse::class
]);
```

## idUUID

Automatically validates uuid's in url parameters based on routes with the first parameter intended to be a UUID
Only validates it is present and is a proper UUID.

**Use in a controller's `__construct` method**

```php
class MyController extends Controller
{
 public function __construct()
    {
        $this->middleware( 'ID_UUID',
            ['only'=>[
                'get',
                'update',
                'destroy'
            ]]
        );
    }
...
}
```
