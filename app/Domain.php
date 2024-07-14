<?php

namespace App;

class Domain {
    public function __construct()
    {
        $this->setDomain();
    }
    public $domain;
    public function setDomain()
    {
        $this->domain = config('app.url');
        return $this;
    }
}
