<?php

/*
 * This file is part of the Predis package.
 *
 * (c) Daniele Alessandri <suppakilla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Predis\Protocol;

use Predis\Commands\ICommand;
use Predis\Network\IConnectionComposable;

interface IProtocolProcessor extends IResponseReader
{
    public function write(IConnectionComposable $connection, ICommand $command);
    public function setOption($option, $value);
}