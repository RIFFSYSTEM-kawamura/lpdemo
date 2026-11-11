/**
 * FAQ Accordion Script
 * 汎用的なFAQアコーディオン機能
 * 
 * 使い方:
 * 1. HTML要素に適切なクラスを追加(.faq-item, .faq-question)
 * 2. このスクリプトを読み込む
 * 3. 自動的にアコーディオン機能が有効になります
 */

(function($) {
    'use strict';
    
    /**
     * FAQアコーディオンの初期化
     */
    function initFaqAccordion() {
        // FAQ質問部分のクリックイベント
        $('.faq-question').on('click', function() {
            var $faqItem = $(this).closest('.faq-item');
            var $allFaqItems = $('.faq-item');
            
            // 既に開いている場合は閉じる
            if ($faqItem.hasClass('active')) {
                $faqItem.removeClass('active');
                $faqItem.find('.faq-answer').slideUp(400);
            } else {
                // 他の開いているFAQを閉じる(オプション: 複数開きたい場合はこのブロックを削除)
                $allFaqItems.not($faqItem).removeClass('active');
                $allFaqItems.not($faqItem).find('.faq-answer').slideUp(400);
                
                // クリックされたFAQを開く
                $faqItem.addClass('active');
                $faqItem.find('.faq-answer').slideDown(400);
            }
        });
        
        // キーボードアクセシビリティ対応
        $('.faq-question').on('keydown', function(e) {
            // Enterキーまたはスペースキーで開閉
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                $(this).trigger('click');
            }
        });
        
        // tabindexを設定してキーボードナビゲーションを可能にする
        $('.faq-question').attr('tabindex', '0');
        
        // ARIA属性を設定してアクセシビリティを向上
        $('.faq-question').each(function(index) {
            var $question = $(this);
            var $answer = $question.siblings('.faq-answer');
            
            var questionId = 'faq-question-' + index;
            var answerId = 'faq-answer-' + index;
            
            $question.attr({
                'id': questionId,
                'aria-expanded': 'false',
                'aria-controls': answerId,
                'role': 'button'
            });
            
            $answer.attr({
                'id': answerId,
                'aria-labelledby': questionId,
                'role': 'region'
            });
        });
        
        // activeクラスが付いている項目のaria-expandedを更新
        $('.faq-item.active .faq-question').attr('aria-expanded', 'true');
    }
    
    /**
     * FAQ項目のaria-expanded属性を更新
     */
    function updateAriaExpanded() {
        $('.faq-item').each(function() {
            var $item = $(this);
            var $question = $item.find('.faq-question');
            var isActive = $item.hasClass('active');
            
            $question.attr('aria-expanded', isActive ? 'true' : 'false');
        });
    }
    
    /**
     * 特定のFAQ項目を開く関数(外部から呼び出し可能)
     * @param {number} index - 開くFAQ項目のインデックス(0から開始)
     */
    window.openFaqItem = function(index) {
        var $faqItem = $('.faq-item').eq(index);
        if ($faqItem.length && !$faqItem.hasClass('active')) {
            $faqItem.find('.faq-question').trigger('click');
        }
    };
    
    /**
     * すべてのFAQ項目を閉じる関数(外部から呼び出し可能)
     */
    window.closeAllFaqItems = function() {
        $('.faq-item.active .faq-question').trigger('click');
    };
    
    /**
     * 初期化処理
     */
    $(document).ready(function() {
        initFaqAccordion();
        
        // MutationObserverでDOMの変更を監視(動的に追加されたFAQにも対応)
        if (window.MutationObserver) {
            var observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.addedNodes.length) {
                        var $addedFaq = $(mutation.addedNodes).find('.faq-item');
                        if ($addedFaq.length) {
                            initFaqAccordion();
                        }
                    }
                });
            });
            
            var targetNode = document.querySelector('.faq-section');
            if (targetNode) {
                observer.observe(targetNode, {
                    childList: true,
                    subtree: true
                });
            }
        }
    });
    
    // aria-expanded属性の更新を監視
    $(document).on('click', '.faq-question', function() {
        setTimeout(updateAriaExpanded, 100);
    });
    
})(jQuery);
