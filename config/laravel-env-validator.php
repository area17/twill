<?php

return [
    'APP_ENV' => 'required|in:local,development,staging,production',
    'APP_KEY' => 'required',
    'DB_DATABASE' => 'required',
    'DB_USERNAME' => 'required',
    // 'S3_KEY' => 'required',
    // 'S3_SECRET' => 'required',
    // 'S3_BUCKET' => 'required',
    // 'IMGIX_SOURCE_HOST' => 'required',
    // 'IMGIX_SIGN_KEY' => 'required_if:IMGIX_USE_SIGNED_URLS,true',
    // 'SEO_IMAGE_DEFAULT_ID' => 'required_if:APP_ENV,production',
    // 'SEO_IMAGE_LOCAL_FALLBACK' => 'required_if:APP_ENV,production',
    // 'ROLLBAR_TOKEN' => 'required_if:APP_ENV,production',
    // 'ROLLBAR_LEVEL' => 'required_if:APP_ENV,production',
    // 'GOOGLE_ANALYTICS_KEY' => 'required_if:APP_ENV,production',
];
