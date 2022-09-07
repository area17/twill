<?php

namespace App\Models\Revisions;

use A17\Twill\Models\Revision;

class RevisionLimitedRevision extends Revision
{
    protected $table = "revision_limited_revisions";
}
