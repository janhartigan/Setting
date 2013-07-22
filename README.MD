# Laravel Setting

Persistent configuration settings for Laravel - Create, Read, Update and Delete settings stored in cache

This package was the result of me not being able to save new settings to config files in a more persistent way.

This package was designed not to replace the config solution currently offered by Laravel but rather complement it and be used in unison with it.

If you need to store permanent data then creating a config file is still the best way.

FAST!
It uses the standard Laravel cache system so it's lightning fast.

## Installation
Require this package in your composer.json:

    "philf/setting": "1.*"

And add the ServiceProvider to the providers array in app/config/app.php

    'Philf\Setting\SettingServiceProvider',

## Usage

Just think arrays :)

Single dimension

    set:     Setting::set(array('name' => 'Phil'))
    put:     Setting::put(array('name' => 'Phil'))
    get:     Setting::get('name')
    forget:  Setting::forget('name')
    has:     Setting::has('name')

Multi dimensional

    set:     Setting::set(array('names' => array('firstname' => 'Phil', 'surname' => 'F')))
    put:     Setting::put(array('names' => array('firstname' => 'Phil', 'surname' => 'F')))
    get:     Setting::get('names.firstname')
    forget:  Setting::forget(array('names' => 'surname'))
    has:     Setting::has('names.firstname')

Using a different path (make sure the path exists and is writable) *

    Setting::path('setting2.json')->set(array('names2' => array('firstname' => 'Phil', 'surname' => 'F')));

Using a different filename

    Setting::filename('setting2.json')->set(array('names2' => array('firstname' => 'Phil', 'surname' => 'F')));

Using both a different path and filename (make sure the path exists and is writable)

    Setting::path(app_path().'/storage/meta/sub')->filename('dummy.json')->set(array('names2' => array('firstname' => 'Phil', 'surname' => 'F')));

## License

Laravel Setting is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)