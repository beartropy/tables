<?php

/**
 * @deprecated Use \Beartropy\Tables\BeartropyTable instead.
 *
 * This file exists for backward compatibility and autoload safety.
 * The real class is BeartropyTable; this alias ensures that existing
 * code using "extends YATBaseTable" continues to work.
 */

namespace Beartropy\Tables;

if (! class_exists(BeartropyTable::class, false)) {
    require_once __DIR__.'/BeartropyTable.php';
}

class_alias(BeartropyTable::class, YATBaseTable::class);
