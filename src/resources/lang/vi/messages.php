<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Flash Messages & Notifications
    |--------------------------------------------------------------------------
    */

    // Success Messages
    'success' => [
        'created' => ':item đã được tạo thành công.',
        'updated' => ':item đã được cập nhật thành công.',
        'deleted' => ':item đã được xóa thành công.',
        'saved' => 'Đã lưu thành công.',
        'sent' => 'Đã gửi thành công.',
        'uploaded' => 'Tải lên thành công.',
        'approved' => 'Đã phê duyệt thành công.',
        'rejected' => 'Đã từ chối thành công.',
        'profile_updated' => 'Hồ sơ đã được cập nhật.',
        'request_submitted' => 'Yêu cầu đã được gửi đi.',
        'message_sent' => 'Tin nhắn đã được gửi.',
    ],
    
    // Error Messages
    'error' => [
        'generic' => 'Đã xảy ra lỗi. Vui lòng thử lại.',
        'not_found' => 'Không tìm thấy :item.',
        'unauthorized' => 'Bạn không có quyền thực hiện thao tác này.',
        'validation_failed' => 'Dữ liệu không hợp lệ.',
        'upload_failed' => 'Tải lên thất bại.',
        'delete_failed' => 'Không thể xóa :item.',
        'update_failed' => 'Cập nhật thất bại.',
        'server_error' => 'Lỗi máy chủ. Vui lòng thử lại sau.',
    ],
    
    // Warning Messages
    'warning' => [
        'unsaved_changes' => 'Bạn có thay đổi chưa lưu. Bạn có muốn tiếp tục?',
        'delete_confirm' => 'Hành động này không thể hoàn tác. Bạn có chắc chắn?',
        'incomplete_profile' => 'Vui lòng hoàn thiện hồ sơ để tiếp tục.',
    ],
    
    // Info Messages
    'info' => [
        'no_results' => 'Không có kết quả phù hợp.',
        'loading' => 'Đang tải dữ liệu...',
        'processing' => 'Đang xử lý...',
        'email_verification_required' => 'Vui lòng xác thực email để tiếp tục.',
    ],
    
    // Profile/Tutor Messages
    'tutor' => [
        'profile_pending' => 'Hồ sơ của bạn đang chờ xét duyệt.',
        'profile_approved' => 'Hồ sơ của bạn đã được phê duyệt!',
        'profile_rejected' => 'Hồ sơ của bạn đã bị từ chối. Vui lòng kiểm tra lý do và cập nhật lại.',
        'cv_uploaded' => 'CV đã được tải lên thành công.',
        'ai_processing' => 'AI đang phân tích CV của bạn...',
        'ai_complete' => 'Phân tích CV hoàn tất!',
    ],
    
    // Request Messages
    'request' => [
        'created' => 'Yêu cầu đã được tạo thành công.',
        'updated' => 'Yêu cầu đã được cập nhật.',
        'deleted' => 'Yêu cầu đã được xóa.',
        'applied' => 'Đã ứng tuyển thành công.',
        'withdrawn' => 'Đã hủy ứng tuyển.',
    ],
    
    // Payment Messages
    'payment' => [
        'success' => 'Thanh toán thành công.',
        'failed' => 'Thanh toán thất bại.',
        'pending' => 'Thanh toán đang chờ xử lý.',
        'insufficient_funds' => 'Số dư không đủ.',
    ],
];
