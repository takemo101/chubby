<?php

use Tests\Application\ApplicationTestCase;
use Tests\Config\ConfigTestCase;
use Tests\Filesystem\FilesystemTestCase;

uses(ApplicationTestCase::class)->in('Application');
uses(ConfigTestCase::class)->in('Config');
uses(FilesystemTestCase::class)->in('Filesystem');
