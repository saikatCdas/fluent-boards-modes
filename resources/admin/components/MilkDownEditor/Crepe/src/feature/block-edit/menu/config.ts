import { editorViewCtx } from '@milkdown/kit/core'
import {
  blockquoteSchema,
  bulletListSchema,
  codeBlockSchema,
  headingSchema,
  orderedListSchema,
  paragraphSchema,
} from '@milkdown/kit/preset/commonmark'

import {
  bulletListIcon,
  codeIcon,
  h2Icon,
  h3Icon,
  h4Icon,
  orderedListIcon,
  quoteIcon,
  textIcon,
} from '../../../icons'
import type { BlockEditFeatureConfig } from '../index'
import type { MenuItemGroup } from './utils'
import {
  clearContentAndAddBlockType,
  clearContentAndSetBlockType,
  clearContentAndWrapInBlockType,
} from './utils'
import { GroupBuilder } from './group-builder'

function $t(str) {
  // Check for fluent-boards-modes i18n
  const fluentBoardsModes = (window as any).fluentBoardsModes;
  if (fluentBoardsModes && fluentBoardsModes.i18n) {
    return fluentBoardsModes.i18n[str] || str;
  }
  // Fallback to the string itself
  return str;
}

export function getGroups(filter?: string, config?: BlockEditFeatureConfig) {
  const groupBuilder = new GroupBuilder()
  groupBuilder
    .addGroup('text', config?.slashMenuTextGroupLabel ?? $t('Text') )
    .addItem('text', {
      label: config?.slashMenuTextGroupLabel ?? $t('Text'),
      icon: config?.slashMenuTextIcon?.() ?? textIcon,
      onRun: (ctx) => {
        const view = ctx.get(editorViewCtx)
        const { dispatch, state } = view

        const command = clearContentAndSetBlockType(paragraphSchema.type(ctx))
        command(state, dispatch)
      },
    })
    .addItem('h2', {
      label: config?.slashMenuH2Label ?? $t('Heading 2'),
      icon: config?.slashMenuH2Icon?.() ?? h2Icon,
      onRun: (ctx) => {
        const view = ctx.get(editorViewCtx)
        const { dispatch, state } = view

        const command = clearContentAndSetBlockType(headingSchema.type(ctx), { level: 2 })
        command(state, dispatch)
      },
    })
    .addItem('h3', {
      label: config?.slashMenuH3Label ?? $t('Heading 3'),
      icon: config?.slashMenuH3Icon?.() ?? h3Icon,
      onRun: (ctx) => {
        const view = ctx.get(editorViewCtx)
        const { dispatch, state } = view

        const command = clearContentAndSetBlockType(headingSchema.type(ctx), { level: 3 })
        command(state, dispatch)
      },
    })
    .addItem('h4', {
      label: config?.slashMenuH4Label ?? $t('Heading 4'),
      icon: config?.slashMenuH4Icon?.() ?? h4Icon,
      onRun: (ctx) => {
        const view = ctx.get(editorViewCtx)
        const { dispatch, state } = view

        const command = clearContentAndSetBlockType(headingSchema.type(ctx), { level: 4 })
        command(state, dispatch)
      },
    })
    .addItem('quote', {
      label: config?.slashMenuQuoteLabel ?? $t('Quote'),
      icon: config?.slashMenuQuoteIcon?.() ?? quoteIcon,
      onRun: (ctx) => {
        const view = ctx.get(editorViewCtx)
        const { dispatch, state } = view

        const command = clearContentAndWrapInBlockType(blockquoteSchema.type(ctx))
        command(state, dispatch)
      },
    })
    .addItem('code', {
      label: config?.slashMenuCodeBlockLabel ?? $t('Code'),
      icon: config?.slashMenuCodeBlockIcon?.() ?? codeIcon,
      onRun: (ctx) => {
        const view = ctx.get(editorViewCtx)
        const { dispatch, state } = view

        const command = clearContentAndAddBlockType(codeBlockSchema.type(ctx))
        command(state, dispatch)
      },
    });

  groupBuilder.addGroup('list', config?.slashMenuListGroupLabel ?? $t('List'))
    .addItem('bullet-list', {
      label: config?.slashMenuBulletListLabel ?? $t('Bullet List'),
      icon: config?.slashMenuBulletListIcon?.() ?? bulletListIcon,
      onRun: (ctx) => {
        const view = ctx.get(editorViewCtx)
        const { dispatch, state } = view

        const command = clearContentAndWrapInBlockType(bulletListSchema.type(ctx))
        command(state, dispatch)
      },
    })
    .addItem('ordered-list', {
      label: config?.slashMenuOrderedListLabel ?? $t('Ordered List'),
      icon: config?.slashMenuOrderedListIcon?.() ?? orderedListIcon,
      onRun: (ctx) => {
        const view = ctx.get(editorViewCtx)
        const { dispatch, state } = view

        const command = clearContentAndWrapInBlockType(orderedListSchema.type(ctx))
        command(state, dispatch)
      },
    });

  config?.buildMenu?.(groupBuilder)

  let groups = groupBuilder.build()

  if (filter) {
    groups = groups
      .map((group) => {
        const items = group
          .items
          .filter(item =>
            item
              .label
              .toLowerCase()
              .includes(filter.toLowerCase()))

        return {
          ...group,
          items,
        }
      })
      .filter(group => group.items.length > 0)
  }

  const items = groups.flatMap(groups => groups.items)
  items.forEach(((item, index) => {
    Object.assign(item, { index })
  }))

  groups.reduce((acc, group) => {
    const end = acc + group.items.length
    Object.assign(group, {
      range: [acc, end],
    })
    return end
  }, 0)

  return {
    groups: groups as MenuItemGroup[],
    size: items.length,
  }
}
