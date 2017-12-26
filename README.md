# pecee/opensubtitles-php-sdk

PHP SDK for retrieving subtitles from OpenSubtitles.org.

## Credits

Credits to [kminek](https://github.com/kminek/open-subtitles) for his original work on this wrapper.

## Usage

```php
$client = Kminek\OpenSubtitles\Client::create([
    'username'  => 'USERNAME',
    'password'  => 'PASSWORD',
    'useragent' => 'USERAGENT',
]);

$response = $client->searchSubtitles([
    [
        'sublanguageid' => 'pol',
        'moviehash' => '163ce22b6261f50a',
        'moviebytesize' => '2094235131',
    ]
]);

var_dump($response->toArray());
```
