<?php

return [
    'HttpStatus' => [
        'OK' => 200,
        'CREATED' => 201,
        'BAD_REQUEST' => 400,
        'UNAUTHORIZED' => 401,
        'VALIDATION_EXCEPTION' => 422,
        'FORBIDDEN' => 403,
        'PAYMENT_REQUIRED' => 402,
    ],
    'UserRoles' => [],
    'UserProfile' => [
        'PROFILE_PICTURES' => ['public/uploads/profile_pictures', 'public/storage/profile_pictures'],
        'SHOP_PICTURES' => ['public/uploads/shop', 'public/storage/shop'],
    ],
    'CourseMediaPath' => [
        'THUMBNAIL_IMAGE' => ['public/courses/thumbnail_image', 'public/storage/courses/thumbnail_image'],
        'PREVIEW_VIDEO' => ['public/courses/preview_video', 'public/storage/courses/preview_video'],
        'LECTURE_MEDIA' => ['public/courses/lecture_media', 'public/storage/courses/lecture_media'],
    ],
    'DefaultValues' => [
        'WATER_TRACK_MAX_VALUE' => 64,
        'WATER_TRACK_MIN_VALUE' => 32,
        'PAGINATION_RECORD' => 10,
    ],
    'Validation' => [
        'PHONE_NUMBER_LENGTH' => '8,10',
        'PHONE_NUMBER' => ['MIN' => '8', 'MAX' => '10'],
        'PHONE_COUNTRY_CODE_MAX_LENGTH' => 4,
        'PHONE_COUNTRY_MAX_LENGTH' => 10,
        'AGE' => '1,3',
    ],
    'Regex' => [
        'EMAIL' => '/^([a-zA-Z0-9_\-\.\+]+)@([a-zA-Z0-9_\-\.]+)\.([a-zA-Z]{2,5})$/i',
        'PASSWORD' => '/^(?=.*?[a-zA-Z])(?=.*?[0-9]).{6,}$/',
        'NAME' => '/^[a-zA-Z0-9\s@(*&^%$#]+$/m',
        'COMMISSION' => '/^(\d{1,2}(\.\d{1,2})?|100(\.00?)?)$/',
        'ALPHA_NUM' => '/^[a-zA-Z0-9]+$/m',
        'ALPHA_NUM_WITH_SPACE' => '/^[a-zA-Z0-9\s]+$/m',
        'URL' => '/(https?):\/\/([\w_-]+(?:(?:\.[\w_-]+)+))([\w.,@?^=%&:\/~+#-]*[\w@?^=%&\/~+#-])?/m',
    ],
    'Message' => [
        'TRY_AGAIN' => 'Please try again.',
    ],
];
