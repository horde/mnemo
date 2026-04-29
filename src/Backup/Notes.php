<?php

declare(strict_types=1);

/**
 * Copyright 2017-2026 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (ASL). If you
 * did not receive this file, see http://www.horde.org/licenses/apache.
 *
 * @author  Jan Schneider <jan@horde.org>
 * @package Mnemo
 */

namespace Horde\Mnemo\Backup;

use ArrayIterator;
use Iterator;
use Mnemo_Driver;

/**
 * Backup iterator for notes.
 */
class Notes implements Iterator
{
    /**
     * The driver instance.
     */
    protected Mnemo_Driver $_driver;

    /**
     * Iterator over the driver instance.
     */
    protected ArrayIterator $_iterator;

    /**
     * Constructor.
     */
    public function __construct(Mnemo_Driver $driver)
    {
        $this->_driver = $driver;
    }

    public function current(): array
    {
        return array_intersect_key(
            $this->_iterator->current(),
            [
                'memolist_id' => true,
                'uid' => true,
                'desc' => true,
                'body' => true,
                'tags' => true,
            ]
        );
    }

    public function key(): int
    {
        return $this->_iterator->key();
    }

    public function next(): void
    {
        $this->_iterator->next();
    }

    public function rewind(): void
    {
        $this->_driver->retrieve(true);
        $this->_iterator = new ArrayIterator($this->_driver->listMemos());
    }

    public function valid(): bool
    {
        return $this->_iterator->valid();
    }
}
