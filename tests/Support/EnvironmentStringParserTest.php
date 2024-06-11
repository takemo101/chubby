<?php

use Takemo101\Chubby\Support\EnvironmentStringParser;

beforeEach(function () {
    $this->parser = new EnvironmentStringParser();
});

describe(
    'EnvironmentStringParser',
    function () {
        it('should parse "true" string to true', function () {
            $result = $this->parser->parse('true');
            expect($result)->toBeTrue();
        });

        it('should parse "(true)" string to true', function () {
            $result = $this->parser->parse('(true)');
            expect($result)->toBeTrue();
        });

        it('should parse "false" string to false', function () {
            $result = $this->parser->parse('false');
            expect($result)->toBeFalse();
        });

        it('should parse "(false)" string to false', function () {
            $result = $this->parser->parse('(false)');
            expect($result)->toBeFalse();
        });

        it('should parse "empty" string to empty string', function () {
            $result = $this->parser->parse('empty');
            expect($result)->toBe('');
        });

        it('should parse "(empty)" string to empty string', function () {
            $result = $this->parser->parse('(empty)');
            expect($result)->toBe('');
        });

        it('should parse "null" string to null', function () {
            $result = $this->parser->parse('null');
            expect($result)->toBeNull();
        });

        it('should parse "(null)" string to null', function () {
            $result = $this->parser->parse('(null)');
            expect($result)->toBeNull();
        });

        it('should parse quoted string to unquoted string', function () {
            $result = $this->parser->parse('"quoted"');

            expect($result)->toBe('quoted');
        });

        it('should parse default value', function () {
            $result = $this->parser->parse('default');
            expect($result)->toBe('default');
        });
    }
)->group('EnvironmentStringParser', 'support');
