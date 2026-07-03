<?php

return [
    'adminEmail' => 'admin@example.com',
    'supportEmail' => 'support@example.com',
    'senderEmail' => 'noreply@example.com',
    'senderName' => 'Example.com mailer',
    'user.passwordResetTokenExpire' => 3600,
    'user.passwordMinLength' => 8,
    // Base URL của frontend web root (nơi phục vụ thư mục uploads). '' = cùng origin (dùng cho frontend).
    'frontendUrl' => '',
    // Thư mục ảnh (dưới frontend/web/), dùng cho cả path lưu DB lẫn URL hiển thị.
    'uploadsWebDir' => 'uploads',
];
