# Polish Profanity Filter 🇵🇱

Lightweight PHP library for detecting and masking Polish profanity in text.

Supports:
- case-insensitive detection
- Unicode-safe matching
- configurable masking strategies
- custom dictionary providers
- custom search patterns
- match position detection (start/end offsets)

Designed for moderation systems, comments filtering, validation pipelines, and content sanitization.

---

## Requirements

- PHP 8.1
- ext-mbstring

## Installation

```bash
composer require szymanski-luk/polish-profanity-filter
```

## Basic usage

### Checking if text contains profanity

```php
use PolishProfanityFilter\PolishProfanityFilter;

$filter = new PolishProfanityFilter();

$filter->containsProfanity('To jest kurwa test.'); // true
```

### Finding matches with offsets

```php
$matches = $filter->findProfanities('To jest kurwa test.'); // MatchCollection object

foreach ($matches as $match) {
    echo $match->word;
    echo $match->start;
    echo $match->end;
}
```

Example result:
```php
word: 'kurwa'
start: 8
end: 13
```

`findProfanities()` returns a `MatchCollection` object. Available methods:
- `isEmpty(): bool`
- `all(): array`
- `first(): ?MatchResult`
- `last(): ?MatchResult`
- `count(): int`
- `containsWord(string $word, bool $caseInsensitive = true): bool`

### Masking profanity

```php
$filter->maskProfanities('To jest kurwa test.'); // To jest k***a test.
```

## Extending

This package can be extended with custom maskers, additional dictionary providers, and custom search patterns.

### Custom Masker

To create a custom Masker, create a class that implements the `MaskerInterface`.

Example:
```php
class CustomMasker implements MaskerInterface
{
    public function mask(string $word): string
    {
        $length = mb_strlen($word);
    
        return str_repeat('*', $length)
    }
}
```

Now you should instantiate `PolishProfanityFilter` as follows:

```php
$filter = new PolishProfanityFilter(masker: new CustomMasker());
```

### Additional Dictionary

To create an additional Dictionary, create a class that implements the `DictionaryProviderInterface`.

```php
class AdditionalDictionaryProvider implements DictionaryProviderInterface
{
    public function getDictionary(): array
    {
        return ['word1', 'word2'];
    }
}
```

Now you should instantiate `PolishProfanityFilter` as follows:

```php
$filter = new PolishProfanityFilter(additionalDictionaryProviders: [new AdditionalDictionaryProvider()]);
```

...or you can override default Dictionary:

```php
$filter = new PolishProfanityFilter(defaultDictionaryProvider: new AdditionalDictionaryProvider());
```


### Custom SearchPattern

To create a custom SearchPattern, create a class that implements the `SearchPatternInterface`.

```php
class CustomSearchPattern implements SearchPatternInterface
{
    public function buildPattern(string $word): string
    {
        return '/(?<![\p{L}\p{N}_])' . preg_quote($word, '/') . '(?![\p{L}\p{N}_])/iu';
    }
}
```

Now you should instantiate `PolishProfanityFilter` as follows:

```php
$filter = new PolishProfanityFilter(searchPattern: new CustomSearchPattern());
```

## Advanced Usage

If you use Symfony and want to register `PolishProfanityFilter` as a service, you can do it like this:
```yaml
# services.yaml

PolishProfanityFilter\PolishProfanityFilter: ~
```

or (in case you want to extend filter) 

```yaml
# services.yaml

App\Custom\CustomDictionaryProvider: ~

PolishProfanityFilter\PolishProfanityFilter:
    arguments:
        $additionalDictionaryProviders: ['@App\Custom\CustomDictionaryProvider']
```

## Contributing

Contributions are welcome.

You can help by:
- improving the default dictionary
- improving search patterns
- fixing bugs
- adding tests and documentation

Please open an issue or submit a pull request.


## Development

Run tests:
```bash
composer test
```

Run static analysis:
```bash
composer stan
```

Run code style:
```bash
composer cs-check 
```

Fix code style:
```bash
composer cs-fix
```

## License

MIT