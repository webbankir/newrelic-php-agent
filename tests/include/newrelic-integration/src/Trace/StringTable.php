<?php
/*
 * Copyright 2020 New Relic Corporation. All rights reserved.
 * SPDX-License-Identifier: Apache-2.0
 */

namespace NewRelic\Integration\Trace;

use OutOfBoundsException;

/**
 * A string table within a transaction trace.
 *
 * String tables are used in condensed transaction traces
 * such as those generated by the PHP agent and detailed in
 * the newrelic-internal Transaction Traces specification.
 */
class StringTable
{
    /**
     * The string references defined in the table.
     *
     * @var string[]
     */
    protected $strings;

    /**
     * Constructs a new string table.
     *
     * @param string[] $strings The string table.
     */
    public function __construct(array $strings)
    {
        $this->strings = $strings;
    }

    /**
     * Retrieves a string reference by numeric key.
     *
     * Users of this class should generally use `resolve()`.
     *
     * @param integer $key
     * @return string
     * @throws OutOfBoundsException Thrown if the key is out of bounds.
     */
    public function get($key)
    {
        if (isset($this->strings[$key])) {
            return $this->strings[$key];
        }
        throw new OutOfBoundsException;
    }

    /**
     * Resolves a string.
     *
     * If the string is not a reference, then the original string is returned
     * unchanged.
     *
     * @param string $string The string to resolve.
     * @return string
     */
    public function resolve($string)
    {
        $matches = null;
        if (preg_match('/^`([0-9]+)$/', $string, $matches)) {
            try {
                return $this->get($matches[1]);
            } catch (OutOfBoundsException $e) {
                // Ignore it; fall through to return the original string.
            }
        }

        return $string;
    }
}
