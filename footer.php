<?php
/**
 * フッターテンプレート（CTAスタイル）
 */
$footer = riff_get_settings('footer');

// デフォルト値
$defaults = array(
    'background_color' => '#4A7CC7',
    'main_text' => 'LINEならお写真を送るだけ！最短15分でお見積もりします',
    'business_hours' => '受付時間 8:00 〜 24:00',
    'phone' => '0123-45-6789',
    'phone_subtext' => 'お急ぎの方はお電話でご相談ください',
    'line_button_text' => 'LINEで無料見積',
    'line_button_url' => '#',
    'line_button_color' => '#06C755',
    'email_button_text' => 'メールで無料見積',
    'email_button_url' => '#',
    'email_button_bg_color' => '#FFFFFF',
    'email_button_text_color' => '#4A7CC7',
    'notice_text' => '※3営業日以内に返信いたします',
    'privacy_text' => 'プライバシーポリシー',
    'privacy_url' => '#',
    'copyright' => '© 2025 demo7',
);

$footer = array_merge($defaults, $footer);

// 電話番号をクリーン化（ハイフンを削除）
$clean_phone = str_replace(array('-', ' ', '(', ')'), '', $footer['phone']);
?>

<footer class="cta-footer" style="background-color: <?php echo esc_attr($footer['background_color']); ?>;">
    <div class="cta-footer-container">
        <!-- メインテキスト -->
        <div class="cta-main-text">
            <h2><?php echo riff_kses_br($footer['main_text']); ?></h2>
        </div>
        
        <!-- コンテンツエリア -->
        <div class="cta-content">
            <!-- 左側：電話情報 -->
            <div class="cta-contact-info">
                <div class="cta-business-hours"><?php echo riff_kses_br($footer['business_hours']); ?></div>
                <a href="tel:<?php echo esc_attr($clean_phone); ?>" class="cta-phone-number">
                    <i class="fas fa-phone-alt"></i>
                    <span><?php echo esc_html($footer['phone']); ?></span>
                </a>
                <div class="cta-phone-subtext"><?php echo riff_kses_br($footer['phone_subtext']); ?></div>

                <?php
                $footer_sns     = isset($footer['sns_links']) ? $footer['sns_links'] : array();
                $sns_platforms  = riff_get_sns_platforms();
                $has_footer_sns = false;
                foreach ($sns_platforms as $k => $p) {
                    if (!empty($footer_sns[$k]['enabled'])) {
                        $has_footer_sns = true;
                        break;
                    }
                }
                if ($has_footer_sns) : ?>
                    <div class="footer-sns-links">
                        <?php foreach ($sns_platforms as $k => $p) :
                            if (empty($footer_sns[$k]['enabled'])) continue;
                        ?>
                            <a href="<?php echo esc_url($footer_sns[$k]['url']); ?>"
                               class="footer-sns-link sns-<?php echo esc_attr($k); ?>"
                               target="_blank" rel="noopener noreferrer"
                               aria-label="<?php echo esc_attr($p['label']); ?>">
                                <i class="<?php echo esc_attr($p['icon']); ?>"></i>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- 右側：ボタンエリア -->
            <div class="cta-buttons-area">
                <div class="cta-buttons">
                    <!-- LINEボタン -->
                    <a href="<?php echo esc_url($footer['line_button_url']); ?>" 
                       class="cta-button cta-button-line"
                       style="background-color: <?php echo esc_attr($footer['line_button_color']); ?>;"
                       target="_blank" rel="noopener noreferrer">
                        <i class="fab fa-line"></i>
                        <span><?php echo riff_kses_br($footer['line_button_text']); ?></span>
                    </a>
                    
                    <!-- メールボタン -->
                    <a href="<?php echo esc_url($footer['email_button_url']); ?>" 
                       class="cta-button cta-button-email"
                       style="background-color: <?php echo esc_attr($footer['email_button_bg_color']); ?>; color: <?php echo esc_attr($footer['email_button_text_color']); ?>;">
                        <i class="fas fa-envelope"></i>
                        <span><?php echo riff_kses_br($footer['email_button_text']); ?></span>
                    </a>
                </div>
                
                <!-- 注意書き -->
                <?php if (!empty($footer['notice_text'])) : ?>
                    <div class="cta-notice"><?php echo riff_kses_br($footer['notice_text']); ?></div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- フッター下部 -->
        <div class="cta-footer-bottom">
            <?php if (!empty($footer['privacy_text']) && !empty($footer['privacy_url'])) : ?>
                <a href="<?php echo esc_url($footer['privacy_url']); ?>" class="cta-privacy-link">
                    <?php echo esc_html($footer['privacy_text']); ?>
                </a>
            <?php endif; ?>
            
            <div class="cta-copyright">
                <?php echo riff_kses_br($footer['copyright']); ?>
            </div>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>