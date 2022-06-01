<?php

namespace A17\Twill\Commands\Traits;

trait HandlesStubs
{
    /**
     * @param array $variables
     * @param string $stub
     * @param array|null $delimiters
     * @return string
     */
    public function replaceVariables($variables, $stub, $delimiters = null)
    {
        $delimiters = $delimiters ?: ['{{', '}}'];

        foreach ($variables as $key => $value) {
            $key = "{$delimiters[0]}{$key}{$delimiters[1]}";

            $stub = str_replace($key, $value, $stub);
        }

        return $stub;
    }

    /**
     * @param array $variables
     * @param string $stub
     * @param array|null $delimiters
     * @return string
     */
    public function replaceConditionals($conditionals, $stub, $delimiters = null)
    {
        $delimiters = $delimiters ?: ['{{', '}}'];

        foreach ($conditionals as $key => $value) {
            $start = "{$delimiters[0]}{$key}{$delimiters[1]}";
            $end = "{$delimiters[0]}\/{$key}{$delimiters[1]}";

            if ((bool)$value) {
                // replace delimiters only
                $stub = preg_replace("/$start/", '', $stub);
                $stub = preg_replace("/$end/", '', $stub);
            } else {
                // replace delimiters and everything between
                $anything = '[\s\S]+?';
                $stub = preg_replace("/{$start}{$anything}{$end}/", '', $stub);
            }
        }

        return $stub;
    }

    /**
     * @param string $stub
     * @return string
     */
    public function removeEmptyLinesWithOnlySpaces($stub)
    {
        return preg_replace('#^ +\n#m', '', $stub);
    }
}
