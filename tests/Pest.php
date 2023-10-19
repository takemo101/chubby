<?php

use Tests\Application\ApplicationTestCase;
use Tests\Config\ConfigTestCase;
use Tests\Filesystem\FilesystemTestCase;
use Tests\AppTestCase;

uses(ApplicationTestCase::class)->in('Application');
uses(ConfigTestCase::class)->in('Config');
uses(FilesystemTestCase::class)->in('Filesystem');
uses(AppTestCase::class)->in('Http', 'Console');
