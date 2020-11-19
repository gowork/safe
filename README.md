# Gowork Safe - Type Safety Tools

Safe accessors wraps unsafe or uncertain associative data structures and provides methods of safe type casting.
Mainly for Symfony. 

## SafeAccessorTrait methods
 
#### `bool(string $key, bool $default = false): bool`
* casts value to `bool` (true, false, 0, 1) if possible
* or throws `InvalidArgumentException` when value set but cannot be casted
* or returns default when value not set

#### `boolOrDefault(string $key, bool $default): bool`
* casts value to `bool` (true, false, 0, 1) 
* or returns default

#### `string(string $key, string $default = ''): string`
* casts value to `string` if possible
* or throws `InvalidArgumentException` when value set but cannot be casted
* or returns default when value not set

#### `stringNullable(string $key, ?string $default = null): ?string`
* casts value to `string` if possible
* or returns default when value not set or is `null`
* or throws `InvalidArgumentException` when value not `null` but cannot be casted

#### `stringOrDefault(string $key, string $default): string`
* casts value to `string` if possible 
* or returns default

#### `int(string $key, int $default = 0): int`
* casts value to `int` if possible
* or throws `InvalidArgumentException` when value set but cannot be casted
* or returns default when value not set

#### `intNullable(string $key, ?int $default = null): ?int`
* casts value to `int` if possible
* or returns default when value not set or is `null`
* or throws `InvalidArgumentException` when value not `null` but cannot be casted

#### `intOrDefault(string $key, int $default): int`
* casts value to `int` if possible 
* or returns default

#### `float(string $key, float $default = 0): float`
* casts value to `float` if possible
* or throws `InvalidArgumentException` when value set but cannot be casted
* or returns default when value not set

#### `floatNullable(string $key, ?float $default = null): ?float`
* casts value to `float` if possible
* or returns default when value not set or is `null`
* or throws `InvalidArgumentException` when value not `null` but cannot be casted

#### `floatOrDefault(string $key, float $default): float`
* casts value to `float` if possible 
* or returns default

#### `strings(string $key): array<int, string>`
* casts value to array of strings if possible
* or throws `InvalidArgumentException` when some item cannot be casted

#### `stringsFiltered(string $key): array<int, string>`
* casts value to array of strings skipping items that cannot be casted

#### `stringsForced(string $key, string $default = ''): array<int, string>`
* casts value to array of strings replacing with default items that cannot be casted

#### `ints(string $key): array<int, int>`
* casts value to array of ints if possible
* or throws `InvalidArgumentException` when some item cannot be casted

#### `intsFiltered(string $key): array<int, int>`
* casts value to array of ints skipping items that cannot be casted

#### `intsForced(string $key, int $default = ''): array<int, int>`
* casts value to array of ints replacing with default items that cannot be casted

#### `floats(string $key): array<int, float>`
* casts value to array of floats if possible
* or throws `InvalidArgumentException` when some item cannot be casted

#### `floatsFiltered(string $key): array<int, float>`
* casts value to array of floats skipping items that cannot be casted

#### `floatsForced(string $key, float $default = ''): array<int, float>`
* casts value to array of floats replacing with default items that cannot be casted

#### `array(string $key): SafeAssocArray`
* casts value to associative array and wraps with `SafeAssocArray`
* or throws `InvalidArgumentException` when value cannot be casted

#### `list(string $key): SafeAssocList`
* casts value to list of associative arrays and wraps with `SafeAssocList`
* or throws `InvalidArgumentException` when value cannot be casted

## Accessors

### SafeAssocArray

```php
$user = [
    'name' => 'John',
    'age' => 18,
    'sports' => ['football', 'handball'],
];

$safe = SafeAssocArray::from($user);
$safe->string('name');             // 'John'
$safe->int('age');                 // 18

$safe->string('nickname', '--');   // '-'
$safe->stringNullable('nickname'); // NULL
$safe->string('nickname');         // InvalidArgumentException

$safe->strings('sports');          // ['football', 'handball']
$safe->ints('sports');             // InvalidArgumentException

```

### SafeConsoleInput

```php
final class ExampleCommand extends Command
{
    // ...
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $arguments = SafeConsoleInput::arguments($input);
        
        // require string from argument
        $file = $arguments->string('name');
    
        $options = SafeConsoleInput::options($input);
        
        // integer with default value
        $limit = $options->int('limit', 20);
        
        // optional integer value
        $pageOrNull = $options->intNullable('page');
        
        // bool
        $isDryRun = $options->bool('dry-run', false);
        
        // string[]
        $tags = $options->strings('tag');
        
        // int[]
        $tags = $options->ints('status');
    }
}
```

### SafeRequest

```php
final class ExampleAction extends Command
{
    // ...
    public function __invoke(Request $request): Response
    {
        $safeRequest = SafeRequest::from($request);
        $query = $safeRequest->query();
        $post = $safeReques->request();
        $attributes = $safeReques->attributes();
        
        $ip = $safeReques->ip();
        $postId = $attributes->string('postId');
        $tags = $post->strings('tags');
        // ...
    }
}
```
