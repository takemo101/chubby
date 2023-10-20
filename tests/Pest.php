<?php

use Tests\Config\ConfigTestCase;
use Tests\Filesystem\FilesystemTestCase;
use Tests\AppTestCase;
use Tests\ApplicationTestCase;

uses(ApplicationTestCase::class)->in('Application', 'Provider');
uses(ConfigTestCase::class)->in('Config');
uses(FilesystemTestCase::class)->in('Filesystem');
uses(AppTestCase::class)->in('Http', 'Console');
