import { TooltipProvider, tooltipFactory } from '@milkdown/kit/plugin/tooltip'
import type { EditorState, PluginView } from '@milkdown/kit/prose/state'
import { TextSelection } from '@milkdown/kit/prose/state'
import type { Ctx } from '@milkdown/kit/ctx'
import type { EditorView } from '@milkdown/kit/prose/view'
import type { AtomicoThis } from 'atomico/types/dom'
import type { DefineFeature, Icon } from '../shared'
import { defIfNotExists } from '../../utils'
import type { ToolbarProps } from './component'
import { ToolbarElement } from './component'

interface ToolbarConfig {
  boldIcon: Icon
  codeIcon: Icon
  italicIcon: Icon
  linkIcon: Icon
  strikethroughIcon: Icon
}

export type ToolbarFeatureConfig = Partial<ToolbarConfig>

const toolbar = tooltipFactory('CREPE_TOOLBAR')

// Register the custom element before using it
defIfNotExists('milkdown-toolbar', ToolbarElement)

class ToolbarView implements PluginView {
  #tooltipProvider: TooltipProvider
  #content: AtomicoThis<ToolbarProps>
  constructor(ctx: Ctx, view: EditorView, config?: ToolbarFeatureConfig) {
    // Use document.createElement to ensure the custom element is properly registered
    const content = document.createElement('milkdown-toolbar') as AtomicoThis<ToolbarProps>
    this.#content = content
    this.#content.ctx = ctx
    this.#content.hide = this.hide
    this.#content.config = config

    this.#tooltipProvider = new TooltipProvider({
      content: this.#content,
      debounce: 0, // No debounce for immediate response to keyboard selections
      offset: 10,
      shouldShow(view: EditorView) {
        const { doc, selection } = view.state
        const { empty, from, to } = selection

        const isEmptyTextBlock = !doc.textBetween(from, to).length && selection instanceof TextSelection

        const isNotTextBlock = !(selection instanceof TextSelection)

        const activeElement = (view.dom.getRootNode() as ShadowRoot | Document).activeElement
        const isTooltipChildren = content.contains(activeElement)

        const isReadonly = !view.editable

        // Don't show if readonly, not a text block, or empty selection
        if (
          isNotTextBlock
          || empty
          || isEmptyTextBlock
          || isReadonly
        )
          return false

        // Check if there's actual text selected (works for both mouse and keyboard selections)
        const selectedText = doc.textBetween(from, to)
        const hasTextSelection = from !== to && selectedText.trim().length > 0
        
        if (hasTextSelection && selection instanceof TextSelection) {
          // Show toolbar if text is selected, regardless of focus state
          // This handles Command+A and other keyboard selections
          return true
        }

        return false
      },
    })
    this.#tooltipProvider.onShow = () => {
      this.#content.show = true
      setTimeout(() => {
        const leftStr = this.#content.style.left || '0';
        // convert left value to number (remove 'px' if present)
        const leftNum = parseInt(leftStr.replace('px', '')) || 0;
        if (leftNum < 0) {
          // set left value to 0
          this.#content.style.left = '0px';
        } else if (leftNum > 350) {
          this.#content.style.left = '350px';
        }
      }, 0);
    }
    this.#tooltipProvider.onHide = () => {
      this.#content.show = false
    }
    this.update(view)
  }

  update = (view: EditorView, prevState?: EditorState) => {
    // Force immediate update to handle keyboard selections like Cmd+A
    this.#tooltipProvider.update(view, prevState)
    // Also trigger a check after a brief delay to catch rapid selection changes
    setTimeout(() => {
      this.#tooltipProvider.update(view, prevState)
    }, 10)
  }

  destroy = () => {
    this.#tooltipProvider.destroy()
    this.#content.remove()
  }

  hide = () => {
    this.#tooltipProvider.hide()
  }
}

export const defineFeature: DefineFeature<ToolbarFeatureConfig> = (editor, config) => {
  editor
    .config((ctx) => {
      ctx.set(toolbar.key, {
        view: view => new ToolbarView(ctx, view, config),
      })
    })
    .use(toolbar)
}
