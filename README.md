# Gowork Safe - Type Safety Tools

Mainly for Symfony

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
