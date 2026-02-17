<?php

namespace App\Traits;

trait HashableId
{
    /**
     * Encode numeric ID to Hash (Custom Implementation)
     */
    protected static function encodeId($id)
    {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $base = strlen($alphabet);
        $salt = config('app.key', 'smi-v-buriram-salt');

        // Simple Obfuscation logic
        $val = $id + 10000; // Offset to avoid short hashes for low IDs
        $hash = '';
        while ($val > 0) {
            $hash = $alphabet[$val % $base] . $hash;
            $val = floor($val / $base);
        }
        return strrev($hash); // Flip it to look more random
    }

    /**
     * Decode Hash back to numeric ID
     */
    protected static function decodeId($hash)
    {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $base = strlen($alphabet);
        $hash = strrev($hash);
        $val = 0;
        for ($i = 0; $i < strlen($hash); $i++) {
            $pos = strpos($alphabet, $hash[$i]);
            if ($pos === false) return null;
            $val = $val * $base + $pos;
        }
        return $val - 10000;
    }

    public function getRouteKey()
    {
        return static::encodeId($this->getKey());
    }

    public function resolveRouteBinding($value, $field = null)
    {
        $id = static::decodeId($value);
        if ($id === null || $id < 0) {
            return null;
        }
        return parent::resolveRouteBinding($id, $field);
    }
}
