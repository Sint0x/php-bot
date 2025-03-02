<?php

class BaseModel
{
    public function __construct($data = []) {
        
        $properties = get_object_vars($this);
        
        foreach ($properties as $key => $value) {
            if (array_key_exists($key, $data)) {
                $this->$key = $data[$key];
            } else if ($value !== false) {
                throw new \Exception("Required: " . $key);
            }
        }
    }
}
