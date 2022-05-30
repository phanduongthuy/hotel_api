<?php

namespace Database\Seeders;

use App\Models\NotificationPopup;
use Illuminate\Database\Seeder;

class NotificationPopupTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Our Services
        self::checkIssetBeforeCreate([
            'name'          => 'Chọn ngôn ngữ gốc',
            'code'          => 'native-language',
            'group_code'    => 'our-services',
            'description'   => 'Thông báo khách hàng chưa chọn ngôn ngữ gốc',
            'content'       => '<p class="ql-align-center">
                                <span style="   color: rgb(230, 0, 0);" class="ql-size-large">
                                    Vui lòng chọn ngôn ngữ file gốc
                                </span>
                                </p>'
        ]);

        self::checkIssetBeforeCreate([
            'name'          => 'Chọn ngôn ngữ dịch',
            'code'          => 'translate-language',
            'group_code'    => 'our-services',
            'description'   => 'Thông báo khách hàng chưa chọn ngôn ngữ gốc cần dịch/review',
            'content'       => '<p class="ql-align-center"><span style="   color: rgb(230, 0, 0);" class="ql-size-large">
                                Vui lòng chọn ngôn ngữ file cần dịch</span></p>'
        ]);

        self::checkIssetBeforeCreate([
            'name'          => 'Tải lên file',
            'code'          => 'upload-file',
            'group_code'    => 'our-services',
            'description'   => 'Thông báo yêu cầu khách hàng tải lên file cần dịch/review',
            'content'       => '<p class="ql-align-center"><span class="ql-size-large" style="color: rgb(230, 0, 0);">
                            Vui lòng tải lên file cần dịch/review</span></p>'
        ]);

        self::checkIssetBeforeCreate([
            'name'          => 'Thời gian nhận bản dịch',
            'code'          => 'receiving-time',
            'group_code'    => 'our-services',
            'description'   => 'Thông báo yêu cầu khách hàng chọn thời gian nhận bản dịch',
            'content'       => '<p class="ql-align-center"><span style="color: rgb(230, 0, 0);" class="ql-size-large">
                            Vui lòng chọn ngày mong muốn nhận bản dịch</span></p>'
        ]);

        self::checkIssetBeforeCreate([
            'name'          => 'Điều khoản dịch vụ',
            'code'          => 'terms-of-service',
            'group_code'    => 'our-services',
            'description'   => 'Thông báo yêu cầu khách hàng đọc và đồng ý với điều khoản dịch vụ',
            'content'       => '<p class="ql-align-center">
                                <span style="color: rgb(230, 0, 0);" class="ql-size-large">
                                Vui lòng đọc và đồng ý điều khoản</span></p>'
        ]);

        self::checkIssetBeforeCreate([
            'name'          => 'Nhập email',
            'code'          => 'email-required',
            'group_code'    => 'our-services',
            'description'   => 'Thông báo yêu cầu khách hàng nhập địa chỉ email',
            'content'       => '<p class="ql-align-center"><span style="   color: rgb(230, 0, 0);" class="ql-size-large">Vui lòng nhập email</span></p>'
        ]);

        self::checkIssetBeforeCreate([
            'name'          => 'Email không hợp lệ',
            'code'          => 'invalid-email',
            'group_code'    => 'our-services',
            'description'   => 'Thông báo khi khách hàng nhập một email không hợp lệ',
            'content'       => '<p class="ql-align-center"><span style="   color: rgb(230, 0, 0);" class="ql-size-large">Email không hơp lệ</span></p>'
        ]);

        self::checkIssetBeforeCreate([
            'name'          => 'Gửi mã xác thực thất bại',
            'code'          => 'send-verification-code-failed',
            'group_code'    => 'our-services',
            'description'   => 'Thông báo khi gửi mã xác thực qua email của khách hàng thất bại',
            'content'       => '<p class="ql-align-center"><span style="   color: rgb(230, 0, 0);" class="ql-size-large">Có lỗi xảy ra, vui lòng thử lại</span></p>'
        ]);

        self::checkIssetBeforeCreate([
            'name'          => 'Gửi mã xác thực thành công',
            'code'          => 'verification-code-sent-success',
            'group_code'    => 'our-services',
            'description'   => 'Thông báo khi gửi mã xác thực qua email của khách hàng thành công',
            'content'       => '<p class="ql-align-center"><span style="color: rgb(0, 71, 178);" class="ql-size-large">
                                Mã xác thực đã được gửi tới email của bạn, vui lòng kiểm tra email và nhập mã xác thực!</span></p>'
        ]);

        self::checkIssetBeforeCreate([
            'name'          => 'Nhập mã xác thực',
            'code'          => 'code-required',
            'group_code'    => 'our-services',
            'description'   => 'Thông báo yêu cầu khách hàng nhập mã xác thực',
            'content'       => '<p class="ql-align-center"><span style="color: rgb(230, 0, 0);" class="ql-size-large">
                            Vui lòng nhập mã xác thực</span></p>'
        ]);

        self::checkIssetBeforeCreate([
            'name'          => 'Xác thực thất bại',
            'code'          => 'authentication-failed',
            'group_code'    => 'our-services',
            'description'   => 'Thông báo khi khách hàng nhập sai mã xác thực hoặc mã xác thực đã hết hạn',
            'content'       => '<p class="ql-align-center"><span style="   color: rgb(230, 0, 0);" class="ql-size-large">
                            Mã xác thực sai hoặc hết hạn</span></p>'
        ]);

        self::checkIssetBeforeCreate([
            'name'          => 'Xác thực thành công',
            'code'          => 'authentication-success',
            'group_code'    => 'our-services',
            'description'   => 'Thông báo khi khách hàng xác thực thành công',
            'content'       => '<p class="ql-align-center"><span style="color: rgb(0, 71, 178);" class="ql-size-large">
                        Xác thực thành công, vui lòng nhập mật khẩu để tiếp tục</span></p>'
        ]);

        self::checkIssetBeforeCreate([
            'name'          => 'Nhập họ và tên',
            'code'          => 'name-required',
            'group_code'    => 'our-services',
            'description'   => 'Thông báo yêu cầu khách hàng nhập họ và tên',
            'content'       => '<p class="ql-align-center"><span style="   color: rgb(230, 0, 0);" class="ql-size-large">
                            Vui lòng nhập họ và tên</span></p>'
        ]);

        self::checkIssetBeforeCreate([
            'name'          => 'Nhập số điện thoại',
            'code'          => 'phone-required',
            'group_code'    => 'our-services',
            'description'   => 'Thông báo yêu cầu khách hàng nhập số điện thoại',
            'content'       => '<p class="ql-align-center"><span style="   color: rgb(230, 0, 0);" class="ql-size-large">
                            Vui lòng nhập số điện thoại</span></p>'
        ]);

        self::checkIssetBeforeCreate([
            'name'          => 'Số điện thoại sai định dạng',
            'code'          => 'invalid-phone-number',
            'group_code'    => 'our-services',
            'description'   => 'Thông báo khi khách hàng nhập nhập số điện thoại không hợp lệ',
            'content'       => '<p class="ql-align-center"><span style="   color: rgb(230, 0, 0);" class="ql-size-large">
                            Số điện thoại không đúng định dạng</span></p>'
        ]);

        self::checkIssetBeforeCreate([
            'name'          => 'Nhập mật khẩu',
            'code'          => 'password-required',
            'group_code'    => 'our-services',
            'description'   => 'Thông báo yêu cầu khách hàng nhập mật khẩu',
            'content'       => '<p class="ql-align-center"><span style="color: rgb(230, 0, 0);" class="ql-size-large">
                            Vui lòng nhập mật khẩu</span></p>'
        ]);

        self::checkIssetBeforeCreate([
            'name'          => 'Nhập mật khẩu xác nhận',
            'code'          => 'confirm-password-required',
            'group_code'    => 'our-services',
            'description'   => 'Thông báo yêu cầu khách hàng nhập mật khẩu xác nhận',
            'content'       => '<p class="ql-align-center"><span style="color: rgb(230, 0, 0);" class="ql-size-large">
                            Vui lòng xác nhận mật khẩu</span></p>'
        ]);

        self::checkIssetBeforeCreate([
            'name'          => 'Xác nhận mật khẩu thất bại',
            'code'          => 'confirm-password-failed',
            'group_code'    => 'our-services',
            'description'   => 'Thông báo khi khách hàng nhập mật khẩu và mật khẩu xác nhận không giống nhau',
            'content'       => '<p class="ql-align-center"><span style="color: rgb(230, 0, 0);" class="ql-size-large">
                            Mật khẩu không khớp</span></p>'
        ]);

        self::checkIssetBeforeCreate([
            'name'          => 'Gửi yêu cầu thất bại',
            'code'          => 'submit-request-failed',
            'group_code'    => 'our-services',
            'description'   => 'Thông báo khi khách hàng gửi yêu cầu dịch/review thất bại',
            'content'       => '<p class="ql-align-center"><span style="color: rgb(230, 0, 0);" class="ql-size-large">
                            Có lỗi xảy ra, vui lòng thử lại</span></p>'
        ]);

        self::checkIssetBeforeCreate([
            'name'          => 'Gửi yêu cầu thành công',
            'code'          => 'submit-request-success',
            'group_code'    => 'our-services',
            'description'   => 'Thông báo khi khách hàng gửi yêu cầu dịch/review thành công',
            'content'       => '<p class="ql-align-justify"><span class="ql-size-large" style="color: rgb(0, 71, 178);">
                                Bạn đã gửi yêu cầu thành công!
                                Cảm ơn bạn đã tin dùng dịch vụ của Chúng tôi!
                                Báo giá sẽ được gửi đến bạn trong 1h Bạn sẽ được chuyển về trong giây lát</span></p>'
        ]);

        self::checkIssetBeforeCreate([
            'name'          => 'Giới hạn độ lớn của file',
            'code'          => 'limit-size-upload',
            'group_code'    => 'our-services',
            'description'   => 'Thông báo khi file tải lên quá lớn',
            'content'       => '<p class="ql-align-center"><span style="   color: rgb(230, 0, 0);" class="ql-size-large">
                            Tài liệu tải lên không được vượt quá 48MB</span></p>'
        ]);

        self::checkIssetBeforeCreate([
            'name'          => 'Đăng nhập bằng Facebook thất bại',
            'code'          => 'login-with-facebook-fail',
            'group_code'    => 'our-services',
            'description'   => 'Thông báo khi khách hàng đăng nhập bằng Facebook thất bại',
            'content'       => '<p class="ql-align-center"><span style="   color: rgb(230, 0, 0);" class="ql-size-large">
                            Đăng nhập bằng Facebook thất bại</span></p>'
        ]);

        self::checkIssetBeforeCreate([
            'name'          => 'Đăng nhập bằng Google thất bại',
            'code'          => 'login-with-google-fail',
            'group_code'    => 'our-services',
            'description'   => 'Thông báo khi khách hàng đăng nhập bằng Google thất bại',
            'content'       => '<p class="ql-align-center"><span style="   color: rgb(230, 0, 0);" class="ql-size-large">
                            Đăng nhập bằng Google thất bại</span></p>'
        ]);

        // Cart
        self::checkIssetBeforeCreate([
            'name'          => 'Xóa file',
            'code'          => 'delete-file',
            'group_code'    => 'cart',
            'description'   => 'Thông báo khách hàng xác nhận xóa file',
            'content'       => '<p class="ql-align-center"><span style="color: rgb(0, 71, 178);" class="ql-size-large">Dữ liệu không thể phục hồi. Bạn có chắc chắn muốn xóa file này?</span></p>'
        ]);

        self::checkIssetBeforeCreate([
            'name'          => 'Xóa file thành công',
            'code'          => 'delete-file-success',
            'group_code'    => 'cart',
            'description'   => 'Thông báo khi khách hàng xóa file thành công',
            'content'       => '<p class="ql-align-center"><span style="color: rgb(0, 71, 178);" class="ql-size-large">Xóa file thành công</span></p>'
        ]);

        self::checkIssetBeforeCreate([
            'name'          => 'Xóa file thất bại',
            'code'          => 'delete-file-failed',
            'group_code'    => 'cart',
            'description'   => 'Thông báo khi khách hàng xóa file thất bại',
            'content'       => '<p class="ql-align-center"><span class="ql-size-large" style="   color: rgb(230, 0, 0);">Xóa file thất bại</span></p>'
        ]);

        self::checkIssetBeforeCreate([
            'name'          => 'Thay đổi file thành công',
            'code'          => 'update-file-success',
            'group_code'    => 'cart',
            'description'   => 'Thông báo khi khách hàng thay đổi file thành công',
            'content'       => '<p class="ql-align-center"><span class="ql-size-large" style="color: rgb(0, 71, 178);">Bạn đã cập nhật file thành công, chúng tôi sẽ kiểm tra lại file cập nhật và update lại báo giá trong vòng 1h(trong thời gian hành chính). Cảm ơn bạn!</span></p>'
        ]);

        self::checkIssetBeforeCreate([
            'name'          => 'Thay đổi file thất bại',
            'code'          => 'update-file-failed',
            'group_code'    => 'cart',
            'description'   => 'Thông báo khi khách hàng thay đổi file thất bại',
            'content'       => '<p class="ql-align-center"><span style="   color: rgb(230, 0, 0);" class="ql-size-large">Cập nhật file thất bại!</span></p>'
        ]);

        self::checkIssetBeforeCreate([
            'name'          => 'Yêu cầu xác thực email',
            'code'          => 'send-verify-code',
            'group_code'    => 'cart',
            'description'   => 'Thông báo mã xác thực đã được gửi tới email khách hàng (Chức năng quên mật khẩu khi chưa đăng nhập)',
            'content'       => '<p class="ql-align-center"><span class="ql-size-large" style="color: rgb(0, 71, 178);">Mã xác thực đã được gửi tới email của bạn, vui lòng kiểm tra email và nhập mã xác thực!</span></p>'
        ]);

        self::checkIssetBeforeCreate([
            'name'          => 'Xác thực email thành công',
            'code'          => 'verify-email-success',
            'group_code'    => 'cart',
            'description'   => 'Thông báo xác thực email thành công (Chức năng quên mật khẩu khi chưa đăng nhập)',
            'content'       => '<p class="ql-align-center"><span class="ql-size-large" style="color: rgb(0, 71, 178);">Xác thực thành công, vui lòng nhập mật khẩu để tiếp tục!</span></p>'
        ]);

        self::checkIssetBeforeCreate([
            'name'          => 'Xác thực email thành công',
            'code'          => 'verify-email-failed',
            'group_code'    => 'cart',
            'description'   => 'Thông báo xác thực email thất bại (Chức năng quên mật khẩu khi chưa đăng nhập)',
            'content'       => '<p class="ql-align-center"><span style="   color: rgb(230, 0, 0);" class="ql-size-large">Mã xác thực sai hoặc hết hạn</span></p>'
        ]);

        self::checkIssetBeforeCreate([
            'name'          => 'Chọn đơn hàng cần thanh toán',
            'code'          => 'order-required',
            'group_code'    => 'cart',
            'description'   => 'Thông báo yêu cầu khách hàng chọn đơn hàng để thanh toán',
            'content'       => '<p class="ql-align-center"><span class="ql-size-large" style="   color: rgb(230, 0, 0);">Vui lòng chọn ít nhất một đơn hàng để thanh toán</span></p>'
        ]);

        // Payment
        self::checkIssetBeforeCreate([
            'name'          => 'Thanh toán PayPal',
            'code'          => 'payment-paypal',
            'group_code'    => 'payment',
            'description'   => 'Thông báo khi khách hàng gửi yêu cầu thanh toán qua PayPal',
            'content'       => '<p class="ql-align-center"><span class="ql-size-large" style="color: rgb(0, 71, 178);">Bạn đã gửi cho quản trị viên yêu cầu thanh toán bằng PayPal</span></p>'
        ]);

        self::checkIssetBeforeCreate([
            'name'          => 'Thanh toán qua số tài khoản',
            'code'          => 'transfer',
            'group_code'    => 'payment',
            'description'   => 'Thông báo khi khách hàng gửi yêu cầu thanh toán qua hình thức chuyển khoản',
            'content'       => '<p class="ql-align-center"><span class="ql-size-large" style="color: rgb(0, 71, 178);">Bạn đã gửi cho quản trị viên yêu cầu thanh toán qua số tài khoản</span></p>'
        ]);

        self::checkIssetBeforeCreate([
            'name'          => 'Thanh toán đơn hàng đang chờ xử lý',
            'code'          => 'payment-of-pending-orders',
            'group_code'    => 'payment',
            'description'   => 'Thông báo khi khách hàng thanh toán đơn hàng đang chờ xử lý thanh toán',
            'content'       => '<p class="ql-align-center"><span class="ql-size-large" style="color: rgb(0, 71, 178);">Đơn hàng đang được chờ xử lý</span></p>'
        ]);

        self::checkIssetBeforeCreate([
            'name'          => 'Thanh toán thất bại',
            'code'          => 'payment-failed',
            'group_code'    => 'payment',
            'description'   => 'Thông báo khi khách hàng thanh toán thất bại',
            'content'       => '<p class="ql-align-center"><span class="ql-size-large" style="   color: rgb(230, 0, 0);">Thanh toán thất bại. Vui lòng thử lại sau</span></p>'
        ]);

        // Support
        self::checkIssetBeforeCreate([
            'name'          => 'Yêu cầu hỗ trợ',
            'code'          => 'support-request',
            'group_code'    => 'support',
            'description'   => 'Thông báo khi khách hàng gửi yêu cầu hỗ trợ thành công',
            'content'       => '<p class=\"ql-align-center\">
                                <span class=\"ql-size-large\" style=\"color: rgb(0, 71, 178);\">Bạn đã gửi yêu cầu thành công! </span></p>
                                <p class=\"ql-align-center\">
                                <span class=\"ql-size-large\" style=\"color: rgb(0, 71, 178);\">Chúng tôi sẽ phản hồi trong thời gian sớm nhất</span>
                                </p>'
        ]);
    }

    public function checkIssetBeforeCreate($data)
    {
        $notificationPopup = NotificationPopup::where('code', $data['code'])->first();
        if (empty($notificationPopup)) {
            NotificationPopup::create($data);
        } else {
            $notificationPopup->update($data);
        }
    }
}
