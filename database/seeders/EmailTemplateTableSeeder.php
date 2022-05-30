<?php

namespace Database\Seeders;

use App\Models\EmailTemplate;
use Illuminate\Database\Seeder;

class EmailTemplateTableSeeder extends Seeder
{
    public function checkIssetBeforeCreate($data)
    {
        $email = EmailTemplate::where('name', $data['name'])->first();
        if (empty($email)) {
            EmailTemplate::create($data);
        } else {
            $email->update($data);
        }
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Request
        self::checkIssetBeforeCreate([
            'title' => 'Email xác nhận cho khách hàng đã yêu cầu dịch/review thành công',
            'name' => 'email-request-translate-for-user',
            'subject' => '[Trans-Flash] Thông báo tiếp nhận yêu cầu xử lý tài liệu ngày {{NGAY}}',
            'group_code' => 'email-request-success',
            'content' => '<p>
                          <span style="color: rgb(34, 34, 34);">Xin chào&nbsp;</span>
                          <strong>{{TEN_KHACH_HANG}}</strong>
                          <span style="color: rgb(34, 34, 34);">.</span>
                        </p>
                        <br>
                        <p>
                          <span style="color: rgb(34, 34, 34);">Cảm ơn bạn đã tin tưởng và sử dụng dịch vụ dịch thuật/review/xử lý tài liệu văn bản của Trans-Flash. Chúng tôi rất hân hạnh được trợ giúp quý khách.</span>
                        </p>
                        <br>
                        <p>
                          <span style="color: rgb(34, 34, 34);"></span>
                          <strong style="color: rgb(34, 34, 34);">Trans-Flash thông báo đã xác nhận yêu cầu:</strong>
                        </p>
                        <br>
                        <p>
                          <u style="color: rgb(34, 34, 34);">Loại yêu cầu</u>
                          <span style="color: rgb(34, 34, 34);">: </span>
                          <em style="color: rgb(34, 34, 34);">{{LOAI}} văn bản</em>
                        </p>
                        <p>
                          <u style="color: rgb(34, 34, 34);">File cần xử lý:&nbsp;</u>
                          <strong>{{FILE}}</strong>
                        </p>
                        <p>
                          <u style="color: rgb(34, 34, 34);">Thời hạn mong muốn nhận kết quả:</u>
                          <span style="color: rgb(34, 34, 34);"> {{THOI_HAN_NHAN_KET_QUA}}</span>
                        </p>
                        <p>
                          <u style="color: rgb(34, 34, 34);">Yêu cầu chi tiết</u>
                          <strong style="color: rgb(34, 34, 34);">
                            <u>:</u>
                          </strong>
                        </p>
                        <p>
                          <em style="color: rgb(34, 34, 34);">“{{YEU_CAU_CHI_TIET}}”</em>
                        </p>
                        <p>
                          <span style="color: rgb(34, 34, 34);">&nbsp;</span>
                        </p>
                        <p>
                          <span style="color: rgb(34, 34, 34);">Bạn có thể gửi thêm hoặc điều chỉnh yêu cầu xử lý tài liệu: </span>
                          <strong style="color: rgb(0, 112, 192);">
                            <u>tại đây</u>
                          </strong>
                        </p>
                        <p>
                          <span style="color: rgb(34, 34, 34);">&nbsp;</span>
                        </p>
                        <p>
                          <strong style="color: rgb(34, 34, 34);">
                            <em>Chúng tôi sẽ sớm gửi lại báo giá chi tiết cho yêu cầu của bạn trong vòng 1 giờ làm việc </em>
                          </strong>
                        </p>
                        <p>
                          <strong style="color: rgb(34, 34, 34);">
                            <em>(trong thời gian hành chính). </em>
                          </strong>
                        </p>
                        <p>
                          <strong style="color: rgb(34, 34, 34);">&nbsp;</strong>
                        </p>
                        <p>
                          <em style="color: rgb(34, 34, 34);">Nếu có thêm yêu cầu hoặc cần trao đổi thêm, mong quý khách để lại số điện thoại qua mail trả lời hoặc quý khách có thể nhắn tin trực tiếp tới chúng tôi bằng messenger: </em>
                          <a href="http://m.me/transformflash.service" rel="noopener noreferrer" target="_blank" style="color: rgb(17, 85, 204);">m.me/transformflash.service</a>
                        </p>
                        <p>&nbsp;</p>
                        <p>
                          <strong style="color: rgb(34, 34, 34);">Trân trọng!</strong>
                        </p>
                        <br>'
        ]);
        self::checkIssetBeforeCreate([
            'title' => 'Email thông báo cho admin có khách hàng đã yêu cầu dịch/review',
            'name' => 'email-request-translate-for-admin',
            'subject' => '[Trans-Flash] Thông báo khách hàng {{TEN_KHACH_HANG}} đã yêu cầu {{LOAI}} tài liệu ngày {{NGAY}}',
            'group_code' => 'email-request-success',
            'content' => '<span>Trans-Flash Xin chào Quản trị viên.</span>
                            <br>
                            <span>TRANS-FLASH thông báo đã có khách hàng <b>{{TEN_KHACH_HANG}}</b>
                                gửi yêu cầu {{LOAI}} tài liệu.</span>
                            <br>
                            <span>File cần xử lý: {{FILE}}.</span>
                            <br>
                            <span>Hãy kiểm tra lại đơn hàng và báo giá sớm tới khách hàng.</span>
                            <br>
                            <span>TRANS-FLASH chúc bạn có một ngày làm việc hiệu quả.</span>
                            <br>
                            <span>Trân trọng!</span>
                            <br>'
        ]);

        // update
        self::checkIssetBeforeCreate([
            'title' => 'Email xác nhận khách hàng đã cập nhật yêu cầu dịch/review thành công',
            'name' => 'email-update-file-for-user',
            'subject' => '[Trans-Flash] Thông báo tiếp nhận cập nhật yêu cầu xử lý tài liệu ngày {{NGAY}}',
            'group_code' => 'email-update-success',
            'content' => '<p><span style="color: rgb(34, 34, 34);">Xin chào&nbsp;</span>
                  <strong>{{TEN_KHACH_HANG}}</strong>
                  <span style="color: rgb(34, 34, 34);">.</span>
                </p>
                <p>
                  <span style="color: rgb(34, 34, 34);">&nbsp;</span>
                </p>
                <p>
                  <span style="color: rgb(34, 34, 34);">Cảm ơn bạn đã tin tưởng và sử dụng dịch vụ dịch thuật/review/xử lý tài liệu văn bản của Trans-Flash. Chúng tôi rất hân hạnh được trợ giúp quý khách.</span>
                </p>
                <p>
                  <br>
                </p>
                <p>
                  <span style="color: rgb(34, 34, 34);"></span>
                  <strong style="color: rgb(34, 34, 34);">Trans-Flash xác nhận là đã nhận được cập nhật yêu cầu:</strong>
                </p>
                <p>
                  <span style="color: rgb(34, 34, 34);">&nbsp;</span>
                </p>
                <p>
                  <u style="color: rgb(34, 34, 34);">Loại yêu cầu</u>
                  <span style="color: rgb(34, 34, 34);">: </span>
                  <em style="color: rgb(34, 34, 34);">{{LOAI}} văn bản</em>
                </p>
                <p>
                  <u style="color: rgb(34, 34, 34);">File cần xử lý mới:&nbsp;</u>
                  <strong>{{FILE_MOI}}</strong>
                  <em style="color: rgb(34, 34, 34);">&nbsp;</em>
                </p>
                <p>
                  <u style="color: rgb(34, 34, 34);">Thời hạn mong muốn nhận kết quả:</u>
                  <span style="color: rgb(34, 34, 34);"> {{THOI_HAN_NHAN_KET_QUA}}</span>
                </p>
                <p>
                  <u style="color: rgb(34, 34, 34);">Yêu cầu chi tiết</u>
                  <strong style="color: rgb(34, 34, 34);">
                    <u>:</u>
                  </strong>
                </p>
                <p>
                  <em style="color: rgb(34, 34, 34);">“{{YEU_CAU_CHI_TIET}}”</em>
                </p>
                <p>
                  <span style="color: rgb(34, 34, 34);">&nbsp;</span>
                </p>
                <p>
                  <span style="color: rgb(34, 34, 34);">Bạn có thể gửi thêm hoặc điều chỉnh yêu cầu xử lý tài liệu: </span>
                  <strong style="color: rgb(0, 112, 192);">
                    <u>tại đây</u>
                  </strong>
                </p>
                <p>
                  <span style="color: rgb(34, 34, 34);">&nbsp;</span>
                </p>
                <p>
                  <strong style="color: rgb(34, 34, 34);">
                    <em>Chúng tôi sẽ sớm gửi lại báo giá chi tiết cho yêu cầu mới cập nhật của bạn trong vòng 1 giờ làm việc (trong thời gian hành chính). </em>
                  </strong>
                </p>
                <p>
                  <strong style="color: rgb(34, 34, 34);">&nbsp;</strong>
                </p>
                <p>
                  <strong style="color: rgb(34, 34, 34);">&nbsp;</strong>
                </p>
                <p>
                  <em style="color: rgb(34, 34, 34);">Nếu có thêm yêu cầu hoặc cần trao đổi thêm, mong quý khách để lại số điện thoại qua mail trả lời hoặc quý khách có thể nhắn tin trực tiếp tới chúng tôi bằng messenger: </em>
                  <a href="http://m.me/transformflash.service" rel="noopener noreferrer" target="_blank" style="color: rgb(17, 85, 204);">m.me/transformflash.service</a>
                </p>
                <p>
                  <br>
                </p>
                <p>
                  <strong style="color: rgb(34, 34, 34);"> Trân trọng!</strong>
                </p>
                <br>'
        ]);
        self::checkIssetBeforeCreate([
            'title' => 'Email thông báo cho admin khách hàng đã cập nhật yêu cầu dịch/review',
            'name' => 'email-update-file-for-admin',
            'subject' => '[Trans-Flash] Thông báo có khách hàng cập nhật yêu cầu xử lý tài liệu ngày {{NGAY}}',
            'group_code' => 'email-update-success',
            'content' => '<span>Trans-Flash Xin chào Quản trị viên.</span>
                        <br>
                        <span>
                            Khách hàng <b>{{TEN_KHACH_HANG}}</b> cập nhật yêu cầu xử lý tài liệu.
                        </span>
                        <br>
                        <span>
                            File tài liệu cũ: <b>{{FILE_CU}}</b>.
                        </span>
                        <br>
                        <span>
                            File tài liệu mới: <b>{{FILE_MOI}}</b>.
                        </span>
                        <br>
                        <span>
                            Hãy kiểm tra lại đơn hàng và cập nhật lại báo giá cho khách hàng.
                        </span>
                        <br>
                        <span>TRANS-FLASH chúc bạn có một ngày làm việc hiệu quả.</span>
                        <br>
                        <span>Trân trọng!</span>
                        <br>'
        ]);

        // payment info
        self::checkIssetBeforeCreate([
            'title' => 'Email thông tin thanh toán qua PayPal cho khách hàng',
            'name' => 'email-payment-with-PayPal-info',
            'subject' => '[Trans-Flash] gửi khách hàng thông tin thanh toán đơn hàng qua PayPal',
            'group_code' => 'email-payment-info',
            'content' => '<span>TRANS-FLASH Xin chào {{TEN_KHACH_HANG}}.</span>
                        <br>
                        <span>TRANS-FLASH chúc bạn có một ngày làm việc hiệu quả.</span>
                        <br>
                        <span>Trân trọng!</span>
                        <br>'
        ]);
        self::checkIssetBeforeCreate([
            'title' => 'Email thông tin thanh toán qua chuyển khoản cho khách hàng',
            'name' => 'email-payment-with-cash-info',
            'subject' => '[Trans-Flash] Thông báo thanh toán qua số tài khoản ngày {{NGAY}}',
            'group_code' => 'email-payment-info',
            'content' => '<p><span style="color: rgb(34, 34, 34);">Xin chào&nbsp;</span>
                              <strong>{{TEN_KHACH_HANG}}</strong>
                              <span style="color: rgb(34, 34, 34);">.</span>
                            </p>
                            <p><span style="color: rgb(34, 34, 34);">&nbsp;</span></p>
                            <p>
                              <span style="color: rgb(34, 34, 34);">Cảm ơn bạn đã tin tưởng và sử dụng dịch vụ dịch thuật/review/xử lý tài liệu văn bản của Trans-Flash. Chúng tôi rất hân hạnh được trợ giúp quý khách.</span>
                            </p>
                              <br>
                            <p>
                              <span style="color: rgb(34, 34, 34);"></span>
                              <strong style="color: rgb(34, 34, 34);">Trans-Flash thông báo đã xác nhận yêu cầu thanh toán cho yêu cầu: </strong>
                            </p>
                            <p>
                              <strong style="color: rgb(34, 34, 34);">&nbsp;</strong>
                            </p>
                            <p>
                              <strong style="color: rgb(34, 34, 34);">Mã đơn hàng: {{MA_DON_HANG}}</strong>
                            </p>
                            <p>
                              <strong style="color: rgb(34, 34, 34);">Chi tiết yêu cầu: </strong>
                              <strong style="color: rgb(0, 112, 192);">
                                <u>xem tại đây</u>
                              </strong>
                            </p>
                            <p>&nbsp;</p>
                            <p>
                              <strong style="color: rgb(34, 34, 34);">Để đảm bảo file được xử lý kịp thời, mời quý khách thanh toán phí yêu cầu theo thông tin như sau:</strong>
                            </p>
                            <p>
                              <strong style="color: rgb(34, 34, 34);">&nbsp;</strong>
                            </p>
                            <p>
                              <strong style="color: rgb(34, 34, 34);">
                                <em>Số tài khoản: 9.6968.2021</em>
                              </strong>
                            </p>
                            <p>
                              <strong style="color: rgb(34, 34, 34);">
                                <em>Chủ Tài Khoản: Công ty cổ phần V-Kookmin</em>
                              </strong>
                            </p>
                            <p>
                              <strong style="color: rgb(34, 34, 34);">
                                <em>Cú pháp chuyển khoản: ID_USER&nbsp;</em>
                              </strong>
                            </p>
                            <p><span style="color: rgb(34, 34, 34);">&nbsp;</span></p>
                            <p>
                              <span style="color: rgb(34, 34, 34);">Sau khi chuyển khoản mong quý khách gửi lại bill chuyển khoản của email này hoặc qua messenger của Transflash để xác nhận chuyển khoản. </span>
                              <a href="http://m.me/transformflash.service" rel="noopener noreferrer" target="_blank" style="color: rgb(17, 85, 204);">m.me/transformflash.service</a>.
                            </p>
                            <p>
                              <span style="color: rgb(34, 34, 34);">&nbsp;</span>
                            </p>
                            <p>
                              <span style="color: rgb(34, 34, 34);">Quý khách cho thể thanh toán bằng VNPay, MoMo, Paypal... theo hướng dẫn ở trang web theo link dưới dây:</span>
                            </p>
                            <p>
                              <span style="color: rgb(34, 34, 34);">[TBD hyperlink]</span>
                            </p>
                            <p>
                              <em style="color: rgb(34, 34, 34);">&nbsp;</em>
                            </p>
                            <p>
                              <em style="color: rgb(34, 34, 34);">Nếu có thêm yêu cầu hoặc cần trao đổi thêm, mong quý khách để lại số điện thoại qua mail trả lời hoặc quý khách có thể nhắn tin trực tiếp tới chúng tôi bằng messenger: </em>
                              <a href="http://m.me/transformflash.service" rel="noopener noreferrer" target="_blank" style="color: rgb(17, 85, 204);">m.me/transformflash.service</a>
                            </p>
                            <p>&nbsp;</p>
                            <p>
                              <strong style="color: rgb(34, 34, 34);">Trân trọng!</strong>
                            </p>
                        <br>'
        ]);

        //  Admin update order
        self::checkIssetBeforeCreate([
            'title' => 'Email thông báo cho khách hàng quản trị viên đã cập nhật thông tin báo giá của đơn hàng',
            'name' => 'email-admin-update-order-info',
            'subject' => '[Trans-Flash] Báo giá yêu cầu xử lý tài liệu ngày {{NGAY}}',
            'group_code' => 'admin-update-order',
            'content' => '<p>
                      <span style="color: rgb(34, 34, 34);">Xin chào </span>
                      <strong style="color: rgb(96, 98, 102); background-color: rgb(245, 247, 250);">{{TEN_KHACH_HANG}}</strong>
                      <span style="color: rgb(34, 34, 34);">,</span>
                    </p>
                    <p>
                      <span style="color: rgb(34, 34, 34);">&nbsp;</span>
                    </p>
                    <p>
                      <span style="color: rgb(34, 34, 34);">Trans-flash đã cập nhật báo giá cho yêu cầu [loại] của quý khách như sau: </span>
                    </p>
                    <p>
                      <span style="color: rgb(34, 34, 34);">&nbsp;</span>
                    </p>
                    <p>
                      <u style="color: rgb(34, 34, 34);">Loại yêu cầu</u>
                      <span style="color: rgb(34, 34, 34);">: {{LOAI}}</span>
                      <em style="color: rgb(34, 34, 34);"> văn bản&nbsp;</em>
                    </p>
                    <p>
                      <u style="color: rgb(34, 34, 34);">File cần xử lý:&nbsp;</u>
                      <strong style="color: rgb(34, 34, 34);">
                        <em>
                          <u>{{FILE}}</u>
                        </em>
                      </strong>
                    </p>
                    <p>
                      <u>Thời hạn mong muốn nhận kết quả:</u> {{THOI_HAN_NHAN_KET_QUA}}
                    </p>
                    <p>
                      <span style="color: rgb(34, 34, 34);">&nbsp;</span>
                    </p>
                    <p>
                      <strong style="color: rgb(34, 34, 34);">
                        <u>Kết quả báo giá:</u>
                      </strong>
                    </p>
                    <p>
                      <span style="color: rgb(34, 34, 34);">Số trang:&nbsp;{{SO_TRANG}} trang</span>
                    </p>
                    <p>
                      <span style="color: rgb(34, 34, 34);">Đơn giá:&nbsp;&nbsp;{{DON_GIA}} VND</span>
                    </p>
                    <p>
                      <span style="color: rgb(34, 34, 34);">Thành tiền: {{TONG_THANH_TIEN}} VND</span>
                    </p>
                    <p>
                      <span style="color: rgb(34, 34, 34);">&nbsp;</span>
                    </p>
                    <p>
                      <span style="color: rgb(34, 34, 34);">Bạn có thể gửi thêm hoặc điều chỉnh yêu cầu xử lý tài liệu: </span>
                      <strong style="color: rgb(0, 112, 192);">
                        <u>tại đây</u>
                      </strong>
                    </p>
                    <p>
                      <span style="color: rgb(34, 34, 34);">&nbsp;</span>
                    </p>
                    <p>
                      <strong style="color: rgb(34, 34, 34);">Để đảm bảo file được dịch kịp thời, mời quý khách thanh toán phí yêu cầu theo thông tin như sau:</strong>
                    </p>
                    <p>
                      <strong style="color: rgb(34, 34, 34);">
                        <em>Số tài khoản: 9.6968.2021</em>
                      </strong>
                    </p>
                    <p>
                      <strong style="color: rgb(34, 34, 34);">
                        <em>Chủ Tài Khoản: Công ty cổ phần V-Kookmin</em>
                      </strong>
                    </p>
                    <p>
                      <strong style="color: rgb(34, 34, 34);">
                        <em>Cú pháp chuyển khoản: ID_USER </em>
                      </strong>
                    </p>
                    <p>
                      <span style="color: rgb(34, 34, 34);">&nbsp;</span>
                    </p>
                    <p>
                      <span style="color: rgb(34, 34, 34);">Sau khi chuyển khoản mong quý khách gửi lại bill chuyển khoản của email này hoặc qua messenger của Transflash để xác nhận chuyển khoản. </span>
                      <a href="http://m.me/transformflash.service" rel="noopener noreferrer" target="_blank" style="color: rgb(17, 85, 204);">m.me/transformflash.service</a>
                    </p>
                    <p>
                      <span style="color: rgb(34, 34, 34);">&nbsp;</span>
                    </p>
                    <p>
                      <span style="color: rgb(34, 34, 34);">Quý khách cho thể thanh toán bằng VNPay, MoMo, Paypal... theo hướng dẫn ở trang web theo link dưới dây:</span>
                    </p>
                    <p>
                      <span style="color: rgb(34, 34, 34);">[TBD hyperlink]</span>
                    </p>
                    <p>
                      <em style="color: rgb(34, 34, 34);">&nbsp;</em>
                    </p>
                    <p>
                      <em style="color: rgb(34, 34, 34);">Nếu có thêm yêu cầu hoặc cần trao đổi thêm, mong quý khách để lại số điện thoại qua mail trả lời hoặc quý khách có thể nhắn tin trực tiếp tới chúng tôi bằng messenger: </em>
                      <a href="http://m.me/transformflash.service" rel="noopener noreferrer" target="_blank" style="color: rgb(17, 85, 204);">m.me/transformflash.service</a>
                    </p>
                    <p>
                      <span style="color: rgb(34, 34, 34);">&nbsp;</span>
                    </p>
                    <p>
                      <span style="color: rgb(34, 34, 34);">&nbsp;</span>
                    </p>
                    <p>
                      <span style="color: rgb(34, 34, 34);">&nbsp;</span>
                    </p>
                    <p>
                      <strong style="color: rgb(34, 34, 34);">Trân trọng!</strong>
                    </p>
                        <br>'
        ]);
        self::checkIssetBeforeCreate([
            'title' => 'Email thông báo cho khách hàng xác nhận thanh toán đơn hàng thành công',
            'name' => 'email-admin-update-confirm-payment',
            'subject' => '[Trans-Flash] Xác nhận thanh toán đơn hàng',
            'group_code' => 'admin-update-order',
            'content' => '<p>
                      <span style="color: rgb(34, 34, 34);">Xin chào&nbsp;</span>
                      <strong style="color: rgb(96, 98, 102); background-color: rgb(245, 247, 250);">{{TEN_KHACH_HANG}}</strong>
                      <span style="color: rgb(34, 34, 34);">.</span>
                    </p>
                    <p>
                      <span style="color: rgb(34, 34, 34);">&nbsp;</span>
                    </p>
                    <p>
                      <span style="color: rgb(34, 34, 34);">Cảm ơn bạn đã tin tưởng và sử dụng dịch vụ dịch thuật/review/xử lý tài liệu văn bản của Trans-Flash. Chúng tôi rất hân hạnh được trợ giúp quý khách.</span>
                    </p>
                    <p>
                      <br>
                    </p>
                    <p>
                      <span style="color: rgb(34, 34, 34);"></span>
                      <strong style="color: rgb(34, 34, 34);">Trans-Flash xác nhận bạn đã </strong>
                      <strong style="color: rgb(192, 0, 0);">hoàn thành thanh toán </strong>
                      <strong style="color: rgb(34, 34, 34);">cho yêu cầu: </strong>
                    </p>
                    <p>
                      <strong style="color: rgb(34, 34, 34);">&nbsp;</strong>
                    </p>
                    <p>
                      <strong style="color: rgb(34, 34, 34);">Mã đơn hàng: {{MA_DON_HANG}}</strong>
                    </p>
                    <p>
                      <strong style="color: rgb(34, 34, 34);">Chi tiết yêu cầu: </strong>
                      <strong style="color: rgb(0, 112, 192);">
                        <u>xem tại đây</u>
                      </strong>
                    </p>
                    <p>
                      <span style="color: rgb(34, 34, 34);">&nbsp;</span>
                    </p>
                    <p>
                      <span style="color: rgb(34, 34, 34);">Nếu cần xuất hóa đơn thanh toán, quý khách có thể yêu cầu hóa đơn qua email này hoặc qua messenger của Transflash tại: </span>
                      <a href="http://m.me/transformflash.service" rel="noopener noreferrer" target="_blank" style="color: rgb(17, 85, 204);">m.me/transformflash.service</a>.
                    </p>
                    <p>&nbsp;</p>
                    <p>Bạn có thể theo dõi tình trạng xử lý tài liệu của mình tại: <u style="color: rgb(68, 68, 68);">Tình trạng đơn hàng</u>
                    </p>
                    <p>
                      <span style="color: rgb(34, 34, 34);">&nbsp;</span>
                    </p>
                    <p>
                      <em style="color: rgb(34, 34, 34);">Nếu có thêm yêu cầu hoặc cần trao đổi thêm, mong quý khách để lại số điện thoại qua mail trả lời hoặc quý khách có thể nhắn tin trực tiếp tới chúng tôi bằng messenger: </em>
                      <a href="http://m.me/transformflash.service" rel="noopener noreferrer" target="_blank" style="color: rgb(17, 85, 204);">m.me/transformflash.service</a>
                    </p>
                    <p>
                      <span style="color: rgb(34, 34, 34);">&nbsp;</span>
                    </p>
                    <p>
                      <strong style="color: rgb(34, 34, 34);">Trân trọng!</strong>
                    </p>
                    <br>'
        ]);
        self::checkIssetBeforeCreate([
            'title' => 'Email thông báo cho khách hàng nhận kết quả xử lý tài liệu',
            'name' => 'email-admin-update-order-result',
            'subject' => '[Trans-Flash] Thông báo nhận kết quả xử lý tài liệu ',
            'group_code' => 'admin-update-order',
            'content' => '<p>
                          <span style="color: rgb(34, 34, 34);">Xin chào&nbsp;</span>
                          <strong>{{TEN_KHACH_HANG}}</strong>
                          <span style="color: rgb(34, 34, 34);">.</span>
                        </p>
                        <p>
                          <span style="color: rgb(34, 34, 34);">&nbsp;</span>
                        </p>
                        <p>
                          <span style="color: rgb(34, 34, 34);">Cảm ơn bạn đã tin tưởng và sử dụng dịch vụ dịch thuật/review/xử lý tài liệu văn bản của Trans-Flash. Chúng tôi rất hân hạnh được trợ giúp quý khách.</span>
                        </p>
                        <p>
                          <br>
                        </p>
                        <p>
                          <span style="color: rgb(34, 34, 34);"></span>
                          <strong style="color: rgb(34, 34, 34);">Trans-Flash đã </strong>
                          <strong style="color: rgb(192, 0, 0);">hoàn thành xử lý tài liệu </strong>
                          <strong style="color: rgb(34, 34, 34);">cho yêu cầu: </strong>
                        </p>
                        <p>
                          <strong style="color: rgb(34, 34, 34);">&nbsp;</strong>
                        </p>
                        <p>
                          <strong style="color: rgb(34, 34, 34);">Mã đơn hàng: {{MA_DON_HANG}}</strong>
                        </p>
                        <p>
                          <strong style="color: rgb(34, 34, 34);">Chi tiết yêu cầu: </strong>
                          <strong style="color: rgb(0, 112, 192);">
                            <u>xem tại đây</u>
                          </strong>
                        </p>
                        <p>
                          <strong style="color: rgb(34, 34, 34);">&nbsp;</strong>
                        </p>
                        <p>
                          <strong style="color: rgb(34, 34, 34);">Quý khách có thể tra cứu kết quả dịch: </strong>
                          <strong style="color: rgb(0, 112, 192);">
                            <u>tại đây</u>
                          </strong>
                        </p>
                        <p>
                          <span style="color: rgb(34, 34, 34);">&nbsp;</span>
                        </p>
                        <p>&nbsp;</p>
                        <p>
                          <em style="color: rgb(34, 34, 34);">Nếu có thêm yêu cầu xem xét lại kết quả xử lý hoặc cần trao đổi thêm, mong quý khách để lại số điện thoại qua mail trả lời hoặc quý khách có thể nhắn tin trực tiếp tới chúng tôi bằng messenger: </em>
                          <a href="http://m.me/transformflash.service" rel="noopener noreferrer" target="_blank" style="color: rgb(17, 85, 204);">m.me/transformflash.service</a>
                        </p>
                        <p>&nbsp;</p>
                        <p>
                          <strong style="color: rgb(34, 34, 34);">Trân trọng!</strong>
                        </p>
                        <br>'
        ]);

        // respone
        self::checkIssetBeforeCreate([
            'title' => 'Email phản hồi yêu cầu của khách hàng',
            'name' => 'email-respone-customer',
            'subject' => '[Trans-Flash] phản hồi yêu cầu của bạn',
            'group_code' => 'email-respone-customer',
            'content' => '<p>Xin chào <strong>{{TEN_KHACH_HANG}}</strong>. </p>
                        <br>
                        <p>Cảm ơn quý khách đã góp ý tới dịch vụ xử lý tài liệu Trans-flash:</p>
                        <p>Chúng tôi xin phản hồi góp ý của quý khách với nội dung sau:</p>
                        <p><br></p>
                        <p>{{NOI_DUNG_PHAN_HOI}}</p>
                        <br>
                        <br>
                        <br>
                        <p>Nếu chưa hài lòng với nội dung phản hồi hoặc có thêm góp ý, mong quý khách trả lời lại email này hoặc nhắn tin trực tiếp tới bộ phận chăm sóc khách hàng của chúng tôi <span style="color: rgb(34, 34, 34);">bằng messenger</span>
                          <em style="color: rgb(34, 34, 34);">: </em>
                          <a href="http://m.me/transformflash.service" rel="noopener noreferrer" target="_blank" style="color: rgb(17, 85, 204);">m.me/transformflash.service</a>
                        </p>
                        <br>
                        <p>Trân trọng!</p>
                        <br>'
        ]);

        // verify
        self::checkIssetBeforeCreate([
            'title' => 'Email thông tin xác thực email của khách hàng',
            'name' => 'email-verify',
            'subject' => '[Trans-Flash] Thông báo mã xác thực email {{EMAIL}} ngày {{NGAY}}',
            'group_code' => 'email-verify',
            'content' => '<p>Xin chào quý khách,</p>
                        <br>
                        <p>Chào mừng quý khách tin dùng dịch vụ xử lý tài liệu của Trans-flash (văn phòng dịch vụ của công ty cổ phần V-Kookmin).</p>
                        <br>
                        <p>Để xác nhận email quý khách sử dụng là đúng: {{EMAIL}}.</p>
                        <p>Mong quý khách nhập: <strong>{{MA_XAC_THUC}}</strong> (Mã xác nhận sẽ hết hạn sau 1 ngày) </p>
                        <br>
                        <p><strong>
                            <u>Lưu ý:</u>
                          </strong></p>
                        <br>
                        <ul>
                          <li>Quý khác tuyệt đối Không cung cấp mã xác thực cho bất cứ ai vì bất cứ lý do nào.</li>
                          <li>Trong trường hợp quý khách không sử dụng dịch vụ và email này không phải của quý khách mong quý khách bỏ qua nội dung mail và kiểm tra lại sự bảo mật của tài khoản email của quý khách.</li>
                        </ul>
                        <br>
                        <p>Trân trọng!</p>
                        <br>'
        ]);

    }
}
