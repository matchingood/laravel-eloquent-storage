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
"matchingood/laravel-eloquent-storage": "^0.2.1"
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
// original file name
$table->string('file_name');

// unique file name to avoid conflicting original names
$table->string('unique_file_name');

// directory to store directory information
// for using directory parameters on saveConent or saveUploadedFile
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

// you can add root directory like below
// in this case with local driver,
// it's gonna be stored at storage/app/root/user_files/xxxxxx
$userFile->saveFromUploadedFile($file, 'root');
```

Then you can check the new record on the table and the new file you saved at `storage/user_files/xxxxxxx`.

This library also provides response macros.
```php
$userFile = UserFile::find(1);

return response()->downloadEloquentStorage($userFile);
```
