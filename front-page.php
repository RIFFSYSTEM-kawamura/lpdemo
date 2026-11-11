<?php
/**
 * Front Page Template - シンプル版
 * 
 * @package RIFF_Theme
 */

get_header();

$hero = riff_get_settings('hero');
$slides = isset($hero['slides']) ? $hero['slides'] : array();
?>

<!-- ヒーローライダー -->
<section class="hero-section" id="hero-slider">
    
    <!-- 背景スライダー -->
    <div class="hero-slider-wrapper">
        <?php if (!empty($slides)) : ?>
            <?php foreach ($slides as $index => $slide) : ?>
                <div class="hero-slide <?php echo $index === 0 ? 'active' : ''; ?>">
                    <div class="hero-background" data-animation="<?php echo esc_attr($slide['image_animation']); ?>">
                        <?php if (!empty($slide['background'])) : ?>
                            <img src="<?php echo esc_url($slide['background']); ?>" alt="Hero Background">
                        <?php else : ?>
                            <div style="width: 100%; height: 100%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);"></div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else : ?>
            <!-- デフォルトスライド -->
            <div class="hero-slide active">
                <div class="hero-background" data-animation="fadeIn">
                    <div style="width: 100%; height: 100%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);"></div>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- コンテンツスライダー -->
    <div class="hero-content-wrapper">
        <?php if (!empty($slides)) : ?>
            <?php foreach ($slides as $index => $slide) : ?>
                <?php 
                $layout_class = isset($slide['layout']) && $slide['layout'] === 'reverse' ? 'layout-reverse' : 'layout-normal';
                $has_image_class = !empty($slide['content_image']) ? 'has-image' : 'no-image';
                ?>
                <div class="hero-content-grid <?php echo esc_attr($layout_class); ?> <?php echo esc_attr($has_image_class); ?> <?php echo $index === 0 ? 'active' : ''; ?>">
                    
                    <!-- テキストコンテンツ -->
                    <div class="hero-content hero-text-content <?php echo $index === 0 ? 'active' : ''; ?>" 
                         data-animation="<?php echo esc_attr($slide['text_animation']); ?>">
                        
                        <?php if (!empty($slide['subtitle'])) : ?>
                            <p class="hero-subtitle"><?php echo riff_kses_br($slide['subtitle']); ?></p>
                        <?php endif; ?>
                        
                        <?php if (!empty($slide['title'])) : ?>
                            <h1 class="hero-title"><?php echo wp_kses_post($slide['title']); ?></h1>
                        <?php endif; ?>
                        
                        <?php if (!empty($slide['description'])) : ?>
                            <p class="hero-description"><?php echo riff_kses_br($slide['description']); ?></p>
                        <?php endif; ?>
                        
                        <?php if (!empty($slide['button1_text']) || !empty($slide['button2_text'])) : ?>
                            <div class="hero-buttons">
                                <?php if (!empty($slide['button1_text'])) : ?>
                                    <a href="<?php echo esc_url($slide['button1_url']); ?>" class="hero-btn primary">
                                        <?php echo esc_html($slide['button1_text']); ?>
                                    </a>
                                <?php endif; ?>
                                
                                <?php if (!empty($slide['button2_text'])) : ?>
                                    <a href="<?php echo esc_url($slide['button2_url']); ?>" class="hero-btn secondary">
                                        <?php echo esc_html($slide['button2_text']); ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- 画像コンテンツ -->
                    <?php if (!empty($slide['content_image'])) : ?>
                        <div class="hero-content hero-image-content <?php echo $index === 0 ? 'active' : ''; ?>" 
                             data-animation="<?php echo esc_attr($slide['content_image_animation']); ?>">
                            <div class="hero-image-container">
                                <img src="<?php echo esc_url($slide['content_image']); ?>" alt="<?php echo esc_attr($slide['title']); ?>">
                            </div>
                        </div>
                    <?php endif; ?>
                    
                </div>
            <?php endforeach; ?>
        <?php else : ?>
            <!-- デフォルトコンテンツ -->
            <div class="hero-content-grid active">
                <div class="hero-content hero-text-content active" data-animation="fadeInLeft">
                    <p class="hero-subtitle">Welcome to Our Site</p>
                    <h1 class="hero-title">あなたのビジネスを<br class="pc">次のレベルへ</h1>
                    <p class="hero-description">プロフェッショナルなソリューションで、あなたのビジネスの成長をサポートします。</p>
                    <div class="hero-buttons">
                        <a href="#contact" class="hero-btn primary">今すぐ始める</a>
                        <a href="#about" class="hero-btn secondary">詳しく見る</a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- スクロールダウン -->
    <div class="scroll-down">
        <span></span>
    </div>
    
</section>

<!-- スライダー設定をJSに渡す -->
<script>
    var riffHeroSettings = {
        autoplay: <?php echo $hero['autoplay'] ? 'true' : 'false'; ?>,
        autoplaySpeed: <?php echo absint($hero['autoplay_speed']); ?>,
        transitionSpeed: <?php echo absint($hero['transition_speed']); ?>,
        slideCount: <?php echo count($slides); ?>
    };
</script>

<!-- メインコンテンツ(固定ページのコンテンツ) -->
<main class="main-content">
    <?php
    // 固定ページのコンテンツを取得
    while (have_posts()) :
        the_post();
        
        // 固定ページのコンテンツを出力
        if (get_the_content()) {
            echo '<div class="page-content-wrapper">';
            the_content();
            echo '</div>';
        }
    endwhile;
    ?>
</main>

<?php
get_footer();
?>