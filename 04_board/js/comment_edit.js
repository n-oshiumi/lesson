$(function() {
    $('.comment-edit').on('click', function() {
        // 編集可能にする
        const comment_content = $(this).parent().prev("form").find('.comment-content');
        comment_content.attr("readonly", false);
        comment_content.addClass('editable');

        // コメント更新ボタンを表示

    })
})