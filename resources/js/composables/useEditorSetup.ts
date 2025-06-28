import { ref } from 'vue'
import { useEditor, EditorContent } from '@tiptap/vue-3'
import StarterKit from '@tiptap/starter-kit'
import { FontSize } from  '../components/incident-report/components/extensions/FontSize'
import { FontFamily } from '../components/incident-report/components/extensions/FontFamily'
import Underline from '@tiptap/extension-underline'
import Link from '@tiptap/extension-link'
import TextAlign from '@tiptap/extension-text-align'
import Color from '@tiptap/extension-color'
import TextStyle from '@tiptap/extension-text-style'
import Highlight from '@tiptap/extension-highlight'
import Placeholder from '@tiptap/extension-placeholder'
import Image from '@tiptap/extension-image'
import CodeBlock from '@tiptap/extension-code-block'
import Blockquote from '@tiptap/extension-blockquote'
import HorizontalRule from '@tiptap/extension-horizontal-rule'
import Superscript from '@tiptap/extension-superscript'
import Subscript from '@tiptap/extension-subscript'

export function useEditorSetup(initialContent: string, onUpdate: (html: string) => void) {
  const showColorDropdown = ref(false)
  const showFontSizeDropdown = ref(false)

  const textColors = ['#000000', '#FF0000', '#0070f3', '#10B981']
  const fontSizes = ['12', '14', '16', '18', '24', '32', '48']

  const editor = useEditor({
    content: initialContent,
    extensions: [
      StarterKit,
      FontSize,
      FontFamily,
      Underline,
      Link.configure({ openOnClick: false }),
      TextStyle,
      Color,
      TextAlign.configure({ types: ['heading', 'paragraph'] }),
      Highlight,
      Placeholder.configure({ placeholder: 'Describe the infraction here...' }),
      Image,
      CodeBlock,
      Blockquote,
      HorizontalRule,
      Superscript,
      Subscript,
    ],
    onUpdate: ({ editor }) => {
      onUpdate(editor.getHTML())
    },
  })

  return {
    editor,
    showColorDropdown,
    showFontSizeDropdown,
    textColors,
    fontSizes
  }
}