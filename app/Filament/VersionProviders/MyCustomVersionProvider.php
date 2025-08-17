<?php

use Awcodes\FilamentVersions\Providers\Contracts\VersionProvider;
 
class MyCustomVersionProvider implements VersionProvider
{
    public function getName(): string
    {
        return 'My Custom Version';
    }
 
    public function getVersion(): string
    {
        return '1.0.0';
    }
}
