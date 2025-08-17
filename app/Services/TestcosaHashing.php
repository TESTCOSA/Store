<?php

namespace App\Services;

use Illuminate\Contracts\Hashing\Hasher as HasherContract;

class TestcosaHashing implements HasherContract
{
    public function make($value, array $options = [])
    {
        // Apply triple MD5 hashing
        return md5(md5(md5($value)));
    }

    public function check($value, $hashedValue, array $options = [])
    {
        // Check if the hashed value matches
        return md5(md5(md5($value))) === $hashedValue;
    }

    public function needsRehash($hashedValue, array $options = [])
    {
        // Triple MD5 doesn't typically need rehashing
        return false;
    }

    public function info($hashedValue)
    {
        // Return information about the hashing
        return [
            'algo' => 'md5',
            'algoName' => 'Triple MD5',
        ];
    }
}
