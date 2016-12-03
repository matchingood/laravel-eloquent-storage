# laravel-eloquent-storage
Eloquent model which manages files easily

```php
$file = $request->file('file');
$userFile = new UserFile;
$userFile->saveFromUploadedFile($file);

$userFile->getContent(); // the content in the $file
```

## Installation
In your composer.json,
```
"matchingood/laravel-eloquent-storage": "^0.2"
```
Then you register EloquentStorage at config/app.php.
```
'providers' => [
    .
    .
    .
    MatchinGood\EloquentStorage\EloquentStorageServiceProvider::class
],
```
You can create the configuration file to execute
```
$ php artisan vendor:publish
```
Then you can configure app/eloquentstorage.php

## Usage

First of all, you have to add 3 specific columns on the tables you want to let it manage file.
```php
$table->string('file_name');
$table->string('unique_file_name');
$table->string('directory');
```

Then, you can enable eloquet models to manage files by using `MatchinGood\EloquentStorage`.

```php
class UserFile extends MatchinGood\EloquentStorage\EloquentStorage
{

}
```

There are 2 ways to store files through EloquentStorage.
```php
$userFile = new UserFile;

// 1. store text
$userFile->saveContent('file name', 'content');

// 2. store uploaded file
$file = $request->file('file');
$userFile->saveFromUploadedFile($file);
```

Then you can check the new record on the table and the new file you saved at `storage/user_files/xxxxxxx`.

This library also provides response macros.
```php
$userFile = UserFile::find(1);

return response()->downloadEloquentStorage($userFile);
```
