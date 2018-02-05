<?php declare(strict_types=1); # -*- coding: utf-8 -*-
/*
 * This file is part of the php-coding-standards package.
 *
 * (c) Inpsyde GmbH
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * This file contains code from "phpcs-neutron-standard" repository
 * found at https://github.com/Automattic/phpcs-neutron-standard
 * Copyright (c) Automattic
 * released under MIT license.
 */

namespace Inpsyde\InpsydeCodingStandard\Sniffs\CodeQuality;

use Inpsyde\InpsydeCodingStandard\Helpers;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;

class HookClosureReturnSniff implements Sniff
{
    /**
     * @inheritdoc
     */
    public function register()
    {
        return [T_CLOSURE];
    }

    /**
     * @inheritdoc
     */
    public function process(File $file, $position)
    {
        if (!Helpers::isHookClosure($file, $position)) {
            return;
        }

        list($functionStart, $functionEnd) = Helpers::functionBoundaries($file, $position);
        if (!$functionStart < 0 || $functionEnd <= 0) {
            return;
        }

        list($nonVoidReturnCount, $voidReturnCount) = Helpers::countReturns($file, $position);

        $isFilterClosure = Helpers::isHookClosure($file, $position, true, false);

        if ($isFilterClosure && (!$nonVoidReturnCount || $voidReturnCount)) {
            $file->addError(
                'No (or void) return from filter closure.',
                $position,
                'NoReturnFromFilter'
            );
        }

        if (!$isFilterClosure && $nonVoidReturnCount) {
            $file->addError('Return value from action closure.', $position, 'ReturnFromAction');
        }
    }
}