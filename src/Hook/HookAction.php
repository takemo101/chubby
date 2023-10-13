<?php

namespace Takemo101\Chubby\Hook;

use Closure;

/**
 * フックアクション
 */
final class HookAction
{
    /**
     * @var string
     */
    public readonly string $key;

    public function __construct(
        public readonly Closure $function,
    ) {
        $this->key = $this->createUniqueKey($function);
    }

    /**
     * callableな値からキーの生成
     *
     * @param Closure $function
     * @return string
     */
    private function createUniqueKey(Closure $function): string
    {
        return spl_object_hash($function);
    }

    /**
     * callableな値から生成する
     *
     * @param callable $callable
     * @return self
     */
    public static function fromCallable(callable $callable): self
    {
        return new self(
            Closure::fromCallable($callable),
        );
    }
}
