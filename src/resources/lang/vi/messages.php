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
    
    // Profile & Password Messages
    'profile_updated' => 'Cập nhật hồ sơ thành công!',
    'password_updated' => 'Mật khẩu đã được thay đổi thành công!',
    'avatar_uploaded' => 'Ảnh đại diện đã được cập nhật!',
    'update_error' => 'Lỗi khi cập nhật: :error',
    'password_error' => 'Lỗi khi đổi mật khẩu: :error',
    
    // Tutor Approval
    'tutor_not_approved' => 'Gia sư này chưa được xác thực bởi admin. Vui lòng chọn gia sư khác.',
    'tutor_awaiting_approval' => 'Bạn cần được admin phê duyệt trước khi có thể gửi yêu cầu kết nối.',
    'tutor_profile_pending' => 'Hồ sơ của bạn đang chờ admin phê duyệt. Vui lòng hoàn thiện hồ sơ và chờ xác thực.',
    
    // Contact Unlock
    'connection_must_accepted' => 'Kết nối phải được chấp nhận trước khi mở khóa thông tin.',
    'contact_already_unlocked' => 'Thông tin liên hệ đã được mở khóa trước đó.',
    'contact_unlocked_success' => 'Đã mở khóa thông tin liên hệ thành công!',
    'contact_unlocked_dev_mode' => 'Đã mở khóa thông tin liên hệ thành công! (Chế độ phát triển - không thanh toán)',
    'unlock_error' => 'Lỗi khi mở khóa: :error',
    
    // Payment Development
    'payment_dev_mode' => 'Tính năng thanh toán đang được phát triển. Vui lòng liên hệ admin để bật chế độ dev.',
    
    // Matching Errors
    'cannot_accept_own_request' => 'Bạn không thể chấp nhận yêu cầu của chính mình.',
    'cannot_decline_own_request' => 'Bạn không thể từ chối yêu cầu của chính mình.',
    'can_only_cancel_own' => 'Bạn chỉ có thể hủy các yêu cầu của mình.',
    
    // Notification Messages
    'notification_marked_read' => 'Đã đánh dấu thông báo là đã đọc.',
    'all_notifications_read' => 'Đã đánh dấu tất cả thông báo là đã đọc.',
    
    // Admin Messages
    'settings_updated' => 'Cài đặt đã được cập nhật thành công!',
    'certificate_deleted' => 'Đã xóa chứng chỉ thành công!',
    'profile_update_success' => 'Cập nhật hồ sơ thành công!',
    
    // Student Request Messages
    'request_created_success' => 'Yêu cầu đã được tạo thành công!',
    'request_updated' => 'Yêu cầu đã được cập nhật.',
    'request_deleted' => 'Yêu cầu đã bị xóa.',
    'request_create_error' => 'Lỗi khi tạo yêu cầu.',
    
    // CV Parser Messages
    'cv_parsing_success' => 'Phân tích CV thành công!',
    'cv_parsing_failed' => 'Lỗi khi phân tích CV.',
    'cv_invalid_format' => 'Định dạng CV không hợp lệ.',
    'cv_upload_error' => 'Lỗi khi upload CV.',
    
    // AI Recommendation Messages
    'ai_loading' => 'AI đang tìm kiếm gia sư phù hợp nhất...',
    'ai_no_results' => 'Không tìm thấy gia sư phù hợp. Thử thay đổi yêu cầu.',
    'ai_error' => 'Lỗi khi tải gợi ý AI.',
    'ai_processing' => 'Đang xử lý bằng AI...',
    
    // Matching Service Messages
    'matching_success' => 'Ghép đôi thành công!',
    'matching_request_sent' => 'Yêu cầu kết nối đã được gửi.',
    'matching_accepted' => 'Đã chấp nhận yêu cầu kết nối.',
    'matching_declined' => 'Đã từ chối yêu cầu kết nối.',
    'matching_cancelled' => 'Đã hủy yêu cầu kết nối.',
    'no_suitable_match' => 'Chưa tìm thấy gia sư phù hợp.',
    
    // Location Messages
    'location_detected' => 'Đã phát hiện vị trí của bạn.',
    'location_error' => 'Không thể xác định vị trí.',
    'location_permission_denied' => 'Quyền truy cập vị trí bị từ chối.',
    
    // Auth Messages
    'login_success' => 'Đăng nhập thành công!',
    'logout_success' => 'Đăng xuất thành công!',
    'register_success' => 'Đăng ký thành công!',
    'email_verified' => 'Email đã được xác thực.',
    'verification_link_sent' => 'Link xác thực đã được gửi đến email của bạn.',
    'password_reset_sent' => 'Link đặt lại mật khẩu đã được gửi.',
    'password_reset_success' => 'Mật khẩu đã được đặt lại thành công.',
    
    // General Success Messages
    'action_success' => 'Thao tác thành công!',
    'save_success' => 'Đã lưu thành công!',
    'delete_success' => 'Đã xóa thành công!',
    'update_success' => 'Đã cập nhật thành công!',
    
    // General Error Messages
    'action_failed' => 'Thao tác thất bại.',
    'something_went_wrong' => 'Đã có lỗi xảy ra. Vui lòng thử lại.',
    'invalid_input' => 'Dữ liệu không hợp lệ.',
    'unauthorized' => 'Bạn không có quyền thực hiện thao tác này.',
    'not_found' => 'Không tìm thấy dữ liệu.',
];
