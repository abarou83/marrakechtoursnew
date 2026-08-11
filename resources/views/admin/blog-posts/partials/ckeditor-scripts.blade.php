@push('styles')
    <style>
        .blog-content-editor .ck-editor__editable {
            min-height: 420px;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const editors = [];

            document.querySelectorAll('.js-blog-content-editor').forEach((textarea) => {
                ClassicEditor.create(textarea).then((editor) => {
                    editors.push(editor);
                }).catch((error) => {
                    console.error('CKEditor init error:', error);
                });
            });

            const form = document.querySelector('form[action*="blog-posts"]');
            if (form) {
                form.addEventListener('submit', function () {
                    editors.forEach((editor) => editor.updateSourceElement());
                });
            }
        });
    </script>
@endpush
