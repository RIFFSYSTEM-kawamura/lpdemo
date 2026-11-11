<?php
/**
 * Main Template File
 * 
 * このファイルはWordPressテーマの必須ファイルです。
 * 他の特定のテンプレートが見つからない場合のフォールバックとして機能します。
 * 
 * @package RIFF_Theme
 */

get_header();
?>

<main class="main-content" style="padding-top: 80px; min-height: 60vh;">
    <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 80px 30px;">
        
        <?php if (have_posts()) : ?>
            
            <div class="posts-wrapper">
                <?php
                while (have_posts()) :
                    the_post();
                ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class('post-item'); ?> style="margin-bottom: 60px; padding-bottom: 40px; border-bottom: 1px solid #e0e0e0;">
                        
                        <?php if (has_post_thumbnail()) : ?>
                            <div class="post-thumbnail" style="margin-bottom: 30px;">
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_post_thumbnail('large', array('style' => 'width: 100%; height: auto; border-radius: 8px;')); ?>
                                </a>
                            </div>
                        <?php endif; ?>
                        
                        <header class="entry-header" style="margin-bottom: 20px;">
                            <h2 class="entry-title" style="font-size: 32px; font-weight: 700; margin-bottom: 15px;">
                                <a href="<?php the_permalink(); ?>" style="color: #333; text-decoration: none;">
                                    <?php the_title(); ?>
                                </a>
                            </h2>
                            
                            <div class="entry-meta" style="color: #666; font-size: 14px;">
                                <span class="posted-on">
                                    <i class="far fa-calendar-alt"></i> 
                                    <?php echo get_the_date(); ?>
                                </span>
                                
                                <?php if (has_category()) : ?>
                                    <span style="margin: 0 10px;">|</span>
                                    <span class="cat-links">
                                        <i class="far fa-folder"></i> 
                                        <?php the_category(', '); ?>
                                    </span>
                                <?php endif; ?>
                                
                                <?php if (get_the_author()) : ?>
                                    <span style="margin: 0 10px;">|</span>
                                    <span class="author">
                                        <i class="far fa-user"></i> 
                                        <?php the_author(); ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </header>
                        
                        <div class="entry-content" style="line-height: 1.8; color: #555; margin-bottom: 20px;">
                            <?php
                            if (is_single()) {
                                the_content();
                            } else {
                                the_excerpt();
                            }
                            ?>
                        </div>
                        
                        <?php if (!is_single()) : ?>
                            <div class="entry-footer">
                                <a href="<?php the_permalink(); ?>" class="read-more" style="display: inline-block; padding: 12px 30px; background: #0066cc; color: #fff; border-radius: 25px; text-decoration: none; font-weight: 600; transition: all 0.3s ease;">
                                    続きを読む <i class="fas fa-arrow-right" style="margin-left: 5px;"></i>
                                </a>
                            </div>
                        <?php endif; ?>
                        
                    </article>
                <?php endwhile; ?>
            </div>
            
            <!-- ページネーション -->
            <div class="pagination" style="margin-top: 60px; text-align: center;">
                <?php
                the_posts_pagination(array(
                    'mid_size'  => 2,
                    'prev_text' => '<i class="fas fa-chevron-left"></i> 前へ',
                    'next_text' => '次へ <i class="fas fa-chevron-right"></i>',
                ));
                ?>
            </div>
            
        <?php else : ?>
            
            <!-- 投稿が見つからない場合 -->
            <div class="no-posts" style="text-align: center; padding: 80px 20px;">
                <h2 style="font-size: 32px; margin-bottom: 20px; color: #333;">
                    <?php _e('投稿が見つかりませんでした', 'riff-theme'); ?>
                </h2>
                <p style="color: #666; margin-bottom: 30px;">
                    <?php _e('申し訳ございませんが、該当する投稿がありません。', 'riff-theme'); ?>
                </p>
                <a href="<?php echo esc_url(home_url('/')); ?>" style="display: inline-block; padding: 12px 30px; background: #0066cc; color: #fff; border-radius: 25px; text-decoration: none; font-weight: 600;">
                    <i class="fas fa-home"></i> ホームに戻る
                </a>
            </div>
            
        <?php endif; ?>
        
    </div>
</main>

<?php
get_footer();
?>