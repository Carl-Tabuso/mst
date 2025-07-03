import { Extension } from '@tiptap/core'

export interface FontFamilyOptions {
  types: string[]
}

declare module '@tiptap/core' {
  interface Commands<ReturnType> {
    fontFamily: {
      setFontFamily: (font: string) => ReturnType
    }
  }
}

export const FontFamily = Extension.create<FontFamilyOptions>({
  name: 'fontFamily',

  addOptions() {
    return {
      types: ['textStyle'],
    }
  },

  addGlobalAttributes() {
    return [
      {
        types: this.options.types,
        attributes: {
          style: {
            default: null,
            renderHTML: (attributes) => {
              if (!attributes.style?.includes('font-family')) return {}
              return {
                style: attributes.style,
              }
            },
            parseHTML: (element) => {
              return {
                style: element.getAttribute('style'),
              }
            },
          },
        },
      },
    ]
  },

  addCommands() {
    return {
      setFontFamily:
        (font: string) =>
        ({ commands }) => {
          return commands.setMark('textStyle', {
            style: `font-family: ${font}`,
          })
        },
    }
  },
})
