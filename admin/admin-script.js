/**
 * RIFFテーマ管理画面JavaScript - スライド機能修正版
 */

(function($) {
    'use strict';
    
    let slideIndex = 0;
    
    // ページ読み込み時の初期化
    $(document).ready(function() {
        console.log('=== RIFF Admin Script 読み込み完了 ===');
        
        // カラーピッカーの初期化(ヘッダー設定ページ用)
        if (typeof $.fn.wpColorPicker !== 'undefined') {
            $('.riff-color-picker').wpColorPicker();
            console.log('カラーピッカーを初期化しました');
        }
        
        // ヒーロー設定ページかどうか確認
        if ($('#slides-container').length > 0) {
            console.log('ヒーロー設定ページを検出しました');
            initHeroSettings();
        }
        
        // ロゴタイプによる表示切り替え(ヘッダー設定ページ用)
        if ($('#logo_type').length > 0) {
            toggleLogoFields();
            $('#logo_type').on('change', toggleLogoFields);
        }
        
        // ロゴ幅・高さ変更時のプレビュー更新
        $('#logo_width, #logo_height').on('change input', updateLogoPreview);
        
        // カスタムCSS機能の初期化
        initCustomCSS();
    });
    
    // ===================================
    // カスタムCSS機能の初期化
    // ===================================
    function initCustomCSS() {
        // カスタムCSSメタボックスが存在するか確認
        if ($('#riff_custom_css').length === 0) {
            return;
        }
        
        console.log('カスタムCSS機能を初期化中...');
        
        const $checkbox = $('input[name="riff_custom_css_enabled"]');
        const $editor = $('.riff-custom-css-editor');
        const $tips = $('.riff-custom-css-tips');
        const $metabox = $('#riff_custom_css');
        
        // メタボックスを常に開いた状態にする
        $metabox.removeClass('closed');
        
        // メタボックスのトグルボタンを監視し、閉じられないようにする
        $metabox.find('.postbox-header').on('click', function(e) {
            setTimeout(function() {
                $metabox.removeClass('closed');
            }, 10);
        });
        
        // 初期状態の設定
        function toggleCSSEditor() {
            if ($checkbox.is(':checked')) {
                $editor.slideDown(300);
                $tips.slideDown(300);
            } else {
                $editor.slideUp(300);
                $tips.slideUp(300);
            }
        }
        
        // ページロード時に状態を反映
        toggleCSSEditor();
        
        // チェックボックスの変更を監視
        $checkbox.on('change', toggleCSSEditor);
        
        // CodeMirrorのカスタマイズ
        setTimeout(function() {
            if (typeof wp.codeEditor !== 'undefined' && $('.CodeMirror').length > 0) {
                console.log('CodeMirrorエディタが初期化されました');
                $('.CodeMirror').css('height', '400px');
            }
        }, 500);
        
        // 表示オプションの変更を監視し、常に表示されるようにする
        setInterval(function() {
            const $hiddenCheckbox = $('#riff_custom_css-hide');
            if ($hiddenCheckbox.length > 0 && $hiddenCheckbox.is(':checked')) {
                $hiddenCheckbox.prop('checked', false);
                $metabox.show();
            }
        }, 1000);
    }
    
    // ===================================
    // ヒーロー設定の初期化
    // ===================================
    function initHeroSettings() {
        console.log('ヒーロー設定を初期化中...');
        
        // 既存のスライド数を取得
        const existingSlides = $('.riff-slide-item');
        slideIndex = existingSlides.length;
        console.log('既存のスライド数:', slideIndex);
        
        // 最初のスライドを開く
        if (existingSlides.length > 0) {
            existingSlides.first().addClass('active');
            console.log('最初のスライドをアクティブにしました');
        }
        
        // イベントハンドラを設定
        setupEventHandlers();
        
        // スライドのソート機能
        if ($.fn.sortable) {
            $('#slides-container').sortable({
                handle: '.riff-slide-handle',
                placeholder: 'ui-sortable-placeholder',
                cursor: 'move',
                opacity: 0.8,
                update: function() {
                    updateSlideNumbers();
                }
            });
            console.log('ソート機能を有効化しました');
        }
        
        // フォーム送信時のデバッグ
        $('#riff-hero-form').on('submit', function() {
            const slideCount = $('.riff-slide-item').length;
            console.log('=== フォーム送信 ===');
            console.log('スライド数:', slideCount);
            return true;
        });
    }
    
    // ===================================
    // イベントハンドラの設定
    // ===================================
    function setupEventHandlers() {
        console.log('イベントハンドラを設定中...');
        
        // スライド追加ボタン - 直接バインド
        $('#add-slide').off('click').on('click', function(e) {
            e.preventDefault();
            console.log('スライド追加ボタンがクリックされました');
            addSlide();
        });
        
        // スライドトグル - イベント委譲
        $(document).off('click', '.riff-slide-toggle').on('click', '.riff-slide-toggle', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('スライドトグルがクリックされました');
            const $slide = $(this).closest('.riff-slide-item');
            $slide.toggleClass('active');
            console.log('スライドのactive状態:', $slide.hasClass('active'));
        });
        
        // スライド削除 - イベント委譲
        $(document).off('click', '.riff-slide-remove').on('click', '.riff-slide-remove', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('スライド削除がクリックされました');
            if (confirm('このスライドを削除してもよろしいですか?')) {
                $(this).closest('.riff-slide-item').fadeOut(300, function() {
                    $(this).remove();
                    updateSlideNumbers();
                    console.log('スライドを削除しました');
                });
            }
        });
        
        // 背景画像アップロード - イベント委譲
        $(document).off('click', '.riff-upload-image').on('click', '.riff-upload-image', function(e) {
            e.preventDefault();
            console.log('背景画像アップロードがクリックされました');
            uploadImage($(this), '.riff-image-url', '.riff-image-preview', '背景画像を選択');
        });
        
        // 背景画像削除 - イベント委譲
        $(document).off('click', '.riff-remove-image').on('click', '.riff-remove-image', function(e) {
            e.preventDefault();
            console.log('背景画像削除がクリックされました');
            removeImage($(this), '.riff-image-url', '.riff-image-preview');
        });
        
        // コンテンツ画像アップロード - イベント委譲
        $(document).off('click', '.riff-upload-content-image').on('click', '.riff-upload-content-image', function(e) {
            e.preventDefault();
            console.log('コンテンツ画像アップロードがクリックされました');
            uploadImage($(this), '.riff-content-image-url', '.riff-content-image-preview', 'コンテンツ画像を選択');
        });
        
        // コンテンツ画像削除 - イベント委譲
        $(document).off('click', '.riff-remove-content-image').on('click', '.riff-remove-content-image', function(e) {
            e.preventDefault();
            console.log('コンテンツ画像削除がクリックされました');
            removeImage($(this), '.riff-content-image-url', '.riff-content-image-preview');
        });
        
        // ロゴ画像アップロード - 直接バインド
        $('.riff-upload-logo').off('click').on('click', function(e) {
            e.preventDefault();
            console.log('ロゴ画像アップロードがクリックされました');
            uploadLogo();
        });
        
        // ロゴ画像削除 - 直接バインド
        $('.riff-remove-logo').off('click').on('click', function(e) {
            e.preventDefault();
            if (confirm('ロゴ画像を削除してもよろしいですか?')) {
                $('#logo_image').val('');
                $('.riff-logo-preview').empty();
                console.log('ロゴ画像を削除しました');
            }
        });
        
        console.log('イベントハンドラの設定完了');
    }
    
    // ===================================
    // スライド追加
    // ===================================
    function addSlide() {
        console.log('addSlide() 実行開始');
        console.log('現在のslideIndex:', slideIndex);
        
        const templateElement = $('#slide-template');
        console.log('テンプレート要素:', templateElement.length);
        
        if (templateElement.length === 0) {
            console.error('テンプレート要素が見つかりません');
            alert('エラー: スライドテンプレートが見つかりません。ページをリロードしてください。');
            return;
        }
        
        const template = templateElement.html();
        console.log('テンプレートHTML取得:', template ? '成功(' + template.length + '文字)' : '失敗');
        
        if (!template || template.trim() === '') {
            console.error('テンプレートが空です');
            alert('エラー: スライドテンプレートが空です。ページをリロードしてください。');
            return;
        }
        
        // {{INDEX}}を現在のインデックスに置換
        const newSlide = template.replace(/\{\{INDEX\}\}/g, slideIndex);
        console.log('新しいスライドHTML生成完了');
        
        // スライドコンテナに追加
        const $container = $('#slides-container');
        
        // 空のメッセージを削除
        $container.find('p.description').remove();
        $container.find('.description').remove();
        
        // 新しいスライドを追加
        $container.append(newSlide);
        console.log('スライドをコンテナに追加しました');
        
        // 新しく追加したスライドを取得
        const $newSlide = $('.riff-slide-item').last();
        
        // すべてのスライドを閉じる
        $('.riff-slide-item').removeClass('active');
        
        // 新しいスライドを開く
        $newSlide.addClass('active');
        console.log('新しいスライドをアクティブにしました');
        
        // インデックスを増やす
        slideIndex++;
        console.log('slideIndexを更新:', slideIndex);
        
        // スライド番号を更新
        updateSlideNumbers();
        
        // 新しいスライドまでスクロール
        $('html, body').animate({
            scrollTop: $newSlide.offset().top - 100
        }, 500);
        
        console.log('addSlide() 実行完了');
    }
    
    // ===================================
    // スライド番号の更新
    // ===================================
    function updateSlideNumbers() {
        console.log('スライド番号を更新中...');
        
        $('.riff-slide-item').each(function(index) {
            const $slide = $(this);
            $slide.attr('data-index', index);
            $slide.find('.riff-slide-header h3').text('スライド ' + (index + 1));
            
            // input nameを更新
            $slide.find('input, textarea, select').each(function() {
                const $input = $(this);
                const name = $input.attr('name');
                if (name && name.includes('slides[')) {
                    const newName = name.replace(/slides\[\d+\]/, 'slides[' + index + ']');
                    $input.attr('name', newName);
                }
            });
        });
        
        // スライド数を更新
        const slideCount = $('.riff-slide-item').length;
        $('#current-slide-count').text(slideCount);
        
        console.log('スライド番号の更新完了。合計: ' + slideCount + '個');
    }
    
    // ===================================
    // 画像アップロード
    // ===================================
    function uploadImage(button, urlInputClass, previewClass, title) {
        console.log('uploadImage() 実行:', title);
        
        const $container = button.closest('.riff-image-upload');
        const $imageUrlInput = $container.find(urlInputClass);
        const $imagePreview = $container.find(previewClass);
        
        console.log('コンテナ:', $container.length);
        console.log('URL入力:', $imageUrlInput.length);
        console.log('プレビュー:', $imagePreview.length);
        
        if (!wp || !wp.media) {
            console.error('wp.mediaが利用できません');
            alert('メディアライブラリが利用できません。');
            return;
        }
        
        const customUploader = wp.media({
            title: title,
            button: {
                text: '画像を使用'
            },
            multiple: false,
            library: {
                type: 'image'
            }
        });
        
        customUploader.on('select', function() {
            const attachment = customUploader.state().get('selection').first().toJSON();
            console.log('画像が選択されました:', attachment.url);
            
            $imageUrlInput.val(attachment.url);
            $imagePreview.html('<img src="' + attachment.url + '" alt="選択された画像">');
        });
        
        customUploader.open();
    }
    
    // ===================================
    // 画像削除
    // ===================================
    function removeImage(button, urlInputClass, previewClass) {
        console.log('removeImage() 実行');
        
        const $container = button.closest('.riff-image-upload');
        const $imageUrlInput = $container.find(urlInputClass);
        const $imagePreview = $container.find(previewClass);
        
        $imageUrlInput.val('');
        $imagePreview.empty();
        
        console.log('画像が削除されました');
    }
    
    // ===================================
    // ロゴ画像アップロード
    // ===================================
    function uploadLogo() {
        console.log('uploadLogo() 実行');
        
        const $imageUrlInput = $('#logo_image');
        const $imagePreview = $('.riff-logo-preview');
        const logoWidth = $('#logo_width').val() || 200;
        const logoHeight = $('#logo_height').val() || 60;
        
        if (!wp || !wp.media) {
            console.error('wp.mediaが利用できません');
            alert('メディアライブラリが利用できません。');
            return;
        }
        
        const customUploader = wp.media({
            title: 'ロゴ画像を選択',
            button: {
                text: 'この画像を使用'
            },
            multiple: false,
            library: {
                type: 'image'
            }
        });
        
        customUploader.on('select', function() {
            const attachment = customUploader.state().get('selection').first().toJSON();
            console.log('ロゴ画像が選択されました:', attachment.url);
            
            $imageUrlInput.val(attachment.url);
            $imagePreview.html('<img src="' + attachment.url + '" alt="ロゴ画像" style="max-width: ' + logoWidth + 'px; max-height: ' + logoHeight + 'px;">');
        });
        
        customUploader.open();
    }
    
    // ===================================
    // ロゴプレビュー更新
    // ===================================
    function updateLogoPreview() {
        const logoWidth = $('#logo_width').val();
        const logoHeight = $('#logo_height').val();
        
        $('.riff-logo-preview img').css({
            'max-width': logoWidth + 'px',
            'max-height': logoHeight + 'px'
        });
        
        console.log('ロゴプレビューを更新:', logoWidth + 'x' + logoHeight);
    }
    
    // ===================================
    // ロゴタイプによる表示切り替え
    // ===================================
    function toggleLogoFields() {
        const logoType = $('#logo_type').val();
        console.log('ロゴタイプ:', logoType);
        
        if (logoType === 'text') {
            $('.logo-text-row').show();
            $('.logo-image-row').hide();
        } else {
            $('.logo-text-row').hide();
            $('.logo-image-row').show();
        }
    }
    
})(jQuery);
// ==============================================
// フッター設定ページのカラーピッカー
// ==============================================
$(document).ready(function() {
    // フッター設定ページのカラーピッカー初期化
    if ($('.riff-color-picker').length) {
        $('.riff-color-picker').wpColorPicker();
    }
});