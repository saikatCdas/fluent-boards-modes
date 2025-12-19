import type { Component } from 'atomico'
import { c, html, useEffect, useUpdate, useState } from 'atomico'
import type { Ctx } from '@milkdown/kit/ctx'
import { commandsCtx, editorViewCtx } from '@milkdown/kit/core'
import {
  emphasisSchema,
  inlineCodeSchema,
  linkSchema,
  strongSchema,
  toggleEmphasisCommand,
  toggleInlineCodeCommand,
  toggleStrongCommand,
} from '@milkdown/kit/preset/commonmark'
import type { MarkType } from '@milkdown/kit/prose/model'
import clsx from 'clsx'
// LinkTooltip feature is disabled, we handle links directly
import { strikethroughSchema, toggleStrikethroughCommand } from '@milkdown/kit/preset/gfm'
import { boldIcon, codeIcon, italicIcon, linkIcon, strikethroughIcon, confirmIcon } from '../../icons'
import type { ToolbarFeatureConfig } from './index'

export interface ToolbarProps {
  ctx: Ctx
  hide: () => void
  show: boolean
  config?: ToolbarFeatureConfig
}

export const toolbarComponent: Component<ToolbarProps> = ({
  ctx,
  hide,
  show,
  config,
}) => {
  const update = useUpdate()
  const [showLinkInput, setShowLinkInput] = useState(false)
  const [linkUrl, setLinkUrl] = useState('')

  useEffect(() => {
    update()
  }, [show, showLinkInput])

  const onClick = (fn: (ctx: Ctx) => void) => (e: MouseEvent) => {
    e.preventDefault()
    ctx && fn(ctx)
    update()
  }

  const isActive = (mark: MarkType) => {
    if (!ctx)
      return false
    const view = ctx.get(editorViewCtx)
    const { state: { doc, selection } } = view
    return doc.rangeHasMark(selection.from, selection.to, mark)
  }

  const handleLinkClick = (e: MouseEvent) => {
    e.preventDefault()
    if (!ctx) return
    
    const view = ctx.get(editorViewCtx)
    const { selection, tr } = view.state

    if (isActive(linkSchema.type(ctx))) {
      // Remove link mark directly using ProseMirror
      const linkMark = linkSchema.type(ctx)
      if (linkMark) {
        const newTr = tr.removeMark(selection.from, selection.to, linkMark)
        view.dispatch(newTr)
      }
      setShowLinkInput(false)
      setLinkUrl('')
      update()
      return
    }

    // Show link input in toolbar
    setShowLinkInput(true)
    setLinkUrl('')
    update()
  }

  const handleConfirmLink = (e: MouseEvent) => {
    e.preventDefault()
    if (!ctx || !linkUrl.trim()) return

    const view = ctx.get(editorViewCtx)
    const { selection, tr } = view.state
    const url = linkUrl.trim()

    // Create link mark with href attribute
    const linkMark = linkSchema.type(ctx)
    if (linkMark) {
      const linkAttrs = { href: url }
      const newTr = tr.addMark(selection.from, selection.to, linkMark.create(linkAttrs))
      view.dispatch(newTr)
    }
    
    setShowLinkInput(false)
    setLinkUrl('')
    update()
    hide?.()
  }

  const handleCancelLink = (e: MouseEvent) => {
    e.preventDefault()
    setShowLinkInput(false)
    setLinkUrl('')
    update()
  }

  return html`<host data-show=${show}>
    ${!showLinkInput ? html`
      <button
        class=${clsx('toolbar-item', ctx && isActive(strongSchema.type(ctx)) && 'active')}
        onmousedown=${onClick((ctx) => {
          const commands = ctx.get(commandsCtx)
          commands.call(toggleStrongCommand.key)
        })}
      >
        ${config?.boldIcon?.() ?? boldIcon}
      </button>
      <button
        class=${clsx('toolbar-item', ctx && isActive(emphasisSchema.type(ctx)) && 'active')}
        onmousedown=${onClick((ctx) => {
          const commands = ctx.get(commandsCtx)
          commands.call(toggleEmphasisCommand.key)
        })}
      >
        ${config?.italicIcon?.() ?? italicIcon}
      </button>
      <button
        class=${clsx('toolbar-item', ctx && isActive(strikethroughSchema.type(ctx)) && 'active')}
        onmousedown=${onClick((ctx) => {
          const commands = ctx.get(commandsCtx)
          commands.call(toggleStrikethroughCommand.key)
        })}
      >
        ${config?.strikethroughIcon?.() ?? strikethroughIcon}
      </button>
      <div class="divider"></div>
      <button
        class=${clsx('toolbar-item', ctx && isActive(inlineCodeSchema.type(ctx)) && 'active')}
        onmousedown=${onClick((ctx) => {
          const commands = ctx.get(commandsCtx)
          commands.call(toggleInlineCodeCommand.key)
        })}
      >
        ${config?.codeIcon?.() ?? codeIcon}
      </button>
      <button
        class=${clsx('toolbar-item', ctx && isActive(linkSchema.type(ctx)) && 'active')}
        onmousedown=${handleLinkClick}
      >
        ${config?.linkIcon?.() ?? linkIcon}
      </button>
    ` : html`
      <div class="link-input-container">
        <input
          type="text"
          class="link-input"
          placeholder="Paste link..."
          value=${linkUrl}
          autofocus
          oninput=${(e: Event) => {
            const target = e.target as HTMLInputElement
            setLinkUrl(target.value)
            update()
          }}
          onkeydown=${(e: KeyboardEvent) => {
            if (e.key === 'Enter') {
              handleConfirmLink(e as any)
            } else if (e.key === 'Escape') {
              handleCancelLink(e as any)
            }
          }}
        />
        <button
          class="toolbar-item confirm-link"
          onmousedown=${handleConfirmLink}
          disabled=${!linkUrl.trim()}
        >
          ${config?.confirmIcon?.() ?? confirmIcon}
        </button>
      </div>
    `}
  </host>`
}

toolbarComponent.props = {
  ctx: Object,
  hide: Function,
  show: Boolean,
  config: Object,
}

export const ToolbarElement = c(toolbarComponent)
