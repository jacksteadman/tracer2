<?php
namespace App;

class Action {
    private $category;
    private $name;
    private $timestamp;
    private $user_id;
    private $session_id;

    private $app;
    private $app_user_id;

    private $referral_code;
    private $referring_user_id;
    private $referring_timestamp;

    private $data = [];
    private $tags = [];
    private $sources = [];

    private $uniqueness_key;
    private $counter_key;

    private $user_agent_raw;
    private $http_referrer_raw;

    private $ip;
    private $location;

    public function validate()
    {
        $missing_fields = [];
        foreach (['category', 'name', 'timestamp', 'user_id', 'app', 'uniqueness_key', 'counter_key'] as $required_field) {
            if (!$this->$required_field) {
                $missing_fields[] = $required_field;
            }
        }
        if (!empty($missing_fields)) {
            throw new \Exception('Missing required fields: ' . join(', ', $missing_fields));
        }

        if (!preg_match('/^\d{4}-\d{2}-\d{2}::\d{2}:\d{2}:\d{2}$/', $this->timestamp)) {
            throw new \Exception('Invalid timestamp format.');
        }

        return true;
    }

    public function fromJson($json) {
        $post = json_decode($json, true);

        if ($post === null) {
            throw new \Exception('Failed decoding input [' . $json . ']: ' . json_last_error());
        }

        foreach (array_keys($post) as $key) {
            $this->$key = $post[$key];
        }
    }

    public function toArray() {
        $array = [];
        foreach (array_keys(get_object_vars($this)) as $var) {
            if ($this->$var !== null) {
                $array[$var] = $this->$var;
            }
        }
        return $array;
    }

    public function fromArray($array) {
        foreach (array_keys($array) as $key) {
            $this->$key = $array[$key];
        }
    }


    public function __get($property) {
        return $this->$property;
    }

    public function __set($property, $value) {
        switch ($property) {
            case 'tags':
            case 'sources':
                if (!is_array($value)) return false;
                $value = array_unique($value);
                break;
        }
        return $this->$property = $value;
    }

}