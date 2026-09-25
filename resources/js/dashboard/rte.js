import { Editor } from '@tiptap/core';
import StarterKit from '@tiptap/starter-kit';
import TextAlign from '@tiptap/extension-text-align';
import { Placeholder } from '@tiptap/extensions';

/**
 * Minimal rich-text editor bound to an Alpine value.
 *   <div x-data="rte(() => block.data.html.ar, v => block.data.html.ar = v, 'rtl')">
 * The Tiptap instance is kept out of Alpine's reactivity (closure variable).
 */
export default function rte(getter, setter, dir = 'rtl', placeholder = '') {
    let editor = null;

    return {
        active: {},

        init() {
            editor = new Editor({
                element: this.$refs.editor,
                extensions: [
                    StarterKit.configure({
                        heading: { levels: [2, 3] },
                        codeBlock: false,
                        code: false,
                        horizontalRule: false,
                        link: { openOnClick: false, autolink: true, HTMLAttributes: { rel: 'noopener noreferrer' } },
                    }),
                    TextAlign.configure({ types: ['heading', 'paragraph'], alignments: ['left', 'center', 'right', 'justify'] }),
                    Placeholder.configure({ placeholder }),
                ],
                content: getter() || '',
                editorProps: { attributes: { dir, class: 'prose-editor' } },
                onUpdate: ({ editor }) => {
                    setter(editor.isEmpty ? '' : editor.getHTML());
                },
                onTransaction: () => this.refresh(),
            });

            // Keep in sync when the value is replaced from outside (undo / locale copy).
            this.$watch(getter, (value) => {
                if (editor && value !== editor.getHTML() && !(editor.isEmpty && !value)) {
                    editor.commands.setContent(value || '', { emitUpdate: false });
                }
            });
        },

        destroy() {
            editor?.destroy();
            editor = null;
        },

        refresh() {
            if (!editor) return;
            this.active = {
                bold: editor.isActive('bold'),
                italic: editor.isActive('italic'),
                underline: editor.isActive('underline'),
                strike: editor.isActive('strike'),
                h2: editor.isActive('heading', { level: 2 }),
                h3: editor.isActive('heading', { level: 3 }),
                bullet: editor.isActive('bulletList'),
                ordered: editor.isActive('orderedList'),
                quote: editor.isActive('blockquote'),
                link: editor.isActive('link'),
                left: editor.isActive({ textAlign: 'left' }),
                center: editor.isActive({ textAlign: 'center' }),
                right: editor.isActive({ textAlign: 'right' }),
            };
        },

        cmd(name, ...args) {
            const chain = editor.chain().focus();
            chain[name](...args).run();
        },

        link() {
            const previous = editor.getAttributes('link').href || '';
            const url = window.prompt('URL', previous);
            if (url === null) return;
            if (url === '') {
                editor.chain().focus().extendMarkRange('link').unsetLink().run();
                return;
            }
            editor.chain().focus().extendMarkRange('link').setLink({ href: url, target: /^https?:/.test(url) ? '_blank' : null }).run();
        },
    };
}
