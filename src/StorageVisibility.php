<?php

declare(strict_types=1);

namespace Talboterie\FlysystemGCPStorage;

class StorageVisibility
{
    public const AUTHENTICATED_READ = 'authenticatedRead';

    public const BUCKET_OWNER_FULL_CONTROL = 'bucketOwnerFullControl';

    public const BUCKET_OWNER_READ = 'bucketOwnerRead';

    public const PRIVATE_OWNER = 'private';

    public const PROJECT_PRIVATE = 'projectPrivate';

    public const PUBLIC_READ = 'publicRead';
}
