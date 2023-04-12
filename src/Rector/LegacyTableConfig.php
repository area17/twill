<?php

namespace A17\Twill\Rector;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use PhpParser\Node;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

class LegacyTableConfig extends LaravelAwareRectorRule
{
    protected array $tables;

    public function configure(array $configuration): void
    {
        $this->tables = $configuration['tables'] ?? [];
        parent::configure($configuration);
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Add back legacy tables to the config',
            []
        );
    }

    public function getNodeTypes(): array
    {
        return [Node\Stmt\Return_::class];
    }

    public function refactor(Node $node)
    {
        assert($node instanceof Node\Stmt\Return_);
        assert($node->expr instanceof Node\Expr\Array_);

        // Keep tables that cannot be found in the DB.
        $tables = [];
        foreach ($this->tables as $name => $table) {
            if (!Schema::hasTable(Config::get('twill.' . $name))) {
                $tables[$name] = $table;
            }
        }

        // Remove tables already present in the config.
        foreach ($node->expr->items as $item) {
            if (
                $item?->key instanceof Node\Scalar\String_
                && array_key_exists($item?->key->value, $tables)
            ) {
                unset($tables[$item?->key->value]);
            }
        }

        foreach ($tables as $name => $table) {
            $node->expr->items[] = new Node\Expr\ArrayItem(
                new Node\Scalar\String_($table),
                new Node\Scalar\String_($name),
            );
        }

        return $node;
    }
}
