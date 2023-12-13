<?php

use Takemo101\Chubby\Console\Command\ServeProcessOutputHandler;
use Symfony\Component\Console\Output\OutputInterface;
use Mockery as m;
use Takemo101\Chubby\Console\Command\ServeProcessRequestPool;

describe(
    'ServeProcessOutputHandler',
    function () {

        it('outputs the server start message', function () {
            $datetime = new DateTimeImmutable();
            $requestPool = new ServeProcessRequestPool();

            $output = m::mock(OutputInterface::class);

            $output->shouldReceive('writeln')->with('Development Server (http://localhost:8000) started');
            $output->shouldReceive('writeln')->with('<fg=yellow;options=bold>Press Ctrl+C to stop the server</>');
            $output->shouldReceive('writeln')->with('<fg=gray>Started at:</> ' . $datetime->format('Y-m-d H:i:s'));

            $handler = new ServeProcessOutputHandler($output, false, $datetime, $requestPool);
            $handler('out', "Development Server (http://localhost:8000) started\n");

            expect($requestPool->getRequests())->toBe([]);
        });

        it('outputs the request message and duration when a request is completed', function () {
            $datetime = new DateTimeImmutable();
            $requestPool = new ServeProcessRequestPool();

            $output = m::mock(OutputInterface::class);

            $output->shouldReceive('write')->with('127.0.0.1:1234');
            $output->shouldReceive('write')->with(" <fg=green>GET /</>");
            $output->shouldReceive('writeln')->with(" {$datetime->format('Y-m-d H:i:s')} ~ <fg=gray>1s</>");

            $closedAt = $datetime->modify('+1 second');

            $handler = new ServeProcessOutputHandler($output, false, $datetime, $requestPool);
            $handler('out', "[{$datetime->format('D M d H:i:s Y')}] 127.0.0.1:1234 Accepted");
            $handler('out', "[{$closedAt->format('D M d H:i:s Y')}] 127.0.0.1:1234 Closing");

            expect($requestPool->getRequests())->toBe([]);
        });

        it('outputs a warning message when a request host is not found', function () {
            $requestPool = new ServeProcessRequestPool();

            $output = m::mock(OutputInterface::class);

            $output->shouldReceive('writeln')->with("<fg=red;options=bold>Invalid line</>");

            $handler = new ServeProcessOutputHandler(
                output: $output,
                requestPool: $requestPool,
            );
            $handler('out', "Invalid line\n");

            expect($requestPool->getRequests())->toBe([]);
        });

        it('sets the request message when a request host is found', function () {
            $datetime = new DateTimeImmutable();
            $requestPool = new ServeProcessRequestPool();

            $output = m::mock(OutputInterface::class);

            $handler = new ServeProcessOutputHandler($output, false, $datetime, $requestPool);
            $handler('out', "[{$datetime->format('D M d H:i:s Y')}] 127.0.0.1:1234 Accepted");
            $handler('out', "[{$datetime->format('D M d H:i:s Y')}] 127.0.0.1:1234 [200]: GET / This is a test message");

            $actual = $requestPool->getRequests();

            expect($actual)->toHaveKey('127.0.0.1:1234');
        });
    },
)->group('ServeProcessOutputHandler', 'console');
