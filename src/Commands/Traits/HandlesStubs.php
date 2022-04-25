<?php

namespace A17\Twill\Commands\Traits;

trait HandlesStubs
{
    /**
     * @param array|null $delimiters
     * @return string
     * @param mixed[] $variables
     */
    public function replaceVariables(array $variables, string $stub, $delimiters = null)
    {
        $delimiters = $delimiters ?: ['{{', '}}'];

        foreach ($variables as $key => $value) {
            $key = sprintf('%s%s%s', $delimiters[0], $key, $delimiters[1]);

            $stub = str_replace($key, $value, $stub);
        }

        return $stub;
    }

    /**
     * @param array $variables
     * @param array|null $delimiters
     * @return string
     */
    public function replaceConditionals($conditionals, string $stub, $delimiters = null)
    {
        $delimiters = $delimiters ?: ['{{', '}}'];

        foreach ($conditionals as $key => $value) {
            $start = sprintf('%s%s%s', $delimiters[0], $key, $delimiters[1]);
            $end = sprintf('%s\/%s%s', $delimiters[0], $key, $delimiters[1]);

            if ((bool)$value) {
                // replace delimiters only
                $stub = preg_replace(sprintf('/%s/', $start), '', $stub);
                $stub = preg_replace(sprintf('/%s/', $end), '', $stub);
            } else {
                // replace delimiters and everything between
                $anything = '[\s\S]+?';
                $stub = preg_replace(sprintf('/%s%s%s/', $start, $anything, $end), '', $stub);
            }
        }

        return $stub;
    }

    /**
     * @return string
     */
    public function removeEmptyLinesWithOnlySpaces(string $stub)
    {
        return preg_replace('#^ +\n#m', '', $stub);
    }
}
