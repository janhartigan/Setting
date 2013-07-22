<?php namespace Philf\Setting;

use Illuminate\Config\Repository;

/*
 * ---------------------------------------------
 * | Do not remove!!!!                         |
 * |                                           |
 * | @package   PhoenixCore                    |
 * | @version   2.0                            |
 * | @develper  Phil F (http://www.Weztec.com) |
 * | @author    Phoenix Development Team       |
 * | @license   Free to all                    |
 * | @copyright 2013 Phoenix Group             |
 * | @link      http://www.phoenix-core.com    |
 * ---------------------------------------------
 *
 * Example syntax:
 * use Setting (If you are using namespaces)
 *
 * Single dimension
 * set:     Setting::set(array('name' => 'Phil'))
 * put:     Setting::put(array('name' => 'Phil'))
 * get:     Setting::get('name')
 * forget:  Setting::forget('name')
 * has:     Setting::has('name')
 *
 * Multi dimensional
 * set:     Setting::set(array('names' => array('firstname' => 'Phil', 'surname' => 'F')))
 * put:     Setting::put(array('names' => array('firstname' => 'Phil', 'surname' => 'F')))
 * get:     Setting::get('names.firstname')
 * forget:  Setting::forget(array('names' => 'surname'))
 * has:     Setting::has('names.firstname')
 *
 * Using a different path (make sure the path exists and is writable) *
 * Setting::path('setting2.json')->set(array('names2' => array('firstname' => 'Phil', 'surname' => 'F')));
 *
 * Using a different filename
 * Setting::filename('setting2.json')->set(array('names2' => array('firstname' => 'Phil', 'surname' => 'F')));
 *
 * Using both a different path and filename (make sure the path exists and is writable)
 * Setting::path(app_path().'/storage/meta/sub')->filename('dummy.json')->set(array('names2' => array('firstname' => 'Phil', 'surname' => 'F')));
 */

class Setting {

    /**
     * Illuminate config repository.
     *
     * @var Illuminate\Config\Repository
     */
    protected $config;

    /**
     * The path to the file
     * @var string
     */
    protected $path;

    /**
     * The filename used to store the config
     * @var string
     */
    protected $filename;

    /**
     * The class working array
     * @var array
     */
    protected $settings;

    public function __construct(Repository $config)
    {
        $this->config     = $config;
        $this->path       = $this->config->get('setting::setting.path');
        $this->filename   = $this->config->get('setting::setting.filename');

        // Load the file and store the contents in $this->settings
        $this->load($this->path, $this->filename);
    }

    /**
     * Set the path to the file to use
     * @param  string $path The path to the file
     * @return \Philf\Setting\Setting
     */
    public function path($path)
    {
        $this->path = $path;
        return $this;
    }

    /**
     * Set the filename to use
     * @param  string $filename The filename
     * @return \Philf\Setting\Setting
     */
    public function filename($filename)
    {
        $this->filename = $filename;
        return $this;
    }

    /**
     * Get a value and return it
     * @param  string $searchKey String using dot notation
     * @return Mixed             The value(s) found
     */
    public function get($searchKey)
    {
        return array_get($this->settings, $searchKey);
    }

    /**
     * An alias for put
     * @param mixed $value The value(s) to be stored
     * @return void
     */
    public function set($value)
    {
        $this->put($value);
    }

    /**
     * Store the passed value in to the json file
     * @param  mixed $value The value(s) to be stored
     * @return void
     */
    public function put($value)
    {
        foreach ($value as $key => $val)
        {
            if (isset($this->settings[$key]) and is_array($val))
            {
                foreach($val as $key2 => $val2)
                {
                    if (isset($this->settings[$key]))
                    {
                        $temp  = is_array($this->settings[$key]) ? $this->settings[$key] : array($key2 => $val2);
                        $temp1 = array();

                        $temp1  = array_add($temp1, $key2, $val2);

                        $this->settings[$key] = array_merge($temp, $temp1);
                    }
                    else
                    {
                        $this->settings = array_add($this->settings, $key2, $val2);
                    }
                }
            }
            else
            {
                if (isset($this->settings[$key]))
                {
                    $this->settings[$key] = $val;
                }
                else
                {
                    $this->settings = array_add($this->settings, $key, $val);
                }
            }
        }

        $this->save($this->path, $this->filename);
        $this->load($this->path, $this->filename);
    }

    /**
     * Forget the value(s) currently stored
     * @param  mixed $deleteKey The value(s) to be removed
     * @return void
     */
    public function forget($deleteKey)
    {
        if (is_array($deleteKey))
        {
            foreach($deleteKey as $key => $val)
            {
                unset($this->settings[$key][$val]);
            }
        }
        else
        {
            unset($this->settings[$deleteKey]);
        }

        $this->save($this->path, $this->filename);
        $this->load($this->path, $this->filename);
    }

    /**
     * Check to see if the value exists
     * @param  string  $searchKey The key to search for
     * @return boolean            True: found - False not found
     */
    public function has($searchKey)
    {
        return array_get($this->settings, $searchKey) ? true : false;
    }

    /**
     * Load the file in to $this->settings so values can be used imediately
     * @param  string $path     The path to be used
     * @param  string $filename The filename to be used
     * @return \Philf\Setting\Setting
     */
    public function load($path = null, $filename = null)
    {
        $this->path     = isset($path) ? $path : $this->path;
        $this->filename = isset($filename) ? $filename : $this->filename;

        if (is_file($this->path.'/'.$this->filename))
        {
            $this->settings = json_decode(file_get_contents($this->path.'/'.$this->filename), true);
        }

        return $this;
    }

    /**
     * Save the file
     * @param  string $path     The path to be used
     * @param  string $filename The filename to be used
     * @return void
     */
    public function save($path = null, $filename = null)
    {
        $this->path     = isset($path) ? $path : $this->path;
        $this->filename = isset($filename) ? $filename : $this->filename;

        $fh = fopen($this->path.'/'.$this->filename, 'w+');
        fwrite($fh, json_encode($this->settings, JSON_UNESCAPED_UNICODE));
        fclose($fh);
    }
}